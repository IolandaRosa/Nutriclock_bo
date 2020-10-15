<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Unidades de Saúde Familiar
            </div>
            <div class="component-wrapper-right">
                <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button" data-toggle="tooltip"
                        title="Nova USF">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                    <span class="full-text">Nova USF</span>
                    <span class="min-text">+</span>
                </button>
            </div>
        </div>
        <div class="component-wrapper-body">
            <data-tables :data="data" :pagination-props="{ pageSizes: [8] }" :action-col="actionCol">
                <el-table-column v-for="title in titles" :prop="title.prop" :label="title.label" :key="title.label"
                                 :sortable="true">
                </el-table-column>
            </data-tables>
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
            title="Eliminar USF"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteUsf"
            message="Tem a certeza que deseja eliminar a Unidade de Saúde Familiar selecionada?"
        />
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import AddCategory from '../modals/AddCategory';
    import { ERROR_MESSAGES } from '../../utils/validations';
    import { COLUMN_NAME } from '../../utils/table_elements';
    import ConfirmationModal from '../modals/ConfirmationModal';

    export default {
        data() {
            return {
                isFetching: false,
                showModal: false,
                showConfirmationModal: false,
                selectedRow: null,
                modalTitle: '',
                selectedUsfName: null,
                placeholderName: 'Nome (Ex: Usf Xpto)',
                selectedUsfId: null,
                data: [],
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
                        handler: row => {
                            this.onEditClick(row);
                        },
                        label: ''
                    }, {
                        props: {
                            type: 'danger is-circle',
                            icon: 'el-icon-delete'
                        },
                        handler: row => {
                            this.onDeleteClick(row);
                        },
                        label: ''
                    }]
                }
            };
        },
        methods: {
            add() {
                this.showModal = true;
                this.modalTitle = 'Nova USF';
            },
            onEditClick(row) {
                this.selectedUsfId = row.id;
                this.selectedUsfName = row.name;
                this.modalTitle = 'Editar USF';
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
                this.selectedUsfId = null;
                this.selectedUsfName = '';
                this.selectedRow = null;
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
                        this.handleSuccess();
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
            handleSuccess() {
                this.isFetching = false;
                this.selectedUsfName = '';
                this.selectedUsfId = '';
                this.getUsfs();
            },
            handleError(error) {
                this.isFetching = false;
                const {response} = error;
                let message = ERROR_MESSAGES.unknownError;
                if (response) {
                    const {data} = response;
                    if (data && data.errors && data.errors.name) {
                        message = ERROR_MESSAGES.alreadyExistingUSF;
                    }

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
            getUsfs() {
                if (this.isFetching) return;
                this.isFetching = true;

                axios.get('api/ufcs').then((response) => {
                    this.isFetching = false;
                    this.data = response.data.data;
                }).catch((error) => {
                    this.isFetching = false;
                });
            }
        },
        mounted() {
            this.getUsfs();
        },
        components: {
            AddCategory,
            ConfirmationModal,
        }
    };
</script>
