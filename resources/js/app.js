require('./bootstrap');

$(document).ready(function(){
    $('.table-action-delete').click(function(e)
    {
        e.preventDefault();
        if(confirm('Do you wish to delete the data?'))
        {
            ($(this).closest('form').find('input[name="_method"]')).attr('value', 'DELETE');
            $(this).closest('form').attr('action', $(this).data('action')).submit();
        }
    });

    $('.table-action-published').click(function(e)
    {
        e.preventDefault();
        ($(this).closest('form').find('input[name="_method"]')).attr('value', 'PUT');
        $(this).closest('form').attr('action', $(this).data('action')).submit();
    });
});