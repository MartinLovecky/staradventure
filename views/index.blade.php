@include('includes.head', ['selector' => $selector,])

@if ($message->hasAny())
@component('components.message', ['message' => $message])@endcomponent
@endif

@if($component == 'register' || $component == 'reset' || $component == 'login')
@set($data = [
    'request' => $request,
    'member' => $member,
    'form' => $form,
    'memberController' => $memberController,
    'enc' => $enc,
    'csrf' => $csrf,
    'selector' => $selector
])
@else
@set($data = [])
@endif
{{-- Main landing page --}}
@if ($component == 'index')
    @component('components.header', ['member' => $member])@endcomponent
    <div id="main">
        @if (!empty($selector->action))
            @component('components.'.$component, $data)@endcomponent
        @endif
    </div>
    </div>
    @include('includes.endOfMainPage')
@else
@include('includes.menu')
@if($selector->action == 'show' && !$selector->page)
    @component('articles.'.$component)@endcomponent
@elseif($selector->action == 'show' && isset($selector->page))
    @component('articles.'.$component)@endcomponent
@else
    @component('articles.'.$component)@endcomponent
@endif

@include('includes.footer')

@endif