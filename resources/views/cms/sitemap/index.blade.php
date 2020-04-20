@extends('layouts.cms')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <h1>Pages</h1>
            {!! JoomLinks::add_button('sitemap', '/create') !!}

            @empty($sitemap)
                <p>No items available</p>
            @else
            <form action="" method="post">
                @csrf
                @method('DELETE')
                
                <table class="table table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Slug</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>

                    {!! JoomForms::table_rows($sitemap, ['name', 'slug'], ['move', 'published', 'edit', 'delete'], route('sitemap'), 'children') !!}
                    </tbody>
                </table>
            </form>
            @endempty
        </div>
    </div>
</div>
@endsection