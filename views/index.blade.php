@include('master.includes.main_head', ['selector' => $selector])
    {{--Messages --}}
    @if ($message->hasAny())  
    @if(isset($cockie['remember']) && $selector->viewName !== 'index')
        @php  $memberController->setMember($cockie['remember']);  @endphp
    @endif
    {{-- Header Links --}}
    @component('component.header')@endcomponent
    {{-- home page component handling --}}
    <div id="main">
    @if ($selector->viewName === 'index' && !empty($selector->action))
        @component('component.'.$selector->action)@endcomponent
    @endif
    </div>
</div>
@include('master.includes.end_page')