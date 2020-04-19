<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-light bg-info navbar-expand-md shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        @foreach($navigation as $navigation_item)
                            @if($navigation_item->published_children->count() == 0)
                                <li class="nav-item"><a class="nav-link{{ in_array($navigation_item->slug, $current_route) ? ' active' : '' }}" href="/{{ $navigation_item->slug }}">{{ $navigation_item->name }}</a></li>
                            @else
                                {{ View::make('.partials.menu')->withNavigation($navigation_item)->withCurrentRoute($current_route)}}
                            @endif
                        @endforeach
                        
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <a class="btn btn-warning" target="_blank" href="mailto:me@oscarolim.com?subject=Contact">Email me!</a>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="mb-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
