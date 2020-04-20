import Sortable from 'sortablejs';

$(document).ready(function(){
    if($('#structure').length > 0)
    {
        Sortable.create(document.getElementById('structure'),{
            handle: '.cb-header',
            onSort: function (){
                save_block_order();
            },
        });
    }
});

window.add_content_block = function(type){
    let block_id = $('#__block_key').val();
    $('#__block_key').val(parseInt(block_id) + 1);
    block_id = 'block-' + block_id;
    let html = '';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    let formData = new FormData();
    formData.append('_method', 'GET');
    formData.append('block_id', block_id);
    formData.append('type', type);
    $.ajax({
        type:'POST', 
            url: $('#__route').val() + "/block",
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                console.log(data);
                $('#structure').append(data);
                save_block_order();
                $('textarea.newckeditor').removeClass('newckeditor').ckeditor();
                $('.upload-form-container').each(function(){
                    $(this).html(input_file_upload($(this).data('id')));
                });
                init_assync_file_upload();
                show_toast('new-block');
            },
            
            error: function(data){
                console.log(data);
            }
    });
}

window.remove_content_block = function(block_id){
    if(confirm('Do you wish to remove the selected block? All content inside the block will be lost!'))
    {
        $('#' + block_id + '-container').remove();
        save_block_order();
        show_toast('delete-block');
    }
}

window.save_block_order = function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    let structure = {};
    $('#structure .content-block').each(function(index, block){
        structure[index] = {id: $(block).data('block_id'), type: $(block).data('block_type')};
    });
    let formData = new FormData();
    formData.append('structure', JSON.stringify(structure));
    formData.append('block_key', $('#__block_key').val());
    $.ajax({
        type:'POST', 
            url: $('#__route').val() + "/structure",
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                
            },
            
            error: function(data){
                console.log(data);
            }
    });
}

window.content_block_save = function(block_id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
         
    let formData = new FormData();
    formData.append('block-id', block_id)
    formData.append('new-block', $('#' + block_id + '-new').val())
    formData.append('block-type', $('#' + block_id + '-type').val());
    switch($('#' + block_id + '-type').val())
    {
        case 'text':
            formData.append('settings', JSON.stringify({border: $('#' + block_id + '-border').val()}));
            formData.append('text', CKEDITOR.instances[block_id + '-text'].getData());
            break;
        case 'image':
            formData.append('image_id', $('#' + block_id + '-file_id').val());
            break;
        case 'text+image':
            formData.append('text', CKEDITOR.instances[block_id + '-text'].getData());
            formData.append('image_id', $('#' + block_id + '-file_id').val());
            formData.append('settings', JSON.stringify({position: $('#' + block_id + '-image-position').val()}));
            break;
        case 'text+video':
            formData.append('text', CKEDITOR.instances[block_id + '-text'].getData());
            formData.append('video_url', $('#' + block_id + '-video_url').val());
            formData.append('settings', JSON.stringify({position: $('#' + block_id + '-video-position').val()}));
            break;
    }

    $.ajax({
        type:'POST', 
            url: $('#__route').val() + "/block",
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                $('#' + block_id + '-new').val('no');
                show_toast('save-block');
            },
            
            error: function(data){
                console.log(data);
            }
    });
}