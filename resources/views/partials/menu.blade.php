<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle{{ in_array($navigation->slug, $currentRoute) ? ' active' : '' }}" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ $navigation->name }}
    </a>
    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="/{{ $navigation->slug }}">Overview</a>
        @foreach($navigation->published_children as $navigation_item)
            <a class="dropdown-item{{ in_array($navigation_item->slug, $currentRoute) ? ' active' : '' }}" href="/{{ $navigation->slug }}/{{ $navigation_item->slug }}">{{ $navigation_item->name }}</a>
            @if($navigation_item->published_children->count() > 0)
                {{ View::make('.partials.menu-children')->withNavigation($navigation_item)->withPrefix($navigation->slug.'/'.$navigation_item->slug)->withCurrentRoute($currentRoute)}}
            @endif
        @endforeach
    </div>
</li>