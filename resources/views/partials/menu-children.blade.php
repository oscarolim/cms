<div class="dropdown-divider"></div>
@foreach($navigation->published_children as $navigation_item)
    <a class="dropdown-item{{ in_array($navigation_item->slug, $currentRoute) ? ' active' : '' }}" href="/{{ $prefix }}/{{ $navigation_item->slug }}">{{ $navigation_item->name }}</a>
    @if($navigation_item->published_children->count() > 0)
        {{ View::make('.partials.menu-children')->withNavigation($navigation_item)->withPrefix($prefix.'/'.$navigation_item->slug)->withCurrentRoute($currentRoute)}}
    @endif
@endforeach
<div class="dropdown-divider"></div>