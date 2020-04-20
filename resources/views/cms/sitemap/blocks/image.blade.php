<div class="content-block" data-block_id="{{ $block_id }}" data-block_type="image" id="{{ $block_id }}-container">
    <div class="cb-header">
        <span class="cb-title">Image</span>
        <div class="float-right text-align-right">
            <button class="btn btn-primary btn-sm" onclick="content_block_save('{{ $block_id }}')">Save</button>
            <button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block('{{ $block_id }}')">Delete</button>
        </div>
    </div>

    <input type="hidden" id="{{ $block_id }}-type" value="image" />
    <input type="hidden" id="{{ $block_id }}-new" value="{{ $block_image_content == NULL ? 'yes' : 'no' }}" />
    <div class="image-preview" id="{{ $block_id }}-image-preview">
        {!! $image != NULL ? '<img src="'.asset($image->folder.$image->filename).'" alt="'.$image->name.'">' : '' !!}
    </div>
    <input type="hidden" id="{{ $block_id }}-file_id" value="{{ $image != NULL ? $image->id : 0 }}" />
    <div class="upload-form-container" data-id="{{ $block_id }}"></div>
</div>