@if(is_array($memberController->loginWithRememberCookie()))
    @redirect("/member/{{$member['username']}}")
@endif
@include('includes.head')
@if($message->hasAny())
    @include('includes.message')
@endif
{{-- header need be outside <div id="main"> --}}
@if($component === 'header')
    @component('components.header')@endcomponent 
@else  
<div id="main">
    @component('components.'.$component)@endcomponent
</div>
@endif
</div>
@include('includes.end')