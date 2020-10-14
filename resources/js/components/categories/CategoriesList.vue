<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Categorias Profissionais
            </div>
            <div class="component-wrapper-right">
                <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button" data-toggle="tooltip" title="Nova Categoria">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                              aria-hidden="true"></span>
                    <span class="full-text">Nova Categoria Profissional</span>
                    <span class="min-text">+</span>
                </button>
            </div>
        </div>
        <div class="component-wrapper-body">
            <data-tables :data="data" :pagination-props="{ pageSizes: [8] }" :action-col="actionCol">
                <el-table-column v-for="title in titles" :prop="title.prop" :label="title.label" :key="title.label" :sortable="true">
                </el-table-column>
            </data-tables>
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

    export default{
        data() {
            return {
                isFetching: false,
                showModal: false,
                modalTitle: '',
                placeholderName: 'Nome (Ex: Médico)',
                selectedCategoryName: null,
                selectedCategoryId: null,
                data: [],
                showConfirmationModal: false,
                selectedRow: null,
                titles: [{
                    prop: "id",
                    label: COLUMN_NAME.Id,
                }, {
                    prop: "name",
                    label: COLUMN_NAME.Name,
                }],
                actionCol: {
                    label: ' ',
                    props: {
                        align: 'center',
                    },
                    buttons: [{
                        props: {
                            type: 'primary is-circle',
                            icon: 'el-icon-edit'
                        },
                        handler: row => { this.onEditClick(row); },
                        label: ''
                    }, {
                        props: {
                            type: 'danger is-circle',
                            icon: 'el-icon-delete'
                        },
                        handler: row => { this.onDeleteClick(row); },
                        label: ''
                    }]
                }
            };
        },
        methods:{
            add(){
                this.showModal = true;
                this.modalTitle = 'Nova Categoria Profissional';
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
            deleteCategory(){
                if (this.isFetching) return;

                this.isFetching = true;
                if (this.selectedRow) {
                    axios.delete(`api/professionalCategories/${this.selectedRow.id}`).then(() => {
                        this.isFetching = false;
                        this.data.splice(this.data.indexOf(this.selectedRow), 1);
                        this.showConfirmationModal = false;
                    }).catch(error => {
                        this.handleError(error);
                    });
                }
            },
            onCloseClick() {
                this.showModal = false;
                this.selectedCategoryId = null;
                this.selectedCategoryName = '';
                this.showConfirmationModal = false;
                this.selectedRow = null;
            },
            onSaveClick(name, id) {
                this.showModal = false;
                if (this.isFetching) return;

                this.isFetching = true;

                if (id) {
                    axios.put(`api/professionalCategories/${id}`, {
                        name,
                    }).then(() => {
                        this.handleSuccess();
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
            handleSuccess() {
                this.isFetching = false;
                this.selectedCategoryName = '';
                this.selectedCategoryId = '';
                this.getCategories();
            },
            handleError(error) {
                this.isFetching = false;
                const { response } = error;
                let message = ERROR_MESSAGES.unknownError;
                if (response) {
                    const {data} = response;
                    if (data && data.errors && data.errors.name) {
                        message = ERROR_MESSAGES.alreadyExistingProfessionalCategory;
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
            getCategories() {
                if (this.isFetching) return;
                this.isFetching = true;

                axios.get('api/professionalCategories').then((response) => {
                    this.isFetching = false;
                    this.data = response.data.data;
                }).catch((error) => {
                    this.isFetching = false;
                });
            }
        },
        mounted() {
            this.getCategories();
        },
        components: {
            AddCategory,
            ConfirmationModal,
        }
    };
</script>

<style>
    .sidebar {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        position: absolute;
        top:45px;
        bottom: 0;
        background: white;
        max-width: 200px;
        box-shadow: 0 3px 6px #0f0f0f28;
        z-index: 1;
        flex-direction: column;
    }

    a {
        color: #cceee1;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 16px;
        font-size: 14px;
        width: 100%;
        text-decoration: none;
    }

    .sidebar-item-text {
        margin-left: 16px;
    }

    a:hover {
        color: white;
        background: #cceee1;
        text-decoration: none;
    }

    .active {
        color: white;
        background: #cceee1;
        opacity: 0.7;
        box-shadow: 0 3px 6px #0f0f0f28;
    }

    @media only screen and (max-width: 900px) {
        .sidebar-item-text {
            display: none;
        }

        a {
            justify-content: center;
        }
    }

    @media only screen and (max-width: 500px) {
        .sidebar {
            justify-content: center;
            left: 0;
            bottom: unset;
            right: 0;
            max-width: unset;
            flex-direction: row;
        }

        .sidebar-item-text {
            display: none;
        }
    }

    .el-select {
        display: none !important;
    }
</style>
