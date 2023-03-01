@use(Mlkali\Sa\Http\Response)
@use(Mlkali\Sa\Support\Enum)
@set($selector = $container->get(Mlkali\Sa\Support\Selector::class))
@set($member = $container->get(Mlkali\Sa\Database\Entity\Member::class))
@set($article = $container->get(Mlkali\Sa\Database\Entity\Article::class))
@set($message = $container->get(Mlkali\Sa\Support\Messages::class))
@set($pagnation = $container->get(Mlkali\Sa\Html\Pagnition::class))
@set($request = $container->get(Mlkali\Sa\Http\Request::class))
@set($articleController = $container->get(Mlkali\Sa\Controllers\ArticleController::class))
@if (!$member->logged)
    @php
        return new Response('/?message=', Enum::USER_NOT_LOGGED, '#');
    @endphp 
@endif
@if (!$member->permission == 'admin' || !$member->permission == 'rewriter')
    @php
        return new Response('/member'.'/'.$member->username.'?message=', Enum::USER_PERMISSION);
    @endphp 
@endif
@if($message->hasAny())
    @component('component.message')@endcomponent
@endif
<div class="article-list">
    <div class="container">
        <div class="row">
            <div class="col-xl-10 offset-xl-1">
                <ol class="breadcrumb" style="margin-top:3vh;">
                    <li class="breadcrumb-item"><span class="text-info">Action:</span></li>
                    <li class="breadcrumb-item"><a href="/create">Create</a></li>
                    <li class="breadcrumb-item"><a href="/update">Update</a></li>
                    <li class="breadcrumb-item"><a href="/delete">Delete</a></li>
                <ol>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><span class="text-info">Příběh:</span></li>    
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/allwin/1">Allwin</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/samuel/1">Samuel</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/isama/1">Isama</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/isamanw/1">Isama - NW</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/isamanh/1">Isama - NH</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/mry/1">Mr. ?</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/white/1">White</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/terror/1">Teror</a></li>
                </ol>
                @if($selector->article)
                    <span class="text-white">Aktivní action: <span class="text-info">{{strtoupper($selector->action)}}</span></span><br>
                    <span class="text-white">Aktivní příběh: <span class="text-info">{{strtoupper($selector->article)}} | {{$selector->page}}</span></span><br>
                    <span class="text-white"><span style="color:#99badd;">#99badd</span> (použít na postavy)<br>
                    <span class="text-white"><span style="color:#009933;">#009933</span> (použít na důležité)
                 @endif
                {!!
                    $form->options(['target'=>'requestHandler'])
                        ->vars(['articleController'=>$articleController, 'request' => $request])
                        ->run()
                !!}
                @if (!isset($selector->action) || !isset($selector->page))
                    <p class="text-warning">*Zvolte <span class="text-info">Action:</span> a poté <span class="text-info">Příběh:</span></p>
                @endif
                @isset($selector->article)
                <div id="toolbar-container" style="margin-top:2vh;"></div>
                <div id="editor">
                @foreach (json_decode($article->getArticleBody()) as $body)
                    <label class="text-white">Nadpis:</label><input type="text" name="chapter"  placeholder="Může zůstat prázdný">
                    <textarea name="editor1">
                    {{  $body }}
                    </textarea>
                @endforeach
                @endisset
                <script>
                    CKEDITOR.replace('editor1');
                </script>
                </div>
                <hr/>
                @isset($selector->article)
                    <div class="pagination">
                        {!!  $pagnition->previous_page()   !!}
                        {!!  $pagnition->main_pagnation()  !!}
                        {!!  $pagnition->next_page()  !!}
                    </div>            
                    <input type="hidden" name="type" value="{{$selector->action}}">
                @endisset
                <button class="btn btn-success btn-block" value="submit" name="submit" type="submit" style="margin-top:2vh;">Odeslat na server</button>
                <p class="text-white" style="margin-top:2vh;"> * Pro vykonání jakékoliv akce je nutné kliknout na Odeslat na server nestačí pouze změnit url a dát ENTRER !!!!!</p>
                </form>
            </div>
        </div>
    </div>
</div>