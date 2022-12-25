@use(Mlkali\Sa\Http\Response)
@if($member->permission == "visit")
    @php
        return new Response('/?message=','danger.K zobrazení stránky se musíte přihlásit','#');
    @endphp
@endif
<div class="book">
	<div class="container">
		<div class="text"> 
			@if ($article->getArticleChapter() !== null && !empty($article->getArticleChapter())) 
			<h1>{{ $article->getArticleChapter() }}</h1> 
			@endif 
			@if ($article->getArticleBody() !== null && !empty($article->getArticleBody())) 
			@foreach (json_decode($article->getArticleBody()) as $item) 
			{!! $item !!} 
			@endforeach 
			@endif 
		</div>
	</div>
</div>
<div class="pagination">
	{!!  $pagnition->previous_page()   !!}
	{!!  $pagnition->main_pagnation()  !!}
	{!!  $pagnition->next_page()  !!}
</div>
