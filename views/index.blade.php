@include('includes.head')
@if ($message->hasAny())
    @component('components.message')@endcomponent
@endif
{{-- 
    endpoint split application into 2 parts 
        frist is 'intro' that can be sum as landing page and its "elemets" inside /views/components
        second is 'article' where user iteractive with (/show,/update,/delete/, /member) inside /views/articles
--}}
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
    @component('articles.'.$component)@endcomponent
    @include('includes.footer')
@endif
