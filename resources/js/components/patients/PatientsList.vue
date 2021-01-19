<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Utentes
                    </h3>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="patientsTable" class="table-wrapper table table-hover dt-responsive w-100">
                        <thead>
                        <tr>
                            <th v-for="title in titles" :class="title.className">
                                {{ title.label }}
                            </th>
                        </tr>
                        </thead>
                        <tbody/>
                    </table>
                </div>
            </div>
        </div>
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            title="Eliminar Utente"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deletePatient"
            message="Tem a certeza que deseja eliminar o utente selecionado?"
        />
        <ChatResponseModal
            :message="null"
            :user="selectedRow"
            v-show="showReplyModal"
            @close="() => { this.showReplyModal = false }"
            @save="onNewResponseClick"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import { getCategoryNameById, parseDateToString, renderGender } from '../../utils/misc';
import { COLUMN_NAME } from '../../utils/table_elements';
import { ROUTE } from '../../utils/routes';
import ConfirmationModal from '../modals/ConfirmationModal';
import {
    EmptyObject,
    initDataTable,
    onClickHandler,
    redrawTable,
    TableActionClasses,
    TableActionColumns
} from '../../utils/dataTables';
import { UserRoles } from '../../constants/misc';
import ChatResponseModal from '../modals/ChatResponseModal';

export default {
    data() {
        return {
            showConfirmationModal: false,
            isFetching: false,
            data: [],
            dataTable: null,
            originalData: [],
            selectedUserId: null,
            selectedRow: null,
            showReplyModal: false,
            ufcs: [],
            selectedUsf: 'ALL',
            titles: [{
                label: COLUMN_NAME.Name,
                className: '',
            }, {
                label: COLUMN_NAME.Gender,
                className: '',
            }, {
                label: COLUMN_NAME.Birthday,
                className: '',
            }, {
                label: COLUMN_NAME.Usf,
                className: '',
            }, {
                label: COLUMN_NAME.Email,
                className: '',
            }, EmptyObject],
            columns: [
                {...TableActionColumns.View, responsivePriority: 1},
                {data: 'gender', responsivePriority: 6},
                {data: 'parsedDate', responsivePriority: 7},
                {data: 'ufc', responsivePriority: 5},
                {data: 'email', responsivePriority: 4}
            ],
        };
    },
    methods: {
        onCloseClick() {
            this.showConfirmationModal = false;
            this.selectedRow = null;
        },
        onViewClick(row) {
            this.$router.push({
                name: 'PatientTabs',
                params: {
                    id: row.id,
                }
            });
        },
        onNewResponseClick() {
            this.selectedRow = null;
            this.showReplyModal = null;
        },
        deletePatient() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.selectedRow) {
                axios.delete(`api/patients/${this.selectedRow.id}`).then(() => {
                    this.isFetching = false;
                    this.data.splice(this.data.indexOf(this.selectedRow), 1);
                    redrawTable(this.dataTable, this.data);
                    this.onCloseClick();
                }).catch(() => {
                    this.$toasted.show('Não foi possivel eliminar o utente selecionado', {
                        type: 'error',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                });
            }
        },
        onDeleteClick(row) {
            this.showConfirmationModal = true;
            this.selectedRow = row;
        },
        async getUsers() {
            if (!this.$store.state.user) this.$router.push(ROUTE.Login);
            this.isFetching = true;

            try {
                const response = await axios.get(`api/users/${this.$store.state.user.id}/ufcs`);
                const ufcsIds = [];
                const dataArray = [];

                this.ufcs = response.data.data;

                this.$store.commit('setUserUfcs', response.data.data);

                response.data.data.forEach(ufc => {
                    ufcsIds.push(ufc.id);
                });

                const res = await axios.post('api/patients', {ufcsIds});
                this.isFetching = false;

                res.data.data.forEach(d => {
                    const obj = d;
                    obj.gender = renderGender(d.gender);
                    obj.ufc = getCategoryNameById(d.ufc_id, response.data.data);
                    obj.parsedDate = parseDateToString(new Date(d.birthday));

                    dataArray.push(obj);
                });

                this.data = dataArray;
                this.originalData = dataArray;

            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            }
        },
        onMessageSend(row) {
            this.selectedRow = row;
            this.showReplyModal = true;
        }
    },
    async mounted() {
        await this.getUsers();
        if (this.$store.state.user.role === UserRoles.Admin) {
            this.columns.push({...TableActionColumns.Delete, responsivePriority: 2});
        }

        if (this.$store.state.user.role === UserRoles.Professional) {
            this.columns.push({...TableActionColumns.Message, responsivePriority: 2});
        }
        this.dataTable = await initDataTable('#patientsTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onViewClick, '#patientsTable', TableActionClasses.View);
        if (this.$store.state.user.role === UserRoles.Admin) {
            onClickHandler(this.dataTable, this.onDeleteClick, '#patientsTable', TableActionClasses.Delete);
        }
        if (this.$store.state.user.role === UserRoles.Professional) {
            onClickHandler(this.dataTable, this.onMessageSend, '#patientsTable', TableActionClasses.Message);
        }
    },
    components: {
        ConfirmationModal,
        ChatResponseModal,
    }
};
</script>
