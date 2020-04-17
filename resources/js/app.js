require('./bootstrap');
require('./content-block');

$(document).ready(function(){
    $('.toast').toast('show').on('hidden.bs.toast', function (){
        $(this).remove();
    });

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

    $('.table-action-move').click(function(e)
    {
        e.preventDefault();
        ($(this).closest('form').find('input[name="_method"]')).attr('value', 'PUT');
        $(this).closest('form').attr('action', $(this).data('action')).submit();
    });
});

window.show_toast = function(title, message = '', type = ''){
    let messages = {
        'new-block': {title: 'Action completed', message: 'A new block has been added', type: 'success'},
        'save-block': {title: 'Action completed', message: 'The content for the current block has been saved', type: 'success'},
        'delete-block': {title: 'Action completed', message: 'The selected block has been deleted', type: 'success'}
    };

    if(messages[title] != undefined)
    {
        message = messages[title].message;
        type = messages[title].type;
        title = messages[title].title;
    }

    let html = '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="4000">';
    html += '<div class="toast-header"><div class="square rounded alert-' + type + ' px-2 mr-2">&nbsp;</div><strong class="mr-auto">' + title + '</strong>';
    html += '<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    html += '<div class="toast-body">' + message + '</div></div>';

    $('#toast-container').append(html);
    $('.toast').toast('show').on('hidden.bs.toast', function (){
        $(this).remove();
    });
}