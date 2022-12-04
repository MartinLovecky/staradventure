@include('master.includes.main_head', ['selector' => $selector])
    {{--Messages --}}
    @isset($_POST['submit'])
        @dump($requestController->submitRegister())
    @endisset
    @if ($message->hasAny())
        @component('component.message', ['message' => $message]) @endcomponent  
    @endif
    {{-- Header Links --}}
    @component('component.header')@endcomponent
    <div id="main">
    {{-- intro --}}    
    @if ($selector->action === 'intro')
        @component('component.intro')@endcomponent
    {{-- register --}}    
    @elseif ($selector->action === 'register')
        @component('component.register', 
            [
            'form' => $form, 
            'requestController' => $requestController
            ])
        @endcomponent    
    {{-- login --}}
    @elseif($selector->action === 'login')
        @component('component.login', 
            [
            'form' => $form, 
            'requestController' => $requestController
            ])
        @endcomponent
    {{-- vop --}}
    @elseif($selector->action === 'vop')
        @component('component.vop')@endcomponent
    {{-- terms --}}
    @elseif($selector->action === 'terms')
        @component('component.terms')@endcomponent
    {{-- reset --}}
    @elseif($selector->action === 'reset')
        @component('component.reset', 
            [
                'form' => $form, 
                'requestController' => $requestController, 
                'query' => $selector->fristQueryValue
            ])
        @endcomponent
    {{-- new password --}}         
    @elseif ($selector->action === 'newpassword')
        @component('component.new_password', 
            [
                'form' => $form, 
                'requestController' => $requestController, 
                'query' => $selector->fristQueryValue
            ])
        @endcomponent
    {{-- storylist --}}
    @elseif ($selector->action === 'storylist')
        @component('component.story_list')@endcomponent
    {{-- update member --}}
    @elseif ($selector->action === 'updatemember')
        @component('component.update_member', 
            [
                'form' => $form, 
                'requestController' => $requestController,
                'query' => $selector->fristQueryValue
            ])
        @endcomponent
    {{-- Save bookmark --}}    
    @elseif ($selector->action === 'savebookmark')
        @component('component.save_bookmark')@endcomponent
    @endif
    </div>
    {{-- Footer --}}
    @component('component.footer')@endcomponent
</div>
@include('master.includes.end_page')