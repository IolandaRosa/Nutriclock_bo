<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Utentes
            </div>
        </div>
        <div class="component-wrapper-body">
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
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            title="Eliminar Dica de Sono"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deletePatient"
            message="Tem a certeza que deseja eliminar a dica de sono selecionada?"
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
            }, EmptyObject, EmptyObject],
            columns: [
                {data: 'name'},
                {data: 'gender'},
                {data: 'parsedDate'},
                {data: 'ufc'},
                {data: 'email'},
                TableActionColumns.View,
                TableActionColumns.Delete,
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
                    this.$router.push(ROUTE.Login)
                }
            }
        },
    },
    async mounted() {
        await this.getUsers();
        this.dataTable = await initDataTable('#patientsTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onViewClick, '#patientsTable', TableActionClasses.View);
        onClickHandler(this.dataTable, this.onDeleteClick, '#patientsTable', TableActionClasses.Delete);
    },
    components: {
        ConfirmationModal,
    }
};
</script>
