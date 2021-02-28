<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Plano Alimentar
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" data-toggle="tooltip" title="Novo Utilizador"
                                type="button" v-on:click.prevent="openDateModal">
                    <span v-if="isFetching" aria-hidden="true" class="spinner-border spinner-border-sm"
                          role="status"></span>
                            <span class="full-text">Nova Data</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body mt-2 text-dark">
                    Listagem de planos alimentares
                </div>
            </div>
        </div>
        <PlanDate @close="closeModal" v-show="showPlanDateModal" @save="openMealTypeModal"/>
        <MealTypeModal @close="closeModal" v-show="showMealTypeModal" :date="mealDate" @save="openIngredients"/>
    </div>
</template>


<script type="text/javascript">
import PlanDate from '../../modals/PlanDateModal';
import MealTypeModal from '../../modals/MealTypeModal';

export default {
    data: function () {
        return {
            isFetching: false,
            showPlanDateModal: false,
            showMealTypeModal: false,
            mealDate: '',
        };
    },
    methods: {
        openDateModal() {
            this.showPlanDateModal = true;
        },
        closeModal() {
            this.showPlanDateModal = false;
            this.showMealTypeModal = false;
        },
        openMealTypeModal(dateString) {
            this.showPlanDateModal = false;
            this.showMealTypeModal = true;
            this.mealDate = dateString;
        },
        openIngredients(name, time, portion, dateString) {
            this.showMealTypeModal = false;
            this.$emit('open-ingredient', name, time, portion, dateString);
        }
    },
    components: {
        PlanDate,
        MealTypeModal,
    }
};
</script>

