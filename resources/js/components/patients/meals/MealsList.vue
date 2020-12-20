<template>
    <div class="tab-wrapper">
        <div class="container">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Diário Alimentar
                    </h3>
                </div>
                <div class="component-wrapper-body">
                    <div v-show="meals.length === 0" class="text-dark mt-2 font-weight-bold">
                        Não existem registos.
                    </div>
                    <MealListItem :key="index" v-for="(d, index) in meals" :meal="d" :date="index"
                                  @show-details="showDetails"/>
                </div>
            </div>
        </div>
    </div>
</template>
m
<script type="text/javascript">
/*jshint esversion: 6 */
import MealListItem from './MealListItem';
import {ROUTE} from '../../../utils/routes';

export default {
    props: ['id'],
    data() {
        return {
            isFetching: false,
            showMealModal: false,
            selectedRow: null,
            meals: [],
        }
    },
    methods: {
        showDetails(row, date) {
            this.$emit('meal-details', row, date);
        },
        calculateSubtotals() {

        },
        getUserMeals() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get(`api/meals/${this.id}/nutritional`).then(response => {
                this.meals = response.data.data;
                this.isFetching = false;
            }).catch((error) => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            });
        },
    },
    mounted() {
        this.getUserMeals();
    },
    components: {
        MealListItem,
    },
};
</script>
