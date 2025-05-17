@use(Mlkali\Sa\Support\Messages)
@if(!$selector->page)
@redirect('/storylist#storylist')
@elseif($member->permission == "visit")
@redirect('/?message=', Messages::USER_PERMISSION)
@else
<article id="story" style="width: 80vw;">
    @include('includes.menu')
    <div class="book">
        <div class="container-story">
            <div class="text">
                @if ($article->articleBody)
                {!! $article->articleBody !!}
                @endif
            </div>
        </div>
        <nav aria-label="...">
            <ul class="pagination justify-content-center">
                {!! $pagnition->previous() !!}
                {!! $pagnition->main() !!}
                {!! $pagnition->next() !!}
            </ul>
        </nav>
    </div>
</article>
@endif
