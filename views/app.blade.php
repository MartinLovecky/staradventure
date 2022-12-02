@include('master.includes.head')
@if ($selector->action != 'show')    
    @include('master.includes.menu')
@endif
@include("master.pages.$selector->viewPage") 
@include('master.includes.footer')