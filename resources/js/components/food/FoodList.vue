<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Alimentos
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" type="button"
                                data-toggle="tooltip"
                                v-on:click.prevent="onClickNewFood"
                                title="Novo Alimento">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span class="full-text">Novo Alimento</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <table id="foodTable" class="table-wrapper table table-hover dt-responsive w-100">
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
        <AddFoodModal
            v-show="showAddFoodModal"
            @close="closeModals"
            @save="updateFoodTable"
            :title="modalTitle"
            :selectedItem="selectedRow"
        />
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="closeModals"
            title="Eliminar Alimento"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="deleteDisease"
            message="Tem a certeza que deseja eliminar o alimento selecionado?"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {COLUMN_NAME} from '../../utils/table_elements';
import {ROUTE} from '../../utils/routes';
import {
    EmptyObject,
    initDataTable,
    TableActionColumns,
    TableActionClasses,
    onClickHandler,
    redrawTable,
} from '../../utils/dataTables';
import AddFoodModal from '../modals/AddFoodModal';
import ConfirmationModal from '../modals/ConfirmationModal';

export default {
    data() {
        return {
            isFetching: false,
            showAddFoodModal: false,
            modalTitle: '',
            data: [],
            dataTable: null,
            selectedRow: null,
            showConfirmationModal: false,
            titles: [{
                    label: COLUMN_NAME.Code,
                    className: '',
                },
                {
                    label: COLUMN_NAME.Name,
                    className: '',
                },
                {
                    label: COLUMN_NAME.energy_kcal,
                    className: '',
                },
                {
                    label: COLUMN_NAME.energy_kJ,
                    className: '',
                },
                {
                    label: COLUMN_NAME.water_g,
                    className: '',
                },
                {
                    label: COLUMN_NAME.protein_g,
                    className: '',
                },
                {
                    label: COLUMN_NAME.fats_g,
                    className: '',
                },
                {
                    label: COLUMN_NAME.carbo_hidrats_g,
                    className: '',
                },
                {
                    label: COLUMN_NAME.fiber_g,
                    className: '',
                },
                {
                    label: COLUMN_NAME.colesterol_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.vitA_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.vitD_pg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.tiamina_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.riboflavina_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.niacina_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.vitB6_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.vit_B12_pg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.vitC_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.na_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.k_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.ca_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.p_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.mg_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.fe_mg,
                    className: '',
                },
                {
                    label: COLUMN_NAME.zn_mg,
                    className: '',
                },
                EmptyObject, EmptyObject],
            columns: [
                {data: 'code'},
                {data: 'name'},
                {data: 'energy_kcal'},
                {data: 'energy_kJ'},
                {data: 'water_g'},
                {data: 'protein_g'},
                {data: 'fats_g'},
                {data: 'carbo_hidrats_g'},
                {data: 'fiber_g'},
                {data: 'colesterol_mg'},
                {data: 'vitA_mg'},
                {data: 'vitD_pg'},
                {data: 'tiamina_mg'},
                {data: 'riboflavina_mg'},
                {data: 'niacina_mg'},
                {data: 'vitB6_mg'},
                {data: 'vit_B12_pg'},
                {data: 'vitC_mg'},
                {data: 'na_mg'},
                {data: 'k_mg'},
                {data: 'ca_mg'},
                {data: 'p_mg'},
                {data: 'mg_mg'},
                {data: 'fe_mg'},
                {data: 'zn_mg'},
                {...TableActionColumns.Edit, responsivePriority: 1},
                {...TableActionColumns.Delete, responsivePriority: 2},
            ],
        };
    },
    methods: {
        closeModals() {
            this.showAddFoodModal = false;
            this.showConfirmationModal = false;
            this.selectedRow = null;
            this.modalTitle = '';
        },
        onClickNewFood() {
            this.showAddFoodModal = true;
            this.modalTitle = 'Novo Alimento';
        },
        async updateFoodTable() {
            await this.handleSuccess();
        },
        async getFood() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get('api/nutritional-info-static');
                this.isFetching = false;
                this.data = response.data.data;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            }
        },
        onDeleteClick(row) {
            this.selectedRow = row;
            this.showConfirmationModal = true;
        },
        onEditClick(row) {
            this.selectedRow = row;
            this.showAddFoodModal = true;
        },
        deleteDisease() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.selectedRow) {
                axios.delete(`api/nutritional-info-static/${this.selectedRow.code}`).then(() => {
                    this.isFetching = false;
                    this.handleSuccess();
                }).catch(() => {
                   this.isFetching = false;
                });
            }
        },
        async handleSuccess() {
            this.isFetching = false;
            this.closeModals();
            await this.getFood();
            redrawTable(this.dataTable, this.data);
        },
    },
    async mounted() {
        await this.getFood();
        this.dataTable = await initDataTable('#foodTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onDeleteClick, '#foodTable', TableActionClasses.Delete);
        onClickHandler(this.dataTable, this.onEditClick, '#foodTable', TableActionClasses.Edit);
    },
    components: {
        AddFoodModal,
        ConfirmationModal,
    }
};
</script>
