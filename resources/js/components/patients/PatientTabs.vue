<template>
    <div class="component-wrapper without-padding">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="home" aria-selected="true">Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#meals" role="tab" aria-controls="profile" aria-selected="false">Diário Alimentar</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent" style="height: calc(100% - 28px); background: linear-gradient(#68d03d50, transparent);">
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="home-tab" style="minHeight: 100%; background: linear-gradient(rgba(104, 208, 61, 0.314), transparent);">
                <Patient :id="this.$route.params.id" />
            </div>
            <div class="tab-pane fade" id="meals" role="tabpanel" aria-labelledby="profile-tab" style="minHeight: 100%; background: linear-gradient(#68d03d50, transparent)">
                <MealsList :id="this.$route.params.id" @meal-details="showMeal" v-show="!showMealDetails"/>
                <MealDetails :meal="selectedMeal" :date="date" @close-details="closeMeal" v-show="showMealDetails"/>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import Patient from './Patient';
    import MealsList from './meals/MealsList';
    import MealDetails from './meals/MealDetails';

    export default{
        data() {
          return {
              showMealDetails: false,
              selectedMeal: null,
              date: null,
          };
        },
        components: {
            Patient,
            MealsList,
            MealDetails,
        }, methods: {
            showMeal(row, date) {
                this.showMealDetails = true;
                this.selectedMeal = row;
                this.date = date;
            },
            closeMeal(){
                this.showMealDetails = false;
            }
        }
    };
</script>

<style>
    .without-padding {
        padding: 0;
    }

    .active {
        opacity: 1 !important;
    }
</style>
