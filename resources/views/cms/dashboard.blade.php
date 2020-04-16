@extends('layouts.cms')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            Hello {{ Auth::user()->name }}
        </div>
    </div>
</div>
@endsection