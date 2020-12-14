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
        getUserMeals() {
            axios.get(`api/meals/${this.id}/user`).then(response => {
                if (this.isFetching) return;

                this.isFetching = true;

                this.meals = response.data.meals;
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
