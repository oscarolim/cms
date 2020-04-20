<div class="content-block" data-block_id="{{ $block_id }}" data-block_type="text+video" id="{{ $block_id }}-container">
    <div class="cb-header">
        <span class="cb-title">Text + Video</span>
        <div class="float-right text-align-right">
            <button class="btn btn-primary btn-sm" onclick="content_block_save('{{ $block_id }}')">Save</button>
            <button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block('{{ $block_id }}')">Delete</button>
        </div>
    </div>

    <input type="hidden" id="{{ $block_id }}-type" value="text+video" />
    <input type="hidden" id="{{ $block_id }}-new" value="{{ $block_text_content == NULL && $block_video_content == NULL ? 'yes' : 'no' }}" />
    <div class="form-group">
        <label for="{{ $block_id }}-video-position">Video position</label>
        <select id="{{ $block_id }}-video-position" class="form-control">
            <option value="left"{{ $settings['position'] == 'left' ? ' selected' : '' }}>Left</option>
            <option value="right"{{ $settings['position'] == 'right' ? ' selected' : '' }}>Right</option>
            <option value="top"{{ $settings['position'] == 'top' ? ' selected' : '' }}>Top</option>
            <option value="bottom"{{ $settings['position'] == 'bottom' ? ' selected' : '' }}>Bottom</option>
        </select>
    </div>
    <div class="form-group">
        <textarea class="form-control ckeditor{{ $block_text_content == NULL ? ' newckeditor' : '' }}" rows="5" name="{{ $block_id }}-text" id="{{ $block_id }}-text">{{ $block_text_content->block_content ?? '' }}</textarea>
    </div>
    <div class="form-group">
        <label for="{{ $block_id }}-video_url">Video embed link</label>
        <input type="text" class="form-control" name="{{ $block_id }}-video_url" id="{{ $block_id }}-video_url" value="{{ $block_video_content->block_content ?? '' }}" />
    </div>
</div>