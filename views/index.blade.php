@include('includes.head')
@if ($message->hasAny())
    @include('includes.message')
@endif
{{-- header need be outside <div id="main"> --}}
@if($component == 'header')
    @component('components.header')@endcomponent 
@endif
@if($component != 'header')   
<div id="main">
    @component('components.'.$component)@endcomponent
</div>
@endif
</div>
@include('includes.endPage')