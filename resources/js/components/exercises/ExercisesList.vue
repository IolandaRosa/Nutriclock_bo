<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="btn-group btn-group-sm w-100"  style="z-index: 0 !important;"  role="group">
                    <button :class="exerciseActive ? 'btn btn-primary w-50' : 'btn btn-outline-primary w-50'"
                            v-on:click.prevent="() => updateTable('Exercício Desportivo')">
                        Exercício Desportivo
                    </button>
                    <button :class="!exerciseActive ? 'btn btn-primary w-50' : 'btn btn-outline-primary w-50'"
                            v-on:click.prevent="() => updateTable('Tarefa Doméstica')">
                        Tarefa Doméstica
                    </button>
                </div>
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        {{ mainTitle }}
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button"
                                data-toggle="tooltip"
                                title="Novo Exercício">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span class="full-text">Novo Exercício</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="exercisesTable" class="table-wrapper table table-hover dt-responsive w-100">
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
            title="Eliminar Exercício Desportivo / Tarefa Doméstica"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.delete"
            message="Tem a certeza que deseja eliminar o exercício desportivo / tarefa doméstica selecionada?"
        />
        <AddExerciseModal
            v-show="showModal"
            @close="this.onCloseClick"
            :selectedRow="selectedRow"
            :isSport="exerciseActive"
            @save="this.onSaveClick"/>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import { ERROR_MESSAGES } from '../../utils/validations';
import { COLUMN_NAME } from '../../utils/table_elements';
import ConfirmationModal from '../modals/ConfirmationModal';
import { renderDiseaseStringToType } from '../../utils/misc';
import { ROUTE } from '../../utils/routes';
import {
    EmptyObject,
    initDataTable,
    onClickHandler,
    redrawTable,
    TableActionClasses,
    TableActionColumns
} from '../../utils/dataTables';
import AddExerciseModal from '../modals/AddExerciseModal';

export default {
    data() {
        return {
            isFetching: false,
            showModal: false,
            exerciseActive: true,
            sportsList: [],
            householdsList: [],
            mainTitle: 'Exercício Desportivo',
            dataTable: null,
            showConfirmationModal: false,
            selectedRow: null,
            titles: [{
                label: COLUMN_NAME.Name,
                className: '',
            }, {
                label: COLUMN_NAME.Met,
                className: '',
            }, EmptyObject, EmptyObject],
            columns: [
                {data: 'name', responsivePriority: 3},
                {data: 'met', responsivePriority: 4},
                {...TableActionColumns.Edit, responsivePriority: 1},
                {...TableActionColumns.Delete, responsivePriority: 2},
            ],
        };
    },
    methods: {
        add() {
            this.selectedRow = null;
            this.showModal = true;
        },
        updateTable(type) {
            if (type === 'Exercício Desportivo') {
                this.exerciseActive = true;
                this.mainTitle = 'Exercício Desportivo';
                redrawTable(this.dataTable, this.sportsList);
                return;
            }
            this.exerciseActive = false;
            this.mainTitle = 'Tarefa Doméstica';
            redrawTable(this.dataTable, this.householdsList);
        },
        onEditClick(row) {
            this.selectedRow = row;
            this.showModal = true;
        },
        onDeleteClick(row) {
            this.selectedRow = row;
            this.showConfirmationModal = true;
        },
        delete() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.selectedRow) {
                if (this.exerciseActive) {
                    axios.delete(`api/exercises-static/${this.selectedRow.id}`).then(() => {
                        this.handleSuccess();
                    }).catch(error => {
                        this.handleError(error);
                    });
                } else {
                    axios.delete(`api/households/${this.selectedRow.id}`).then(() => {
                        this.handleSuccess();
                    }).catch(error => {
                        this.handleError(error);
                    });
                }
            }
        },
        onCloseClick() {
            this.showModal = false;
            this.showConfirmationModal = false;
        },
        onSaveClick() {
            this.showModal = false;
            this.handleSuccess();
        },
        async handleSuccess(message) {
            this.isFetching = false;
            if (message) this.showMessage(message, 'success');
            this.onCloseClick();
            if (this.exerciseActive) {
                await this.getSports();
                redrawTable(this.dataTable, this.sportsList);
            } else {
                await this.getHouseholds();
                redrawTable(this.dataTable, this.householdsList);
            }
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
        async getSports() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get('api/exercises/list');
                this.isFetching = false;
                this.sportsList = response.data.data;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            }
        },
        async getHouseholds() {
            if (this.isFetching) return;
            this.isFetching = true;
            try {
                const response = await axios.get('api/households');
                this.isFetching = false;
                this.householdsList = response.data.data;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            }
        },
    },
    async mounted() {
        await this.getSports();
        this.dataTable = await initDataTable('#exercisesTable', this.sportsList, this.columns);
        onClickHandler(this.dataTable, this.onDeleteClick, '#exercisesTable', TableActionClasses.Delete);
        onClickHandler(this.dataTable, this.onEditClick, '#exercisesTable', TableActionClasses.Edit);
        await this.getHouseholds();
    },
    components: {
        ConfirmationModal,
        AddExerciseModal,
    }
}
;
</script>
