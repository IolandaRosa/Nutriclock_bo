<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Categorias Profissionais
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button"
                                data-toggle="tooltip"
                                title="Nova Categoria">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span class="full-text">Nova Categoria Profissional</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="categoriesTable" class="table-wrapper table table-hover dt-responsive w-100">
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
            :selectedName="selectedCategoryName"
            :selectedId="selectedCategoryId"
            :placeholder-name="placeholderName"
            :max-char="40"
            @save="this.onSaveClick"
        />
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            title="Eliminar Categoria"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteCategory"
            message="Tem a certeza que deseja eliminar a categoria selecionada?"
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
    onClickHandler,
    redrawTable,
    TableActionClasses,
    TableActionColumns
} from '../../utils/dataTables';

export default {
    data() {
        return {
            isFetching: false,
            showModal: false,
            modalTitle: '',
            placeholderName: 'Ex: Médico',
            selectedCategoryName: null,
            selectedCategoryId: null,
            data: [],
            showConfirmationModal: false,
            selectedRow: null,
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
            this.selectedCategoryId = null;
            this.selectedCategoryName = '';
            this.selectedRow = null;
            this.modalTitle = 'Nova Categoria Profissional';
            this.showModal = true;
        },
        onEditClick(row) {
            this.selectedCategoryId = row.id;
            this.selectedCategoryName = row.name;
            this.modalTitle = `Editar Categoria ${row.name}`;
            this.showModal = true;
        },
        onDeleteClick(row) {
            this.selectedRow = row;
            this.showConfirmationModal = true;
        },
        deleteCategory() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.selectedRow) {
                axios.delete(`api/professionalCategories/${this.selectedRow.id}`).then(() => {
                    this.data.splice(this.data.indexOf(this.selectedRow), 1);
                    this.handleSuccess('Categoria eliminada com sucesso!', true);
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
                axios.put(`api/professionalCategories/${id}`, {
                    name,
                }).then(() => {
                    this.handleSuccess('Categoria atualizada com sucesso!');
                }).catch(error => {
                    this.handleError(error);
                });
                return;
            }

            axios.post('api/professionalCategories', {
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
            if (!fromDelete) await this.getCategories();
            redrawTable(this.dataTable, this.data);
        },
        handleError(error) {
            this.isFetching = false;
            const {response} = error;
            let message = ERROR_MESSAGES.unknownError;
            if (response) {
                const {data} = response;
                if (data && data.errors && data.errors.name) {
                    message = ERROR_MESSAGES.alreadyExistingProfessionalCategory;
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
        async getCategories() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get('api/professionalCategories');
                this.isFetching = false;
                this.data = response.data.data;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            }
        }
    },
    async mounted() {
        await this.getCategories();
        this.dataTable = await initDataTable('#categoriesTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onDeleteClick, '#categoriesTable', TableActionClasses.Delete);
        onClickHandler(this.dataTable, this.onEditClick, '#categoriesTable', TableActionClasses.Edit);
    },
    components: {
        AddCategory,
        ConfirmationModal,
    }
};
</script>
