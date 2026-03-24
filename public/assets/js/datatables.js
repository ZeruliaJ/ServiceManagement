$(function (e) {

    // Set global DataTable language defaults
    if (window.Lang) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                emptyTable: window.Lang.dt_empty_table,
                info: window.Lang.dt_info,
                infoEmpty: window.Lang.dt_info_empty,
                infoFiltered: window.Lang.dt_info_filtered,
                lengthMenu: window.Lang.dt_length_menu,
                loadingRecords: window.Lang.dt_loading_records,
                processing: window.Lang.dt_processing,
                search: window.Lang.dt_search,
                searchPlaceholder: window.Lang.dt_search_placeholder,
                zeroRecords: window.Lang.dt_zero_records,
                paginate: {
                    first: window.Lang.dt_first,
                    last: window.Lang.dt_last,
                    next: window.Lang.dt_next,
                    previous: window.Lang.dt_previous
                },
                aria: {
                    sortAscending: window.Lang.dt_sort_ascending,
                    sortDescending: window.Lang.dt_sort_descending
                }
            }
        });
    }

    // basic datatable
    $('#datatable-basic').DataTable({
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
        "pageLength": 10,
        // scrollX: true
    });
    // basic datatable

    // responsive datatable
    $('#responsiveDataTable').DataTable({
        responsive: true,
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
        "pageLength": 10,
    });
    // responsive datatable

    // responsive modal datatable
    $('#responsivemodal-DataTable').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data[0] + ' ' + data[1];
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        },
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
        "pageLength": 10,
    });
    // responsive modal datatable

    // file export datatable
    $('#file-export').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
        scrollX: true
    });
    // file export datatable

    // delete row datatable
    var table = $('#delete-datatable').DataTable({
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        }
    });
    $('#delete-datatable tbody').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });
    $('#button').on("click", function () {
        table.row('.selected').remove().draw(false);
    });
    // delete row datatable

    // scroll vertical
    $('#scroll-vertical').DataTable({
        scrollY: '265px',
        scrollCollapse: true,
        paging: false,
        scrollX: true,
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
    });
    // scroll vertical

    // hidden columns
    $('#hidden-columns').DataTable({
        columnDefs: [
            {
                target: 2,
                visible: false,
                searchable: false,
            },
            {
                target: 3,
                visible: false,
            },
        ],
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
        "pageLength": 10,
        // scrollX: true
    });
    // hidden columns
    
    // add row datatable
    var t = $('#add-row').DataTable({
        
        language: {
            searchPlaceholder: window.Lang ? window.Lang.dt_search_placeholder : 'Search...',
            sSearch: '',
        },
    });
    var counter = 1;
    $('#addRow').on('click', function () {
        t.row.add([counter + '.1', counter + '.2', counter + '.3', counter + '.4', counter + '.5']).draw(false);
        counter++;
    });
    // add row datatable

});
