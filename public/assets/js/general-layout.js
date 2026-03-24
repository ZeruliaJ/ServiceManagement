$(document).ready(function(){
    $("#alert").fadeTo(5000, 500).slideUp(1000, function(){
        $("#alert").slideUp(100);
    });
    $(document).on('click','.delete-record',function(){
        var route = $(this).attr('data-route');
        Swal.fire({
                    title: window.Lang.are_you_sure,
                    text: window.Lang.wont_be_able_to_revert,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: window.Lang.yes_delete_it
                    }).then((result) => {
                    if (result.isConfirmed)
                    {
                        $.ajax({
                                url: route,
                                method: 'POST',
                                headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response)
                                {

                                    if(response.success == 1)
                                    {
                                        Swal.fire({
                                                title: window.Lang.deleted,
                                                text: window.Lang.record_deleted_successfully,
                                                icon: "success"
                                            });
                                        $('#dataTable').DataTable().ajax.reload();
                                    }
                                    else
                                    {
                                        Swal.fire({
                                                title: window.Lang.oops,
                                                text: window.Lang.something_went_wrong,
                                                icon: "error"
                                            });
                                    }
                                }
                            });
                    }
                });
    });

    $(document).on('click','.change-status',function(){
        var route = $(this).attr('data-route');
        Swal.fire({
                    title: window.Lang.are_you_sure,
                    text: window.Lang.want_to_change_status,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: window.Lang.yes_change_it
                    }).then((result) => {
                    if (result.isConfirmed)
                    {
                        $.ajax({
                                url: route,
                                method: 'POST',
                                headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response)
                                {

                                    if(response.success == 1)
                                    {
                                        Swal.fire({
                                                title: window.Lang.changed,
                                                text: window.Lang.status_changed_successfully,
                                                icon: "success"
                                            });
                                        $('#dataTable').DataTable().ajax.reload();
                                    }
                                    else
                                    {
                                        Swal.fire({
                                                title: window.Lang.oops,
                                                text: window.Lang.something_went_wrong,
                                                icon: "error"
                                            });
                                    }
                                }
                            });
                    }
                });
    });
    refreshSelectBox();

    var firstSwitcher = document.querySelector('.language-switcher');
    if (firstSwitcher) {
        var currentLocale = firstSwitcher.dataset.currentLocale;
        document.querySelectorAll('.lang-slider').forEach(function(slider) {
            var index = (currentLocale === 'sw') ? 0 : 1;
            slider.style.transform = 'translateX(' + (index * 100) + '%)';
        });
    }
});
function refreshSelectBox()
{
    $('.ajax-endpoint').each(function() {
        var $this = $(this);
        var endpoint = $this.data('endpoint');
        var placeholder = $this.data('placeholder');
        var field1 = $this.attr('data-field1-id');
        var field2 = $this.attr('data-field2-id');
        $this.select2({
            ajax: {
                url: endpoint,
                dataType: 'json',

                data: function(params) {
                    var data = {
                        search: params.term,
                        field1: field1,
                        field2: field2
                    };
                    return data;
                },
                processResults: function(response) {
                    return {
                        results: response.data.map(function(item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                }
            },
            minimumInputLength: 0,
            placeholder: placeholder,
        });
    });
}
function destroySelect2($this, $placeholder)
{
    var field1 = $this.attr('data-field1-id');
    if ($this.hasClass("select2-hidden-accessible"))
    {
        $this.select2('destroy');
    }

    $this.select2({
        ajax: {
            url: $this.data('endpoint'),
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || '',
                    field1: field1
                };
            },
            processResults: function (response) {
                return {
                    results: (response.data || []).map(function (item) {
                        return { id: item.id, text: item.name };
                    })
                };
            }
        },
        placeholder: $placeholder,
        allowClear: true,
        minimumInputLength: 0
    });
}

$(document).on('click', '.add-row', function(e) {
    e.preventDefault();
    var $row = $(this).closest('tr');
    var $clone = $row.clone();

    $clone.find('input').val('');
    $clone.find('select').val('').trigger('change');
    $clone.find('.select2-container').remove();
    $clone.find('select').select2();
    $row.after($clone);
    $('.js-example-basic-single').select2();
    refreshSelectBox();
});

$(document).on('click', '.remove-row', function () {
    let $tbody = $(this).closest('tbody');

    if ($tbody.find('tr').length === 1)
    {
        Swal.fire({
                    text: window.Lang.atleast_one_row_required,
                    icon: "info"
                });
        return;
    }

    $(this).closest('tr').remove();
});
