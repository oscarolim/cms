import Sortable from 'sortablejs';

$(document).ready(function(){
    if($('#structure').length > 0)
    {
        Sortable.create(document.getElementById('structure'),{
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
    let html_header_buttons = '<button class="btn btn-primary btn-sm" onclick="content_block_save(\'' + block_id + '\')">Save</button>';
    html_header_buttons += '<button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block(\'' + block_id + '\')">Delete</button>';
    let html = '';
    switch(type)
    {
        case 'text':
            html = '<div class="content-block" data-block_id="' + block_id + '" data-block_type="text" id="' + block_id + '-container">';
            html += '<div class="cb-header"><span class="cb-title">Text</span><div class="float-right text-align-right">' + html_header_buttons + '</div></div>';
            html += '<input type="hidden" id="' + block_id + '-type" value="text" />';
            html += '<input type="hidden" id="' + block_id + '-new" value="yes" />';
            html += '<textarea class="form-control ckeditor newckeditor" rows="5" name="' + block_id + '-text" id="' + block_id + '-text"></textarea>';
            html += '</div>';
            break;
        case 'image':
            html = '<div class="content-block" data-block_id="' + block_id + '" data-block_type="image" id="' + block_id + '-container">';
            html += '<div class="cb-header"><span class="cb-title">Image</span><div class="float-right text-align-right">' + html_header_buttons + '</div></div>';
            html += '<input type="hidden" id="' + block_id + '-type" value="image" />';
            html += '<input type="hidden" id="' + block_id + '-new" value="yes" />';
            html += '<div class="image-preview" id="' + block_id + '-image-preview"></div>';
            html += '<input type="hidden" id="' + block_id + '-file_id" value="0" />';
            html += input_file_upload(block_id);
            html += '</div>';
            break;
        case 'text+image':
            html = '<div class="content-block" data-block_id="' + block_id + '" data-block_type="text+image" id="' + block_id + '-container">';
            html += '<div class="cb-header"><span class="cb-title">Text + Image</span><div class="float-right text-align-right">' + html_header_buttons + '</div></div>';
            html += '<input type="hidden" id="' + block_id + '-type" value="text+image" />';
            html += '<input type="hidden" id="' + block_id + '-new" value="yes" />';
            html += '<div class="form-group"><label for="' + block_id + '-image-position">Image position</label>';
            html += '<select id="' + block_id + '-image-position" class="form-control">';
            html += '<option value="left">Left</value>';
            html += '<option value="right">Right</value>';
            html += '<option value="full">Full width (text over image, centred)</value>';
            html += '</select></div>';
            html += '<div class="form-group"><textarea class="form-control ckeditor newckeditor" rows="5" name="' + block_id + '-text" id="' + block_id + '-text"></textarea></div>';
            html += '<div class="image-preview" id="' + block_id + '-image-preview"></div>';
            html += '<input type="hidden" id="' + block_id + '-file_id" value="0" />';
            html += input_file_upload(block_id);
            html += '</div>';
            break;
    }

    $('#structure').append(html);
    save_block_order();
    $('textarea.newckeditor').removeClass('newckeditor').ckeditor();
    init_assync_file_upload();
    show_toast('new-block');
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