@extends('layouts.frontend')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-6">
                <h1>Contact</h1>
                <p>Would you like to start a chat? Send me an email using the form below.</p>
                
                <form method="POST" action="{{ route('contact') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Your name</label>
                        <input type="text" class="form-control" id="name" name="name" aria-describedby="name" placeholder="Name" value="{{ old('name') }}" required>
                        @error('email')
                            <small class="form-text text-danger">{{ $errors->first('name') }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Your email address</label>
                        <input type="text" class="form-control" id="email" name="email" aria-describedby="email" placeholder="Email address" value="{{ old('email') }}" required>
                        @error('email')
                            <small class="form-text text-danger">{{ $errors->first('email') }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="text">Your message</label>
                        <textarea class="form-control" id="text" name="text" rows="5" aria-describedby="text" placeholder="Your message" required>{{ old('text') }}</textarea>
                        @error('email')
                            <small class="form-text text-danger">{{ $errors->first('text') }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
@endsection
