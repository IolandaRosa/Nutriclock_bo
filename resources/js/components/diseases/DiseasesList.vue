<template>
    <div class="component-wrapper">
        <div class="container pt-5 pb-2">
            <div class="p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Patologias
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button"
                                data-toggle="tooltip"
                                title="Nova Patologia">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span class="full-text">Nova Patologia</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="diseasesTable" class="table-wrapper table table-hover dt-responsive w-100">
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
            title="Eliminar Patologia / Alergia"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteDisease"
            message="Tem a certeza que deseja eliminar a patologia / alergia selecionada?"
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
import {ROUTE} from '../../utils/routes';
import {
    EmptyObject,
    initDataTable,
    onClickHandler, redrawTable,
    TableActionClasses,
    TableActionColumns
} from "../../utils/dataTables";

export default {
    data() {
        return {
            isFetching: false,
            showModal: false,
            modalTitle: '',
            placeholderName: 'Ex: Doença Cardíaca',
            selectedDiseaseName: null,
            selectedDiseaseId: null,
            selectedDiseaseType: null,
            data: [],
            dataTable: null,
            showConfirmationModal: false,
            selectedRow: null,
            titles: [{
                label: COLUMN_NAME.Name,
                className: '',
            }, {
                label: COLUMN_NAME.Type,
                className: '',
            }, EmptyObject, EmptyObject],
            columns: [
                {data: 'name', responsivePriority: 3 },
                { data: 'type', responsivePriority: 4 },
                { ...TableActionColumns.Edit, responsivePriority: 1 },
                { ...TableActionColumns.Delete, responsivePriority: 2 },
            ],
        };
    },
    methods: {
        add() {
            this.showModal = true;
            this.modalTitle = 'Adicionar Patologia / Alergia';
        },
        onEditClick(row) {
            this.selectedDiseaseName = row.name;
            this.selectedDiseaseId = row.id;
            this.selectedDiseaseType = renderDiseaseStringToType(row.type);
            this.modalTitle = "Editar Patologia / Alergia";
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
                    this.data.splice(this.data.indexOf(this.selectedRow), 1);
                    this.handleSuccess('Patologia eliminada com sucesso!');
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
                    this.handleSuccess('Patologia atualizada com sucesso!');
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
        async handleSuccess(message) {
            this.isFetching = false;
            if (message) this.showMessage(message, 'success');
            this.onCloseClick();
            await this.getDiseases();
            redrawTable(this.dataTable, this.data);
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
        async getDiseases() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get('api/diseases');
                this.isFetching = false;
                if (response.data.data) {
                    response.data.data.forEach(d => {
                        d.type = renderDiseaseType(d.type);
                    });
                }
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
        await this.getDiseases();
        this.dataTable = await initDataTable('#diseasesTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onDeleteClick, '#diseasesTable', TableActionClasses.Delete);
        onClickHandler(this.dataTable, this.onEditClick, '#diseasesTable', TableActionClasses.Edit);
    },
    components: {
        AddDisease,
        ConfirmationModal,
    }
};
</script>
