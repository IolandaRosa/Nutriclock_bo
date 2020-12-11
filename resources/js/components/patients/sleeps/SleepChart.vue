<template>
    <div class="tab-wrapper">
        <div class="container">
            <div class="p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Diário do Sono
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-success" type="button" data-toggle="tooltip"
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
                    <div class="table-wrapper" style="padding: 8px; margin-bottom: 16px">
                        <div><strong>Média total de horas dormidas: </strong>{{ this.totalAverage.toFixed(2) }}</div>
                        <div><strong>Média mensal de horas dormidas: </strong>{{ this.monthAverage.toFixed(2) }}</div>
                        <div><strong>Máximo mensal de horas dormidas: </strong>{{ this.monthMaximum.toFixed(2) }}</div>
                        <div><strong>Minimo mensal de horas dormidas: </strong>{{ this.monthMinimum.toFixed(2) }}</div>
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
                            <div v-if="chartData == null" class="no-data">
                                Não existem registos.
                            </div>
                            <line-chart v-else :chart-data="chartData"
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
                                labelString: 'Dias do mês'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display:true,
                                labelString: 'Horas de sono'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                       }]
                    }
                    }" :height="200"></line-chart>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">

import LineChart from './LineChart';
import {ROUTE} from '../../../utils/routes';
import {parseMonth} from '../../../utils/misc';

export default {
    props: ['id', 'stats'],
    data: function () {
        return {
            months: [],
            years: [],
            chartData: null,
            isFetching: false,
            statsData: null,
            totalAverage: 0,
            monthAverage: 0,
            monthMinimum: 0,
            monthMaximum: 0,
            selectedMonth: null,
            selectedYear: null,
        };
    },
    methods: {
        closeStats() {
            this.$emit('close-sleep-stats');
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
            const values = [];
            let sum = 0;
            let minAux = 24;
            let maxAux = 0;
            this.statsData.chartStats[year][month].forEach(day => {
                sum += day.value;

                if (day.value > maxAux) {
                    maxAux = day.value;
                }

                if (day.value < minAux) {
                    minAux = day.value;
                }

                labels.push(day.label);
                values.push(day.value);
            });

            this.monthAverage = sum / this.statsData.chartStats[year][month].length;
            this.monthMaximum = maxAux;
            this.monthMinimum = minAux;

            this.chartData = {
                labels: labels,
                datasets: [
                    {
                        label: 'Horas de Sono',
                        borderColor: '#67C23A90',
                        fill: false,
                        backgroundColor: '#67C23A90',
                        data: values
                    }
                ]
            };
        },
        getSleepStats() {
            if (this.isFetching) return
            this.isFetching = true;

            axios.get(`api/sleeps/stats/${this.id}`).then(response => {
                    this.isFetching = false;
                    this.statsData = response.data.stats;
                    this.totalAverage = response.data.stats.averageTime;

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
                    this.$router.push(ROUTE.Login)
                }
            });
        }
    },
    mounted() {
        this.getSleepStats();
    }
    ,
    components: {
        LineChart,
    }
    ,
}
;
</script>

<style>
canvas {
    height: 300px !important;
}
</style>
