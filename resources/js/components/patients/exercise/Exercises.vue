<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Atividade Física
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" type="button" data-toggle="tooltip"
                                v-on:click="showStats" title="Relatório Gráfico">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-graph-up" fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M0 0h1v15h15v1H0V0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body mt-2 text-dark">
                    <table id="exercisesTable" class="table-wrapper table table-hover dt-responsive w-100">
                        <thead>
                        <tr>
                            <th v-for="title in titles" :class="title.className">
                                {{ title.label }}
                            </th>
                        </tr>
                        </thead>
                        <tbody/>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
import {ROUTE} from '../../../utils/routes';
import {COLUMN_NAME} from '../../../utils/table_elements';
import {EmptyObject, initDataTable} from '../../../utils/dataTables';

export default {
    props: ['id'],
    data: function () {
        return {
            isFetching: false,
            data: [],
            dataTable: null,
            titles: [
                EmptyObject,
                {
                    label: COLUMN_NAME.Date,
                    className: '',

                },
                {
                    label: COLUMN_NAME.Name,
                    className: '',
                },
                {
                    label: COLUMN_NAME.StartTime,
                    className: '',
                },
                {
                    label: COLUMN_NAME.EndTime,
                    className: '',
                },
                {
                    label: COLUMN_NAME.Duration,
                    className: '',
                },
                {
                    label: COLUMN_NAME.BurnedCalories,
                    className: 'text-center',
                },
                {
                    label: COLUMN_NAME.Met,
                    className: 'text-center',
                },
            ],
            columns: [
                {
                    data: 'type',
                    responsivePriority: 1,
                    render: function (data) {
                        if (data.toString() === 'H') return '<div data-toggle="tooltip" title="Atividade Doméstica" class="dot pink-dot" />';
                        return '<div data-toggle="tooltip" title="Atividade Desportiva"  class="dot blue-dot" />';
                    }
                },
                {data: 'date', responsivePriority: 5},
                {data: 'name', responsivePriority: 2},
                {data: 'startTime', responsivePriority: 7},
                {data: 'endTime', responsivePriority: 8},
                {data: 'duration', responsivePriority: 3},
                {data: 'burnedCalories', responsivePriority: 4},
                {data: 'met', responsivePriority: 6},
            ],
        };
    },
    methods: {
        showStats() {
            this.$emit('show-exercise-stats');
        },
        millisToHours(value) {
            let hourWithMinutes = value/(1000 * 60 *60);
            let hours = Math.floor(hourWithMinutes);
            let minutes = hourWithMinutes - hours;

            if (minutes === 0) return `${hours}:00`;

            return `${hours}:${Math.ceil(minutes * 60)}`;
        },
        async getExercisesByUser() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get(`api/exercises/admin/${this.id}`);
                response.data.data.forEach(element => {
                    let dateArray = element.date.split("T");
                    element.date = dateArray[0];
                    element.startTime = this.millisToHours(element.startTime);
                    element.endTime = this.millisToHours(element.endTime);
                });
                this.data = response.data.data;
                this.isFetching = false;
            } catch (error) {
                console.log(error);
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            }
        },
    },
    async mounted() {
        await this.getExercisesByUser();
        this.dataTable = await initDataTable('#exercisesTable', this.data, this.columns);

        const that = this;
        window.addEventListener("resize", function(){
            that.dataTable.columns.adjust().draw();
        }, true);
    },
};
</script>

<style>
.table-wrapper {
    background: #fdfdfd;
    box-shadow: 0 3px 6px #0f0f0f28;
}

.dot {
    height: 20px;
    width: 20px;
    box-shadow: 0 3px 6px #0f0f0f28;
    border-radius: 10px;
    margin: auto;
}

.pink-dot {
    background: #C18C8C;
}

.blue-dot {
    background: #6890A7;
}

.shown > img {
    transform: rotate(90deg);
}
</style>
