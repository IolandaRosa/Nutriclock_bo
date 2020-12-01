<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Doenças
            </div>
            <div class="component-wrapper-right">
                <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button" data-toggle="tooltip"
                        title="Nova Categoria">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                    <span class="full-text">Nova Doença</span>
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
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            title="Eliminar Doença / Alergia"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteDisease"
            message="Tem a certeza que deseja eliminar a doença / alergia selecionada?"
        />
        <AddDisease
            v-show="showModal"
            @close="this.onCloseClick"
            :title="modalTitle"
            :selected-disease-name="selectedDiseaseName"
            :selected-disease-id="selectedDiseaseId"
            :selected-disease-type="selectedDiseaseType"
            @save="this.onSaveClick"
        />
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import AddDisease from '../modals/AddDisease';
    import {ERROR_MESSAGES} from '../../utils/validations';
    import {COLUMN_NAME} from '../../utils/table_elements';
    import ConfirmationModal from '../modals/ConfirmationModal';
    import {renderDiseaseStringToType, renderDiseaseType} from "../../utils/misc";
    import { ROUTE } from '../../utils/routes';

    export default {
        data() {
            return {
                isFetching: false,
                showModal: false,
                modalTitle: '',
                placeholderName: 'Nome (Ex: Doença Cardíaca)',
                selectedDiseaseName: null,
                selectedDiseaseId: null,
                selectedDiseaseType: null,
                data: [],
                showConfirmationModal: false,
                selectedRow: null,
                titles: [{
                    prop: "id",
                    label: COLUMN_NAME.Id,
                }, {
                    prop: "name",
                    label: COLUMN_NAME.Name,
                }, {
                    prop: "type",
                    label: COLUMN_NAME.Type,
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
                this.modalTitle = 'Adicionar Doença / Alergia';
            },
            onEditClick(row) {
                this.selectedDiseaseName = row.name;
                this.selectedDiseaseId = row.id;
                this.selectedDiseaseType = renderDiseaseStringToType(row.type);
                this.modalTitle = "Editar Doença / Alergia";
                this.showModal = true;
            },
            onDeleteClick(row) {
                this.selectedRow = row;
                this.showConfirmationModal = true;
            },
            deleteDisease() {
                if (this.isFetching) return;

                this.isFetching = true;
                if (this.selectedRow) {
                    axios.delete(`api/diseases/${this.selectedRow.id}`).then(() => {
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
            onSaveClick(name, type, id) {
                this.showModal = false;
                if (this.isFetching) return;

                this.isFetching = true;

                if (id) {
                    axios.put(`api/diseases/${id}`, {
                        name,
                        type,
                    }).then(() => {
                        this.handleSuccess();
                    }).catch(error => {
                        this.handleError(error);
                    });
                    return;
                }
                axios.post('api/diseases', {
                    name,
                    type
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
                this.getDiseases();
            },
            handleError(error) {
                this.isFetching = false;
                const {response} = error;
                let message = ERROR_MESSAGES.unknownError;
                if (response) {
                    const {data} = response;
                    if (data && data.errors && data.errors.name) {
                        message = ERROR_MESSAGES.alreadyExistingDisease;
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
            getDiseases() {
                if (this.isFetching) return;
                this.isFetching = true;

                axios.get('api/diseases').then((response) => {
                    this.isFetching = false;
                    if (response.data.data) {
                        response.data.data.forEach(d => {
                            d.type = renderDiseaseType(d.type);
                        });
                    }
                    this.data = response.data.data;
                }).catch((error) => {
                    this.isFetching = false;
                    if (error.response && error.response.status === 401) {
                        this.$router.push(ROUTE.Login)
                    }
                });
            }
        },
        mounted() {
            this.getDiseases();
        },
        components: {
            AddDisease,
            ConfirmationModal,
        }
    };
</script>
