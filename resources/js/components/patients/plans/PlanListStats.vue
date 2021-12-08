<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <button class="btn btn-sm btn-outline-secondary mb-1" v-on:click="closePage">
                    🡄 Plano Alimentar
                </button>
                <div class="component-wrapper-header">
                    <h5 class="component-wrapper-left text-secondary d-flex align-items-center mb-0"
                        style="font-size: 18px !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-calendar mr-2" viewBox="0 0 16 16">
                            <path
                                d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        Informação Nutricional {{ date }}
                    </h5>
                </div>
                <div class="component-wrapper-body mt-2 text-dark">
                    <div class="d-flex justify-content-center">
                        <div
                            class="text-secondary font-weight-bold text-uppercase bg-white p-2 m-2 rounded with-shadow">
                            <div class="text-dark mb-2 text-capitalize">Estatisticas Totais</div>
                            <div class="d-flex p-2 align-items-center rounded border">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-lightning-fill text-warning mr-2" viewBox="0 0 16 16">
                                    <path
                                        d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5z"/>
                                </svg>
                                {{ calories }} kcal
                            </div>
                            <div class="mt-1 d-flex p-2 align-items-center rounded border">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-droplet-fill text-info mr-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M8 16a6 6 0 0 0 6-6c0-1.655-1.122-2.904-2.432-4.362C10.254 4.176 8.75 2.503 8 0c0 0-6 5.686-6 10a6 6 0 0 0 6 6zM6.646 4.646c-.376.377-1.272 1.489-2.093 3.13l.894.448c.78-1.559 1.616-2.58 1.907-2.87l-.708-.708z"/>
                                </svg>
                                {{ water }} litros
                            </div>
                            <div class=mt-2>
                                <pie-chart v-if="totalStats !== null" :chart-data="totalStats" :options="{
                                            responsive: true,
                                            maintainAspectRatio: false,
                                    }"/>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap">
                        <div class="text-secondary font-weight-bold text-uppercase bg-white p-2 m-2 rounded with-shadow overflow-auto" v-for="(day, index) in days" :key="index">
                            <div class="text-dark mb-2 text-capitalize d-flex">
                                <div class="flex-grow-1">{{ renderMealName(day.name) }}</div>
                                <div><small class="text-secondary">{{ day.hour }} horas</small></div>
                            </div>
                            <div class="d-flex p-2 align-items-center rounded border">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-lightning-fill text-warning mr-2" viewBox="0 0 16 16">
                                    <path
                                        d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5z"/>
                                </svg>
                                {{ day.energy }} kcal
                            </div>
                            <div class="d-flex p-2 align-items-center rounded border mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-droplet-fill text-info mr-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M8 16a6 6 0 0 0 6-6c0-1.655-1.122-2.904-2.432-4.362C10.254 4.176 8.75 2.503 8 0c0 0-6 5.686-6 10a6 6 0 0 0 6 6zM6.646 4.646c-.376.377-1.272 1.489-2.093 3.13l.894.448c.78-1.559 1.616-2.58 1.907-2.87l-.708-.708z"/>
                                </svg>
                                {{ (day.water /1000).toFixed(2) }} litros
                            </div>
                            <div class="mt-2">
                                <pie-chart v-if="day.stats !== null" :chart-data="{
                                        labels: labels,
                                        datasets: [
                                            {
                                                backgroundColor: colors,
                                                data: day.stats
                                            }
                                        ]
                                    }" :options="{
                                            responsive: true,
                                            maintainAspectRatio: false,
                                    }"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script type="text/javascript">
import PieChart from '../../utils/PieChart';
import { parseMealTypeToString } from '../../../utils/misc';

export default {
    props: ['date', 'id'],
    data: function () {
        return {
            isFetching: false,
            totalStats: null,
            water: null,
            calories: null,
            days: [],
            labels: ['Proteínas', 'Gorduras', 'Hidratos de Carbono', 'Fibra'],
            colors: [
                '#F48FB1',
                '#FFF59D',
                '#90CAF9',
                '#AED581'
            ]
        };
    },
    methods: {
        closePage() {
            this.$emit('open-plan-list');
        },
        renderMealName(value) {
            return parseMealTypeToString(value);
        }
    },
    mounted() {
        if (this.isFetching) return;
        this.isFetching = true;
        axios.get(`api/meal-type-stats/${this.id}`).then(response => {
            this.isFetching = false;

            this.totalStats = {
                labels: this.labels,
                datasets: [
                    {
                        backgroundColor: this.colors,
                        data: response.data.data.stats
                    }
                ]
            };
            this.water = response.data.data.water;
            this.calories = response.data.data.kcal;
            this.days = response.data.data.days;
        }).catch(() => {
            this.isFetching = false;
        });
    },
    components: {
        PieChart
    },

};
</script>

