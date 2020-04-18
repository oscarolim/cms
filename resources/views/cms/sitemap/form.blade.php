@extends('layouts.cms')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {!! JoomLinks::return_button('sitemap') !!}

            @isset($sitemap)
                <h1>Edit page [{{ $sitemap->name }}]</h1>
            @else
                <h1>New page</h1>
            @endisset
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3>Add content</h3>
            <div class="btn-group" role="group" aria-label="Available blocks">
                <button type="button" class="btn btn-secondary" onclick="add_content_block('text')">Text</button>
                <button type="button" class="btn btn-secondary" onclick="add_content_block('image')">Image</button>
                <button type="button" class="btn btn-secondary" onclick="add_content_block('text+image')">Text + Image</button>
            </div>
            <hr />
            <div id="structure">
                {!! JoomForms:: parse_block_configuration($sitemap->structure, $sitemap->content) !!}
            </div>
        </div>
        <div class="col-md-4">
            <form method="POST" action="{{ route('sitemap') }}/{{$sitemap->id ?? ''}}">
                <input type="hidden" id="__route" value="{{ route('sitemap') }}/{{$sitemap->id ?? 0}}" />
                <input type="hidden" id="__upload_route" value="{{ route('upload-file') }}" />
                <input type="hidden" id="__block_key" value="{{ $sitemap->structure_block_key ?? 0}}" />
                @csrf
                @isset($sitemap)
                    @method('PUT')
                @endisset

                <div class="form-group">
                    <label for="name">Page name</label>
                    <input type="text" class="form-control" id="name" name="name" aria-describedby="name" placeholder="Page name" value="{{ old('name', $sitemap->name ?? '') }}" required>
                    <small id="nameHelp" class="form-text text-muted">This name will be used on navigation and page title.</small>
                    @error('name')
                        <small class="form-text text-danger">{{ $errors->first('name') }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="slug">Page slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" aria-describedby="slug" placeholder="Page slug" value="{{ old('slug', $sitemap->slug ?? '') }}">
                    <small id="nameHelp" class="form-text text-muted">This might be modified to be unique. Leave empty to automatically generate by the system.</small>
                    @error('slug')
                        <small class="form-text text-danger">{{ $errors->first('slug') }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="parent_id">Parent section</label>
                    <select class="form-control" id="parent_id" name="parent_id" aria-describedby="parent_id">
                        <option value="">Root (Main page)</option>
                        @foreach($sections as $section_item_id => $section_item)
                            <option value="{{ $section_item_id }}" {{ JoomForms::option_is_selected('parent_id', $section_item_id, $sitemap->parent_id ?? false) }}>{!! $section_item !!}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <small class="form-text text-danger">{{ $errors->first('parent_id') }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="published" id="published" value="1" {{ JoomForms::checkbox_is_checked('published', 1, $sitemap->published ?? false) }}>
                        <label class="form-check-label" for="published">Published</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection