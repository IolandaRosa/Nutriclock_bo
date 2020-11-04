<template>
    <div class="tab-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Diário de Refeições
            </div>
        </div>
        <div class="component-wrapper-body">
            <MealListItem :key="index" v-for="(d, index) in meals" :meal="d" :date="index" @show-details="showDetails"/>
        </div>

    </div>
</template>
m
<script type="text/javascript">
    /*jshint esversion: 6 */
    import MealListItem from './MealListItem';
    import { COLUMN_NAME } from '../../../utils/table_elements';
    import { parseDateToString, parseMealTypeToString } from '../../../utils/misc';

    export default{
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
                }).catch((e) => {
                    console.log('meals', e);
                    this.isFetching = false;
                });
            },
        },
        mounted() {
            console.log('mount')
            this.getUserMeals();
        },
        components: {
            MealListItem,
        },
    };
</script>
