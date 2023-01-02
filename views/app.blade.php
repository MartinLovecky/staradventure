{{-- Story, member / editor page --}}
@include('master.includes.head')
@include('master.includes.menu')
    @if($selector->action == 'show' && !$selector->page)
        @component('master.list_of_articles')@endcomponent
    @elseif($selector->action == 'show' && isset($selector->page))
        @component('master.show_article')@endcomponent
    @else
        @component("master.$selector->viewPage")@endcomponent
    @endif
@include('master.includes.footer')