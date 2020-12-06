export const initDataTable = (tableId, data, columns) => {
    return $(tableId).DataTable({
            data,
            columns,
            language: dataTablesTranslations,
        },
    );
}

const dataTablesTranslations = {
    lengthMenu: 'Ver _MENU_ registos por página',
    zeroRecords: 'Não encontrado',
    info: 'Página _PAGE_ de _PAGES_',
    infoEmpty: 'Sem registos',
    infoFiltered: '(filtrado de _MAX_ registos)',
    emptyTable: 'Não existem dados',
    loadingRecords: 'Carregando...',
    processing: 'Processando...',
    search: 'Pesquisa',
    paginate: {
        first: '<<',
        last: '>>',
        next: '>',
        previous: '<'
    },
    aria: {
        sortAscending: ': Ordem crescente',
        sortDescending: ': Ordem decrescente'
    }
};

export const onClickHandler = (table, callback, tableId, className) => {
    $(tableId + ' tbody').on('click', 'td.' + className, function () {
        const tr = $(this).closest('tr');
        const row = table.row(tr);
        callback(row.data());
    });
}

export const TableActionClasses = {
    Edit: 'edit-control',
    Delete: 'delete-control'
}

export const EmptyObject = {
    label: '',
    className: 'w-20',
}

export const TableActionColumns = {
    Edit: {
        className: TableActionClasses.Edit,
        orderable: false,
        data: null,
        defaultContent: '',
        render: function () {
            return '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="#3490dc" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>\n' +
                '</svg>';
        }
    },
    Delete: {
        className: TableActionClasses.Delete,
        orderable: false,
        data: null,
        defaultContent: '',
        render: function () {
            return '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="#e3342f" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>\n' +
                '  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>\n' +
                '</svg>';
        }
    }
}

export const redrawTable = (table, data) => {
    table.clear();
    table.rows.add(data);
    table.draw();
}
