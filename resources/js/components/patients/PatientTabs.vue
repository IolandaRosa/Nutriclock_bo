<template>
    <div class="component-wrapper without-padding">
        <ul class="nav nav-tabs" id="myTab" role="tablist" style="border-bottom: 0px">
            <li class="nav-item">
                <a class="nav-link active" style="border-color: transparent; color:#FFF; box-shadow: none;"
                   id="home-tab" data-toggle="tab"
                   href="#profile" role="tab" aria-controls="home" aria-selected="true">Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="border-color: transparent; color:#FFF; box-shadow: none;" id="profile-tab"
                   data-toggle="tab" href="#meals"
                   role="tab" aria-controls="profile" aria-selected="false">Diário Alimentar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="border-color: transparent; color:#FFF; box-shadow: none;" id="sleep-tab"
                   data-toggle="tab" href="#sleeps"
                   role="tab" aria-controls="sleep"
                   aria-selected="false">Diário Sono</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent"
             style="height: calc(100% - 28px); background: linear-gradient(#a3dc92, transparent);">
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="home-tab"
                 style="minHeight: 100%;">
                <Patient :id="this.$route.params.id"/>
            </div>
            <div class="tab-pane fade" id="meals" role="tabpanel" aria-labelledby="profile-tab"
                 style="minHeight: 100%;">
                <MealsList :id="this.$route.params.id" @meal-details="showMeal" v-show="!showMealDetails"/>
                <MealDetails :meal="selectedMeal" :date="date" @close-details="closeMeal" v-show="showMealDetails"/>
            </div>
            <div class="tab-pane fade" id="sleeps" role="tabpanel" aria-labelledby="home-tab" style="minHeight: 100%;">
                <Sleeps :id="this.$route.params.id" @show-sleep-stats="showSleepStat" v-show="!showSleepStats"/>
                <SleepChart :id="this.$route.params.id" @close-sleep-stats="closeSleepStat" v-show="showSleepStats"/>
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

export default {
    data() {
        return {
            showMealDetails: false,
            showSleepStats: false,
            selectedMeal: null,
            date: null,
        };
    },
    components: {
        SleepChart,
        Sleeps,
        Patient,
        MealsList,
        MealDetails,
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
    },
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
