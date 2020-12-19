export const initDataTable = (tableId, data, columns) => {
    return $(tableId).DataTable({
            data,
            columns,
            language: dataTablesTranslations,
        },
    );
}

const dataTablesTranslations = {
    lengthMenu: '_MENU_ Registos por página',
    zeroRecords: 'Não encontrado',
    info: 'Página _PAGE_ de _PAGES_',
    infoEmpty: 'Sem registos',
    infoFiltered: '(filtrado de _MAX_ registos)',
    emptyTable: 'Não existem dados',
    loadingRecords: 'Carregando...',
    processing: 'Processando...',
    search: '<span data-toggle="tooltip" title="Pesquisar"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">\n' +
        '  <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>\n' +
        '  <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>\n' +
        '</svg></span>',
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
        let row = table.row(tr);

        if (jQuery.isEmptyObject(row.data())) {
            row = table.row($(this));
        }

        callback(row.data());
    });

    $(tableId + ' tbody').on('click', 'li.' + className, function () {
        const row = table.row($(this));

        callback(row.data());
    });
}

export const TableActionClasses = {
    Edit: 'edit-control',
    Delete: 'delete-control',
    View: 'view-control',
    Resend: 'resend-control',
    Block: 'block-control'
}

export const EmptyObject = {
    label: '',
    className: 'w-20',
}

const tableActionsGeneralObject = {
    orderable: false,
    data: null,
    defaultContent: '',
};

export const TableActionColumns = {
    Edit: {
        ...tableActionsGeneralObject,
        className: TableActionClasses.Edit,
        render: function () {
            return '<span data-toggle="tooltip" title="Editar"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi pointer bi-pencil-fill" fill="#409eff" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>\n' +
                '</svg></span>';
        }
    },
    Delete: {
        ...tableActionsGeneralObject,
        className: TableActionClasses.Delete,
        render: function () {
            return '<span data-toggle="tooltip" title="Apagar"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi pointer bi-trash" fill="#f56c6c" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>\n' +
                '  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>\n' +
                '</svg></span>';
        }
    },
    View: {
        ...tableActionsGeneralObject,
        className: TableActionClasses.View,
        render: function () {
            return '<span data-toggle="tooltip" title="Detalhes"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi pointer bi-person-lines-fill" fill="#6c757d" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm7 1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm2 9a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>\n' +
                '</svg></span>';
        }
    },
    Resend: {
        ...tableActionsGeneralObject,
        className: TableActionClasses.Resend,
        render: function () {
            return '<span data-toggle="tooltip" title="Reenviar Email"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi pointer bi-envelope-fill" fill="#67c23a" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path fill-rule="evenodd" d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z"/>\n' +
                '</svg></span>';
        }
    },
    Block: {
        ...tableActionsGeneralObject,
        className: TableActionClasses.Block,
        render: function (data) {
            if (data.deleted_at) {
                return '<span data-toggle="tooltip" title="Desbloquear"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi pointer bi-lock-fill" fill="#909399" xmlns="http://www.w3.org/2000/svg">\n' +
                    '  <path d="M2.5 9a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2V9z"/>\n' +
                    '  <path fill-rule="evenodd" d="M4.5 4a3.5 3.5 0 1 1 7 0v3h-1V4a2.5 2.5 0 0 0-5 0v3h-1V4z"/>\n' +
                    '</svg></span>';
            }
            return '<span data-toggle="tooltip" title="Bloquear"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi pointer bi-unlock-fill" fill="#909399" xmlns="http://www.w3.org/2000/svg">\n' +
                '  <path d="M.5 9a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2V9z"/>\n' +
                '  <path fill-rule="evenodd" d="M8.5 4a3.5 3.5 0 1 1 7 0v3h-1V4a2.5 2.5 0 0 0-5 0v3h-1V4z"/>\n' +
                '</svg></span>';
        }
    }
}

export const redrawTable = (table, data) => {
    table.clear();
    table.rows.add(data);
    table.draw();
}
