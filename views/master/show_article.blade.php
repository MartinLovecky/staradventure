@use(Mlkali\Sa\Http\Response)
@use(Mlkali\Sa\Support\Enum)
@set($article = $container->get(Mlkali\Sa\Database\Entity\Article::class))
@set($pagnation = $container->get(Mlkali\Sa\Html\Pagnition::class))
@set($member = $container->get(Mlkali\Sa\Database\Entity\Member::class))
@if($member->permission == "visit")
    @php
        return new Response('/?message=', Enum::USER_PERMISSION, '#');
    @endphp
@endif
<div class="book">
	<div class="container-story">
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
	<div class="pagination">
		{!!  $pagnition->previous_page()   !!}
		{!!  $pagnition->main_pagnation()  !!}
		{!!  $pagnition->next_page()  !!}
	</div>
</div>
