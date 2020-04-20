<div class="content-block" data-block_id="{{ $block_id }}" data-block_type="text" id="{{ $block_id }}-container">
    <div class="cb-header">
        <span class="cb-title">Text</span>
        <div class="float-right text-align-right">
            <button class="btn btn-primary btn-sm" onclick="content_block_save('{{ $block_id }}')">Save</button>
            <button class="btn btn-danger btn-sm ml-2" onclick="remove_content_block('{{ $block_id }}')">Delete</button>
        </div>
    </div>

    <input type="hidden" id="{{ $block_id }}-type" value="text" />
    <input type="hidden" id="{{ $block_id }}-new" value="{{ $block_text_content == NULL ? 'yes' : 'no' }}" />
    <div class="form-group">
        <label for="{{ $block_id }}-border">Border</label>
        <select id="{{ $block_id }}-border" class="form-control">
            <option value="none"{{ ($settings['border'] ?? '') == 'none' ? ' selected' : ''}}>None</option>
            <option value="top"{{ ($settings['border'] ?? '') == 'top' ? ' selected' : ''}}>Top</option>
        </select>
    </div>
    <textarea class="form-control ckeditor{{ $block_text_content == NULL ? ' newckeditor' : '' }}" rows="5" name="{{ $block_id }}-text" id="{{ $block_id }}-text">{{ $block_text_content->block_content ?? '' }}</textarea>
</div>