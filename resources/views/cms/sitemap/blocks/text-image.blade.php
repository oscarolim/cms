<div class="content-block" data-block_id="{{ $block_id }}" data-block_type="text+image" id="{{ $block_id }}-container">
    <div class="cb-header">
        <span class="cb-title">Text + Image</span>
        <div class="float-right text-align-right">
            <button class="btn btn-primary btn-sm" onclick="content_block_save('{{ $block_id }}')">Save</button>
            <button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block('{{ $block_id }}')">Delete</button>
        </div>
    </div>

    <input type="hidden" id="{{ $block_id }}-type" value="text+image" />
    <input type="hidden" id="{{ $block_id }}-new" value="{{ $block_text_content == NULL && $block_image_content == NULL ? 'yes' : 'no' }}" />
    <div class="form-group">
        <label for="{{ $block_id }}-image-position">Image position</label>
        <select id="{{ $block_id }}-image-position" class="form-control">
            <option value="left"{{ $settings['position'] == 'left' ? ' selected' : '' }}>Left</option>
            <option value="right"{{ $settings['position'] == 'right' ? ' selected' : '' }}>Right</option>
            <option value="full"{{ $settings['position'] == 'full' ? ' selected' : '' }}>Full width (text over image, centred)</option>
            <option value="full-75pc"{{ $settings['position'] == 'full-75pc' ? ' selected' : '' }}>Full width, 75pc height</option>
        </select>
    </div>
    <div class="form-group">
        <textarea class="form-control ckeditor{{ $block_text_content == NULL ? ' newckeditor' : '' }}" rows="5" name="{{ $block_id }}-text" id="{{ $block_id }}-text">{{ $block_text_content->block_content ?? '' }}</textarea>
    </div>

    <div class="image-preview" id="{{ $block_id }}-image-preview">
        {!! $image != NULL ? '<img src="'.asset($image->folder.$image->filename).'" alt="'.$image->name.'">' : '' !!}
    </div>
    <input type="hidden" id="{{ $block_id }}-file_id" value="{{ $image != NULL ? $image->id : 0 }}" />
    <div class="upload-form-container" data-id="{{ $block_id }}"></div>
</div>