<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Instituições
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button"
                                data-toggle="tooltip"
                                title="Nova Instituição">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span class="full-text">Nova Instituição</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="ufcsTable" class="table-wrapper table table-hover dt-responsive w-100">
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
            :selectedName="selectedUsfName"
            :selectedId="selectedUsfId"
            :placeholder-name="placeholderName"
            :max-char="100"
            @save="this.onSaveClick"
        />
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            title="Eliminar Instituição"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteUsf"
            message="Tem a certeza que deseja eliminar a instituição selecionada?"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import AddCategory from '../modals/AddCategory';
import { ERROR_MESSAGES } from '../../utils/validations';
import { COLUMN_NAME } from '../../utils/table_elements';
import ConfirmationModal from '../modals/ConfirmationModal';
import { ROUTE } from '../../utils/routes';
import {
    EmptyObject,
    initDataTable,
    onClickHandler, redrawTable,
    TableActionClasses,
    TableActionColumns
} from '../../utils/dataTables';

export default {
    data() {
        return {
            isFetching: false,
            showModal: false,
            showConfirmationModal: false,
            selectedRow: null,
            modalTitle: '',
            selectedUsfName: null,
            placeholderName: 'Ex: Instituição A',
            selectedUsfId: null,
            data: [],
            dataTable: null,
            titles: [{
                label: COLUMN_NAME.Name,
                className: '',
            }, EmptyObject, EmptyObject],
            columns: [
                {data: 'name'},
                TableActionColumns.Edit,
                TableActionColumns.Delete,
            ],
        };
    },
    methods: {
        add() {
            this.selectedUsfId = null;
            this.selectedUsfName = '';
            this.selectedRow = null;
            this.modalTitle = 'Nova Instituição';
            this.showModal = true;
        },
        onEditClick(row) {
            this.selectedUsfId = row.id;
            this.selectedUsfName = row.name;
            this.modalTitle = 'Editar Instituição';
            this.showModal = true;
        },
        onDeleteClick(row) {
            this.selectedRow = row;
            this.showConfirmationModal = true;
        },
        deleteUsf() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.selectedRow) {
                axios.delete(`api/ufcs/${this.selectedRow.id}`).then(() => {
                    this.data.splice(this.data.indexOf(this.selectedRow), 1);
                    this.handleSuccess('UFS eliminada com sucesso!', true);
                }).catch(error => {
                    this.handleError(error);
                });
            }
        },
        onCloseClick() {
            this.showModal = false;
            this.showConfirmationModal = false;
        },
        onSaveClick(name, id) {
            this.showModal = false;
            if (this.isFetching) return;

            this.isFetching = true;

            if (id) {
                axios.put(`api/ufcs/${id}`, {
                    name,
                }).then(() => {
                    this.handleSuccess('UFS atualizada com sucesso!');
                }).catch(error => {
                    this.handleError(error);
                });
                return;
            }

            axios.post('api/ufcs', {
                name,
            }).then(() => {
                this.handleSuccess();
            }).catch(error => {
                this.handleError(error);
            });
        },
        async handleSuccess(message, fromDelete) {
            this.isFetching = false;
            if (message) this.showMessage(message, 'success');
            this.onCloseClick();
            if (!fromDelete) await this.getUsfs();
            redrawTable(this.dataTable, this.data);
        },
        handleError(error) {
            this.isFetching = false;
            const {response} = error;
            let message = ERROR_MESSAGES.unknownError;
            if (response) {
                if (response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                    return;
                }

                const {data} = response;
                if (data && data.errors && data.errors.name) {
                    message = ERROR_MESSAGES.alreadyExistingUSF;
                }
                if (data && data.error) {
                    message = data.error;
                }
            }
            this.showMessage(message, 'error');
        },
        showMessage(message, className) {
            this.$toasted.show(message, {
                type: className,
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
        async getUsfs() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get('api/ufcs');
                this.isFetching = false;
                this.data = response.data.data;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            }
        }
    },
    async mounted() {
        await this.getUsfs();
        this.dataTable = await initDataTable('#ufcsTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onDeleteClick, '#ufcsTable', TableActionClasses.Delete);
        onClickHandler(this.dataTable, this.onEditClick, '#ufcsTable', TableActionClasses.Edit);
    },
    components: {
        AddCategory,
        ConfirmationModal,
    }
};
</script>
