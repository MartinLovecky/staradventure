@include('includes.head')
@if ($message->hasAny())
    @component('components.message')@endcomponent
@endif
@if($endpoint == 'intro')
    @if($component == 'header')
       @component('components.'.$component)@endcomponent 
    @endif
    <div id="main">
    @if($component != 'header')   
        @component('components.'.$component)@endcomponent
    @endif
    </div>
    </div>
    @include('includes.endOfMainPage')
@elseif($endpoint == 'article')
    @include('includes.menu')
    @if($selector->action == 'show' && !$selector->page)
        @component('master.list_of_articles')@endcomponent
    @elseif($selector->action == 'show' && $selector->page)
        @component('master.show_article')@endcomponent
    @endif
    @component('articles.'.$component)@endcomponent
    @include('includes.footer')
@endif
