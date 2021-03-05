<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Relatório gráfico
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" type="button" data-toggle="tooltip"
                                v-on:click="closeStats"
                                title="Tabela">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-table" fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body pt-4" style="color: gray">
                    <div class="card-deck">
                        <div class="card table-wrapper mb-2 p-2 pt-3 mt-2 text-dark">
                            <div class="card-text"><strong>Média total de duração de
                                exercício: </strong>{{ this.averageDuration.toFixed(2) }} minutos
                            </div>
                            <div class="card-text"><strong>Média mensal de duração de
                                exercício: </strong>{{ this.monthDurationAverage.toFixed(2) }}
                            </div>
                            <div class="card-text"><strong>Máximo mensal de duração de
                                exercício: </strong>{{ this.monthDurationMaximum.toFixed(2) }}
                            </div>
                            <div class="card-text"><strong>Mínimo mensal de duração de
                                exercício: </strong>{{ this.monthDurationMinimum.toFixed(2) }}
                            </div>
                        </div>
                        <div class="card table-wrapper mb-2 p-2 pt-3 mt-2 text-dark">
                            <div class="card-text"><strong>Média total de calorias
                                gastas: </strong>{{ this.averageCalories.toFixed(2) }}
                            </div>
                            <div class="card-text"><strong>Média mensal de calorias
                                gastas: </strong>{{ this.monthCaloriesAverage.toFixed(2) }}
                            </div>
                            <div class="card-text"><strong>Máximo mensal de calorias
                                gastas: </strong>{{ this.monthCaloriesMaximum.toFixed(2) }}
                            </div>
                            <div class="card-text"><strong>Mínimo mensal de calorias
                                gastas: </strong>{{ this.monthCaloriesMinimum.toFixed(2) }}
                            </div>
                        </div>
                    </div>
                    <div class="table-wrapper p-4">
                        <div class="row mb-4">
                            <div class="col-4"/>
                            <div class="col-4">
                                <span><strong>Filtrar Ano: </strong></span>
                                <select
                                    style="height: 40px"
                                    class="form-control"
                                    id="stats-year-select"
                                    v-on:change="onYearChange"
                                    v-model="selectedYear">
                                    <option v-for="y in years" :value="y.value">{{ y.label }}</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <span><strong>Filtrar meses: </strong></span>
                                <select
                                    style="height: 40px"
                                    class="form-control"
                                    id="stats-month-select"
                                    v-on:change="() => fillStatsByMonth(this.selectedYear, this.selectedMonth)"
                                    v-model="selectedMonth">
                                    <option v-for="m in months" :value="m.value">{{ m.label }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div v-if="chartDurationData == null" class="no-data">
                                Não existem registos de duração.
                            </div>
                            <line-chart v-else :chart-data="chartDurationData"
                                        :options="{
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            legend: {
                                            display: false
                                        },
                                        scales: {
                                            xAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                    display: true,
                                                    labelString: 'Dia do mês'
                                                }
                                            }],
                                            yAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                    display:true,
                                                    labelString: 'Duração'
                                                },
                                                ticks: {
                                                    beginAtZero: true
                                                }
                                           }]
                                        }
                                        }" :height="200">
                            </line-chart>
                        </div>
                        <div class="mt-2">
                            <div v-if="chartCaloriesData == null" class="no-data">
                                Não existem registos de calorias gastas.
                            </div>
                            <line-chart v-else :chart-data="chartCaloriesData"
                                        :options="{
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            legend: {
                                            display: false
                                        },
                                        scales: {
                                            xAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                    display: true,
                                                    labelString: 'Dia do mês'
                                                }
                                            }],
                                            yAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                    display:true,
                                                    labelString: 'Calorias gastas'
                                                },
                                                ticks: {
                                                    beginAtZero: true
                                                }
                                           }]
                                        }
                                        }" :height="200">
                            </line-chart>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">

import LineChart from '../../utils/LineChart';
import { ROUTE } from '../../../utils/routes';
import { parseMonth } from '../../../utils/misc';

export default {
    props: ['id', 'stats'],
    data: function () {
        return {
            months: [],
            years: [],
            chartDurationData: null,
            chartCaloriesData: null,
            isFetching: false,
            statsData: null,
            averageDuration: 0,
            averageCalories: 0,
            monthDurationAverage: 0,
            monthDurationMinimum: 0,
            monthDurationMaximum: 0,
            monthCaloriesAverage: 0,
            monthCaloriesMinimum: 0,
            monthCaloriesMaximum: 0,
            selectedMonth: null,
            selectedYear: null,
        };
    },
    methods: {
        closeStats() {
            this.$emit('close-exercise-stats');
        },
        onYearChange() {
            this.months = [];
            Object.keys(this.statsData.chartStats[this.selectedYear]).map(monthKey => {
                this.months.push({
                        label: parseMonth(monthKey),
                        value: monthKey,
                    }
                );
            });
            this.selectedMonth = Object.keys(this.statsData.chartStats[this.selectedYear])[0];
            this.fillStatsByMonth(this.selectedYear, Object.keys(this.statsData.chartStats[this.selectedYear])[0]);
        },
        fillStatsByMonth(year, month) {
            const labels = [];
            const valuesDuration = [];
            const valuesCalories = [];
            let sumDuration = 0;
            let minDurationAux = Number.MAX_SAFE_INTEGER;
            let maxDurationAux = 0;
            let sumCalories = 0;
            let minCaloriesAux = Number.MAX_SAFE_INTEGER;
            let maxCaloriesAux = 0;
            this.statsData.chartStats[year][month].forEach(day => {
                sumDuration += day.duration;
                sumCalories += day.calories;

                if (day.duration > maxDurationAux) maxDurationAux = day.duration;
                if (day.calories > maxCaloriesAux) maxCaloriesAux = day.calories;
                if (day.duration < minDurationAux) minDurationAux = day.duration;
                if (day.calories < minCaloriesAux) minCaloriesAux = day.calories;

                labels.push(day.label);
                valuesDuration.push(day.duration);
                valuesCalories.push(day.calories);
            });

            this.monthDurationAverage = sumDuration / this.statsData.chartStats[year][month].length;
            this.monthDurationMaximum = maxDurationAux;
            this.monthDurationMinimum = minDurationAux;
            this.monthCaloriesAverage = sumCalories / this.statsData.chartStats[year][month].length;
            this.monthCaloriesMaximum = maxCaloriesAux;
            this.monthCaloriesMinimum = minCaloriesAux;

            this.chartDurationData = {
                labels: labels,
                datasets: [
                    {
                        label: 'Duração',
                        borderColor: '#67C23A90',
                        fill: false,
                        backgroundColor: '#67C23A90',
                        data: valuesDuration
                    }
                ]
            };

            this.chartCaloriesData = {
                labels: labels,
                datasets: [
                    {
                        label: 'Calorias',
                        borderColor: '#67C23A90',
                        fill: false,
                        backgroundColor: '#67C23A90',
                        data: valuesCalories
                    }
                ]
            };
        },
        getExercisesStats() {
            if (this.isFetching) return
            this.isFetching = true;

            axios.get(`api/exercises/admin/stats/${this.id}`).then(response => {
                    this.isFetching = false;
                    this.statsData = response.data.stats;
                    this.averageDuration = response.data.stats.averageDuration;
                    this.averageCalories = response.data.stats.averageCalories;

                    if (response.data.stats && response.data.stats.totalRegisters > 0) {
                        const year = Object.keys(this.statsData.chartStats)[0];
                        Object.keys(this.statsData.chartStats).map(key => {
                            this.years.push({
                                label: key,
                                value: key,
                            });

                            if (key === year) {
                                Object.keys(this.statsData.chartStats[key]).map(monthKey => {
                                    this.months.push({
                                            label: parseMonth(monthKey),
                                            value: monthKey,
                                        }
                                    );
                                });
                            }
                        });
                    }

                    this.selectedYear = year;
                    this.selectedMonth = Object.keys(this.statsData.chartStats[year])[0];

                    this.fillStatsByMonth(year, Object.keys(this.statsData.chartStats[year])[0]);
                }
            ).catch(error => {
                this.isFetching = false;
                this.fetched = true;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        }
    },
    mounted() {
        this.getExercisesStats();
    },
    components: {
        LineChart,
    },
};
</script>

<style>
canvas {
    height: 300px !important;
}
</style>
