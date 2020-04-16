@extends('layouts.cms')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {!! JoomLinks::return_button('sitemap') !!}

            @isset($sitemap)
                <h1>Edit sitemap section [{{ $sitemap->name }}]</h1>
            @else
                <h1>New sitemap section</h1>
            @endisset

            <form method="POST" action="{{ route('sitemap') }}/{{$sitemap->id ?? ''}}">
                @csrf
                @isset($sitemap)
                    @method('PUT')
                @endisset

                <div class="form-group">
                    <label for="name">Section name</label>
                    <input type="text" class="form-control" id="name" name="name" aria-describedby="name" placeholder="Section name" value="{{ old('name', $sitemap->name ?? '') }}" required>
                    <small id="nameHelp" class="form-text text-muted">This name will be used on navigation and page title.</small>
                    @error('name')
                        <small class="form-text text-danger">{{ $errors->first('name') }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="slug">Section slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" aria-describedby="slug" placeholder="Section slug" value="{{ old('slug', $sitemap->slug ?? '') }}">
                    <small id="nameHelp" class="form-text text-muted">This might be modified to be unique. Leave empty to automatically generate by the system.</small>
                    @error('slug')
                        <small class="form-text text-danger">{{ $errors->first('slug') }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="parent_id">Parent section</label>
                    <select class="form-control" id="parent_id" name="parent_id" aria-describedby="parent_id">
                        <option value="NULL">Root</option>
                        @foreach($sections as $section_item_id => $section_item)
                            <option value="{{ $section_item_id }}">{!! $section_item !!}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <small class="form-text text-danger">{{ $errors->first('parent_id') }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="template_id">Template</label>
                    
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