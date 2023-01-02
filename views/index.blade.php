@include('master.includes.main_head', ['selector' => $selector])
    {{--Messages --}}
    @if ($message->hasAny())
        @component('component.message', ['message' => $message]) @endcomponent  
    @endif
    {{-- remeber --}}
    @if(isset($cockie['remember']) && $selector->viewName !== 'index')
        @php  $memberController->recallUser($cockie['remember']);  @endphp
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