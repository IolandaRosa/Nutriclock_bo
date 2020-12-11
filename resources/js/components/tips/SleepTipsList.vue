<template>
    <div class="component-wrapper">
        <div class="container pt-5">
            <div class="p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Dicas de Sono
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button"
                                data-toggle="tooltip"
                                title="Nova Dica">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span class="full-text">Nova Dica</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="sleepTipsTable" class="table-wrapper table table-hover dt-responsive w-100">
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
        <AddCategory
            v-show="showModal"
            @close="this.onCloseClick"
            :title="modalTitle"
            :selectedName="selectedTipDescription"
            :selectedId="selectedTipId"
            :placeholder-name="placeholderName"
            :max-char="100"
            @save="this.onSaveClick"
        />
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            title="Eliminar Dica de Sono"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteTip"
            message="Tem a certeza que deseja eliminar a dica de sono selecionada?"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {ROUTE} from '../../utils/routes';
import {COLUMN_NAME} from '../../utils/table_elements';
import {
    EmptyObject,
    initDataTable,
    onClickHandler,
    redrawTable,
    TableActionClasses,
    TableActionColumns
} from '../../utils/dataTables';
import AddCategory from '../modals/AddCategory';
import ConfirmationModal from '../modals/ConfirmationModal';
import {ERROR_MESSAGES} from '../../utils/validations';

export default {
    data() {
        return {
            isFetching: false,
            showModal: false,
            dataTable: null,
            showConfirmationModal: false,
            modalTitle: '',
            selectedTipDescription: null,
            placeholderName: 'Ex: Deve ouvir música relaxante.',
            selectedTipId: null,
            selectedRow: null,
            data: [],
            titles: [
                {
                    label: COLUMN_NAME.Description,
                    className: '',
                },
                EmptyObject,
                EmptyObject,
            ],
            columns: [
                {data: 'description'},
                TableActionColumns.Edit,
                TableActionColumns.Delete,
            ],
        };
    },
    methods: {
        add() {
            this.showModal = true;
            this.modalTitle = 'Nova Dica de Sono';
        },
        onDeleteClick(row) {
            this.selectedRow = row;
            this.selectedTipId = row.id;
            this.showConfirmationModal = true;
        },
        onEditClick(row) {
            this.selectedTipId = row.id;
            this.selectedTipDescription = row.description;
            this.modalTitle = 'Editar Dica de Sono';
            this.showModal = true;
        },
        onCloseClick() {
            this.showModal = false;
            this.selectedTipId = null;
            this.selectedTipDescription = '';
            this.showConfirmationModal = false;
        },
        async getSleepTips() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get('api/tips');
                this.isFetching = false;
                this.data = response.data.data;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            }
        },
        onSaveClick(description, id) {
            this.showModal = false;
            if (this.isFetching) return;

            this.isFetching = true;

            if (id) {
                axios.put(`api/tips/${id}`, {
                    description,
                }).then(() => {
                    this.handleSuccess();
                }).catch(error => {
                    this.handleError(error);
                });
                return;
            }

            axios.post('api/tips', {
                description,
            }).then(() => {
                this.handleSuccess();
            }).catch(error => {
                this.handleError(error);
            });
        },
        deleteTip() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.selectedRow) {
                axios.delete(`api/tips/${this.selectedTipId}`).then(() => {
                    this.isFetching = false;
                    this.data.splice(this.data.indexOf(this.selectedRow), 1);
                    redrawTable(this.dataTable, this.data);
                    this.showConfirmationModal = false;
                }).catch(error => {
                    this.handleError(error);
                });
            }
        },
        async handleSuccess() {
            this.isFetching = false;
            this.selectedTipDescription = '';
            this.selectedTipId = '';
            this.selectedRow = null;
            await this.getSleepTips();
            redrawTable(this.dataTable, this.data);
        },
        handleError(error) {
            this.isFetching = false;
            const {response} = error;
            let message = ERROR_MESSAGES.unknownError;
            if (response) {
                const {data} = response;

                if (data && data.error) {
                    message = data.error;
                }
            }

            this.$toasted.show(message, {
                type: 'error',
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
    },
    async mounted() {
        await this.getSleepTips();
        this.dataTable = await initDataTable('#sleepTipsTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onEditClick, '#sleepTipsTable', TableActionClasses.Edit);
        onClickHandler(this.dataTable, this.onDeleteClick, '#sleepTipsTable', TableActionClasses.Delete);
    },
    components: {
        AddCategory,
        ConfirmationModal,
    }
};
</script>
