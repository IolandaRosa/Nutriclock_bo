<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" style="max-width: 400px">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Informação Estatística</span>
                    </div>
                    <div class="modal-body m-0 pt-0">
                        <div class="card-text">
                            {{ mealName }}
                        </div>
                        <div class="text-secondary font-weight-bold text-md-center mr-2 text-uppercase">
                            <div class="card">
                                <div class="card-body d-flex p-2 align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightning-fill text-warning mr-2" viewBox="0 0 16 16">
                                        <path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5z"/>
                                    </svg>
                                    {{ calories }} kcal
                                </div>
                            </div>
                            <div class="card mt-4">
                                <div class="card-body d-flex p-2 align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-droplet-fill text-info mr-2" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 16a6 6 0 0 0 6-6c0-1.655-1.122-2.904-2.432-4.362C10.254 4.176 8.75 2.503 8 0c0 0-6 5.686-6 10a6 6 0 0 0 6 6zM6.646 4.646c-.376.377-1.272 1.489-2.093 3.13l.894.448c.78-1.559 1.616-2.58 1.907-2.87l-.708-.708z"/>
                                    </svg>
                                    {{ water }} litros
                                </div>
                            </div>

                            <div class = mt-2>
                                <pie-chart v-if="chartData !== null" :chart-data="chartData"
                                           :options="{
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            legend: {
                                            display: false
                                        },
                                    }">
                                </pie-chart>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-center p-0">
                        <button class="btn-bold btn btn-primary" @click="onCloseClick" style="width: 100px">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import PieChart from '../utils/PieChart';

export default {
    props: ['ingredients', 'mealName'],
    data() {
        return {
            chartData: null,
            water: null,
            calories: null,
        };
    },
    methods: {
        onCloseClick() {
            this.$emit('close');
        },
        getStatsInfo(ingredients) {
            axios.post(`api/meal-type-stats`, { 'ingredients': ingredients}).then(response => {
                this.water = response.data.data.water;
                this.calories = response.data.data.kcal;

                this.chartData = {
                    labels: ['Proteínas', 'Gorduras', 'Hidratos de Carbono', 'Fibra'],
                    datasets: [
                        {
                            label: '% Macronutrientes',
                            backgroundColor: [
                                '#F48FB1',
                                '#FFF59D',
                                '#90CAF9',
                                '#AED581'
                            ],
                            data: response.data.data.stats
                        }
                    ]
                };
            }).catch(() => {
            });
        }
    },
    watch: {
        ingredients: function (newVal, oldVal) {
            if (newVal) {
                this.getStatsInfo(newVal);
            }
        }
    },
    components: {
        PieChart,
    }
};
</script>
