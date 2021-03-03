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
                    <div v-if="!this.plan || this.plan.length === 0">
                        Não existem planos alimentares registados
                    </div>
                    <div v-else v-for="p in this.plan" :key="p.id" class="bg-white rounded with-shadow p-2 mb-2">
                        <span>{{ p.dayOfWeek }} {{ p.date }}</span>

                        <div v-for="mealType in p.mealTypes" :key="mealType.id" class="bg-light rounded with-shadow p-2 mb-2"">
                            <span> {{ mealType.type }}</span>
                            <span> {{ mealType.hour }}</span>
                            <span> {{ mealType.portion }}</span>

                            <div v-for="i in mealType.ingredients" :key="i.id">
                                <span> {{ i.name }}</span>
                                <span> {{ i.quantity }}</span>
                                <span> {{ i.unit }}</span>
                                <span> {{ i.grams }}</span>
                            </div>
                        </div>
                    </div>
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
    props: ['id'],
    data: function () {
        return {
            isFetching: false,
            showPlanDateModal: false,
            showMealTypeModal: false,
            mealDate: '',
            plan: [],
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
        },
    },
    mounted() {
        if (this.isFetching) return;
        this.isFetching = true;

        axios.get(`api/meal-plans/${this.id}`).then(response => {
            this.isFetching = false;
            this.plan = response.data.data;
        }).catch(() => {
            this.isFetching = false;
        });
    },
    components: {
        PlanDate,
        MealTypeModal,
    }
};
</script>

