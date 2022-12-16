@use(Mlkali\Sa\Http\Response)
@if($member->permission == "visit")
    @php
        return new Response('/?message=','danger.K zobrazení stránky se musíte přihlásit','#');
    @endphp
@endif
<main id="main">
@if ($article->getArticleChapter() !== null && !empty($article->getArticleChapter()))
    <div class="chapter">
        <h1>{{ $article->getArticleChapter() }}</h1>
    </div>        
@endif
@if ($article->getArticleBody() !== null && !empty($article->getArticleBody()))
    @foreach (json_decode($article->getArticleBody()) as $item)
        {!!  $item  !!}
    @endforeach
@endif
</main>
<nav class="d-flex justify-content-center align-items-center" id="wp_pagnation" style="background-color:#343a40">
    <ul class="pagination" style="margin-top: 2vh;">
        {!!  $pagnition->previous_page()   !!}
        {!!  $pagnition->main_pagnation()  !!}
        {!!  $pagnition->next_page()  !!}
    </ul>
</nav>
