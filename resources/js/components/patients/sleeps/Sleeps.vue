<template>
    <div class="tab-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Diário do Sono
            </div>
            <div class="component-wrapper-right">
                <button class="btn-bold btn btn-success" type="button" data-toggle="tooltip"
                        v-on:click="showStats" title="Estatísticas" >
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-graph-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="component-wrapper-body pt-4" style="color: gray">
            <table id="sleepsTable" class="table-wrapper table table-hover dt-responsive w-100">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora de Deitar</th>
                    <th>Hora de Levantar</th>
                    <th class="text-center">Qualidade do Sono</th>
                    <th/>
                </tr>
                </thead>
                <tbody/>
            </table>
        </div>
    </div>
</template>

<script type="text/javascript">
import {ROUTE} from '../../../utils/routes';
import {COLUMN_NAME} from '../../../utils/table_elements';

export default {
    props: ['id'],
    data: function () {
        return {
            isFetching: false,
            data: [],
        };
    },
    methods: {
        showStats() {
            this.$emit('show-sleep-stats');
        },
        formatDetailRow(d) {
            const act = d.activities || 'Sem informação';
            const mot = d.motives || 'Sem informação';
            return '<table class="table-wrapper table w-100 table-sm table-striped"">' +
                '<tr>' +
                '<td class="font-weight-bold">Atividades realizadas antes de adormecer</td>' +
                '</tr>' +
                '<tr>' +
                '<td style="font-size: 13px">' + act + '</td>' +
                '</tr>' +
                '<tr>' +
                '<td class="font-weight-bold">Outros motivos que podem ter prejudicado a qualidade do sono</td>' +
                '</tr>' +
                '<tr>' +
                '<td style="font-size: 13px">' + mot + '</td>' +
                '</tr>' +
                '</table>';
        },
        getSleepByUser() {
            if (this.isFetching) return;
            this.isFetching = true;

            const that = this;

            axios.get(`api/sleeps/${this.id}`).then(sleepsResponse => {
                this.isFetching = false;
                const sleepsTable = $('#sleepsTable').DataTable({
                        data: sleepsResponse.data.data,
                        columns: [
                            {data: 'date'},
                            {data: 'sleepTime'},
                            {data: 'wakeUpTime'},
                            {
                                data: 'hasWakeUp',
                                orderable: false,
                                render: function (data) {
                                    if (data) return '<div class="dot green-dot" />';
                                    return '<div class="dot red-dot" />';
                                }
                            },
                            {
                                className: 'details-control text-center',
                                orderable: false,
                                data: null,
                                defaultContent: '',
                                render: function () {
                                    return '<img src="/images/next.png" height="20" width="20" />';
                                }
                            },
                        ],
                        "language": {
                            "lengthMenu": "Ver _MENU_ registos por página",
                            "zeroRecords": "Não encontrado",
                            "info": "Página _PAGE_ de _PAGES_",
                            "infoEmpty": "Sem registos",
                            "infoFiltered": "(filtrado de _MAX_ registos)",
                            "emptyTable": "Não existem dados",
                            "loadingRecords": "Carregando...",
                            "processing": "Processando...",
                            "search": "Pesquisa",
                            "paginate": {
                                "first": "<<",
                                "last": ">>",
                                "next": ">",
                                "previous": "<"
                            },
                            "aria": {
                                "sortAscending": ": Ordem crescente",
                                "sortDescending": ": Ordem decrescente"
                            }
                        }
                    },
                );


                $('#sleepsTable tbody').on('click', 'td.details-control', function () {
                    const tr = $(this).closest('tr');

                    const row = sleepsTable.row(tr);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('gray-bg');
                        $(this).removeClass('shown');
                    } else {
                        $(this).addClass('shown');
                        tr.addClass('gray-bg');
                        row.child(that.formatDetailRow(row.data())).show();
                    }
                });
            }).catch((error) => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            });
        },
    },
    mounted() {
        this.getSleepByUser();
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

.gray-bg {
    background: #dee2e6;
}
</style>
