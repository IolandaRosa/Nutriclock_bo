<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Diário do Sono
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary mr-2" type="button" data-toggle="tooltip"
                                v-on:click="exportData" title="Exportar">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-bar-down"
                                 fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M1 3.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13a.5.5 0 0 1-.5-.5zM8 6a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 .708-.708L7.5 12.293V6.5A.5.5 0 0 1 8 6z"/>
                            </svg>
                        </button>
                        <button class="btn-bold btn btn-primary" type="button" data-toggle="tooltip"
                                v-on:click="showStats" title="Estatísticas">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-graph-up" fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M0 0h1v15h15v1H0V0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body mt-2 text-dark">
                    <table id="sleepsTable" class="table-wrapper table table-hover dt-responsive w-100">
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
import {initDataTable} from '../../../utils/dataTables';

export default {
    props: ['id'],
    data: function () {
        return {
            isFetching: false,
            data: [],
            dataTable: null,
            titles: [
                {
                    label: COLUMN_NAME.Date,
                    className: '',

                },
                {
                    label: COLUMN_NAME.SleepTime,
                    className: '',
                },
                {
                    label: COLUMN_NAME.WakeUpTime,
                    className: '',
                },
                {
                    label: COLUMN_NAME.TotalHours,
                    className: '',
                },
                {
                    label: COLUMN_NAME.HasWakeUp,
                    className: 'text-center',
                },
                {
                    label: 'Atividades realizadas antes de adormecer',
                    className: '',
                },
                {
                    label: 'Outros motivos que podem ter prejudicado a qualidade do sono',
                    className: '',
                },
            ],
            columns: [
                {data: 'date', responsivePriority: 3},
                {data: 'sleepTime', responsivePriority: 1},
                {data: 'wakeUpTime', responsivePriority: 2},
                {data: 'totalHours', responsivePriority: 4},
                {
                    data: 'hasWakeUp',
                    responsivePriority: 5,
                    orderable: false,
                    render: function (data) {
                        if (!data) return '<div class="dot green-dot" />';
                        return '<div class="dot red-dot" />';
                    }
                },
                {data: 'activities', responsivePriority: 6},
                {data: 'motives', responsivePriority: 7},
            ],
        };
    },
    methods: {
        showStats() {
            this.$emit('show-sleep-stats');
        },
        exportData() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/sleeps/export', {
                responseType: 'blob',
            }).then((response) => {
                this.isFetching = false;
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'sleeps.xlsx');
                link.click();
            }).catch((error) => {
                this.isFetching = false;
                this.$toasted.show('Ocorreu um erro durente o download do ficheiro', {
                    type: 'error',
                    duration: 2000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            });
        },
        async getSleepByUser() {
            if (this.isFetching) return;
            this.isFetching = true;

            try {
                const response = await axios.get(`api/sleeps/${this.id}`);
                this.data = response.data.data;
                this.isFetching = false;
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            }
        },
    },
    async mounted() {
        await this.getSleepByUser();
        this.dataTable = await initDataTable('#sleepsTable', this.data, this.columns);

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

.red-dot {
    background: coral;
}

.green-dot {
    background: #7dce65;
}

.shown > img {
    transform: rotate(90deg);
}
</style>
