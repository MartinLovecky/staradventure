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
    @component('articles.'.$component)@endcomponent
    @include('includes.footer')
@endif
