require('./bootstrap');
require('./content-block');
require('./jquery.easing.1.3');

$(document).ready(function(){
    $('.toast').toast('show').on('hidden.bs.toast', function (){
        $(this).remove();
    });

    $('.upload-form-container').each(function(){
        $(this).html(input_file_upload($(this).data('id')));
    });
    init_assync_file_upload();

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

    //Animations
    $('.full-width-image-container .text-container, .full-75pc-width-image-container .text-container').fadeIn(300, 'easeIn');
    $('.full-width-image-container .text-container, .full-75pc-width-image-container .text-container').children().each(function(index){
        $(this).hide(0).delay(500 * (index + 1)).fadeIn(300, 'easeIn');     
    });

    $('.text-image-left-container img').css({'opacity': 0}).animate({'margin-left' : '0%', 'opacity': 1}, 350, 'easeIn')
    $('.text-image-right-container img').css({'opacity': 0}).animate({'margin-left' : '0%', 'opacity': 1}, 350, 'easeIn')
});

window.show_toast = function(title, message = '', type = ''){
    let messages = {
        'new-block': {title: 'Action completed', message: 'A new block has been added', type: 'success'},
        'save-block': {title: 'Action completed', message: 'The content for the current block has been saved', type: 'success'},
        'delete-block': {title: 'Action completed', message: 'The selected block has been deleted', type: 'success'},
        'item-save': {title: 'Action completed', message: 'The item data has been saved', type: 'success'},
        'item-published': {title: 'Action completed', message: 'The item published status has been updated', type: 'success'},
        'item-move': {title: 'Action completed', message: 'The item order has been updated', type: 'success'},
        'item-deleted': {title: 'Action completed', message: 'The item has been deleted', type: 'success'},
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

window.input_file_upload = function(id){
    let html = '<form class="form-image-upload form-image-upload-new" data-preview_id="' + id + '" action="javascript:void(0)" enctype="multipart/form-data">';
    html += '<input type="file" name="image" required="" />';
    html += '<input type="hidden" name="type" value="image" />';
    html += '<input type="hidden" name="field_id" value="image" />';
    html += '<button type="submit" class="btn btn-primary mt-2">Upload image</button>';
    html += '</form>';

    return html;
}

window.init_assync_file_upload = function(){
    $('.form-image-upload-new').removeClass('form-image-upload-new').on('submit', (function(e){
        e.preventDefault();
        upload_image(this);
    }));
}

window.upload_image = function(form){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var formData = new FormData(form);
    $.ajax({
        type:'POST',
        url: $('#__upload_route').val(),
        data:formData,
        cache:false,
        contentType: false,
        processData: false,
        success:function(data){
            let target_id = '#' + $(form).data('preview_id');
            $(target_id + '-image-preview').html('<img src="' + data.folder + data.filename + '" alt="' + data.name + '" />');
            $(target_id + '-file_id').val(data.id);
            $(form).trigger("reset");
        },
        error: function(data){
            console.log(data);
        }
    });
}