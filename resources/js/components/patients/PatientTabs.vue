<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active"
                       style="color: #FFF"
                       id="home-tab" data-toggle="tab"
                       href="#profile" role="tab" aria-controls="home" aria-selected="true">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab"
                       style="color: #FFF"
                       data-toggle="tab" href="#meals"
                       role="tab" aria-controls="profile" aria-selected="false">Diário Alimentar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="sleep-tab"
                       style="color: #FFF"
                       data-toggle="tab" href="#sleeps"
                       role="tab" aria-controls="sleep"
                       aria-selected="false">Diário Sono</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="exercise-tab"
                       style="color: #FFF"
                       data-toggle="tab" href="#exercises"
                       role="tab" aria-controls="exercise"
                       aria-selected="false">Atividade Física</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="meal-plan-tab"
                       style="color: #FFF"
                       data-toggle="tab" href="#mealPlan"
                       role="tab" aria-controls="mealPlan"
                       aria-selected="false">Plano Alimentar</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="home-tab">
                    <Patient :id="this.$route.params.id"/>
                </div>
                <div class="tab-pane fade" id="meals" role="tabpanel" aria-labelledby="profile-tab">
                    <MealsList :id="this.$route.params.id" @meal-details="showMeal" v-show="!showMealDetails"/>
                    <MealDetails :meal="selectedMeal" :date="date" @close-details="closeMeal" v-show="showMealDetails"/>
                </div>
                <div class="tab-pane fade" id="sleeps" role="tabpanel" aria-labelledby="home-tab">
                    <Sleeps :id="this.$route.params.id" @show-sleep-stats="showSleepStat" v-show="!showSleepStats"/>
                    <SleepChart :id="this.$route.params.id" @close-sleep-stats="closeSleepStat" v-show="showSleepStats"/>
                </div>
                <div class="tab-pane fade" id="exercises" role="tabpanel" aria-labelledby="home-tab">
                    <Exercises :id="this.$route.params.id" @show-exercise-stats="showExerciseStat" v-show="!showExerciseStats"/>
                    <ExerciseStats :id="this.$route.params.id" @close-exercise-stats="closeExerciseStat" v-show="showExerciseStats"/>
                </div>
                <div class="tab-pane fade" id="mealPlan" role="tabpanel" aria-labelledby="home-tab">
                    <PlanList @open-ingredient="showIngredientsPage" v-show="!showIngredient" />
                    <MealIngredients
                        v-show="showIngredient"
                        :name="name"
                        :time="time"
                        :portion="portion"
                        :dateString="dateString"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import Patient from './Patient';
import MealsList from './meals/MealsList';
import MealDetails from './meals/MealDetails';
import Sleeps from './sleeps/Sleeps';
import SleepChart from './sleeps/SleepChart';
import Exercises from './exercise/Exercises';
import ExerciseStats from './exercise/ExerciseStats';
import PlanList from './plans/PlanList';
import MealIngredients from './plans/MealIngredients';

export default {
    data() {
        return {
            showMealDetails: false,
            showSleepStats: false,
            selectedMeal: null,
            date: null,
            showExerciseStats: false,
            showIngredient: false,
            name: '',
            time: '',
            portion: '',
            dateString: '',
        };
    },
    components: {
        SleepChart,
        Sleeps,
        Patient,
        MealsList,
        MealDetails,
        Exercises,
        ExerciseStats,
        PlanList,
        MealIngredients,
    },
    methods: {
        showMeal(row, date) {
            this.showMealDetails = true;
            this.selectedMeal = row;
            this.date = date;
        },
        closeMeal() {
            this.showMealDetails = false;
        },
        showSleepStat() {
            this.showSleepStats = true;
        },
        closeSleepStat() {
            this.showSleepStats = false;
        },
        showExerciseStat() {
            this.showExerciseStats = true;
        },
        closeExerciseStat() {
            this.showExerciseStats = false;
        },
        showIngredientsPage(name, time, portion, dateString) {
            this.showIngredient = true;
            this.name = name;
            this.time = time;
            this.portion = portion;
            this.dateString = dateString;
        }
    },
};
</script>

<style>
.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: #495057 !important;
    background-color: #f8fafc !important;
    border-color: #dee2e6 #dee2e6 #f8fafc !important;
}

a.nav-link:hover {
    color: #495057 !important;
    background-color: #f8fafc !important;
    border-color: #dee2e6 #dee2e6 #f8fafc !important;
    opacity: 0.9;
}

@media only screen and (max-width: 600px) {
    .tab-wrapper {
        padding: 0;
    }
}

</style>
