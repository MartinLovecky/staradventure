@use(Mlkali\Sa\Support\Enum)
@if (!$member->logged)
    @redirect('/?message=', Enum::USER_NOT_LOGGED)
@endif
@if (!$member->permission == 'admin' || !$member->permission == 'rewriter')
    @redirect("/member/{$member->username}?message=", Enum::USER_PERMISSION, '#member')
@endif
<div class="article-list">
    <div class="container">
        <div class="row">
            <div class="col-xl-10 offset-xl-1">
                <ol class="breadcrumb" style="margin-top:3vh;">
                    <li class="breadcrumb-item"><span class="text-info">Action:</span></li>
                    <li class="breadcrumb-item"><a href="/create#editor">Create</a></li>
                    <li class="breadcrumb-item"><a href="/update#editor">Update</a></li>
                    <li class="breadcrumb-item"><a href="/delete#editor">Delete</a></li>
                <ol>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><span class="text-info">Příběh:</span></li>    
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/allwin/1#story">Allwin</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/samuel/1#story">Samuel</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/isama/1#story">Isama</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/isamanw/1#story">Isama - NW</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/isamanh/1#story">Isama - NH</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/mry/1#story">Mr. ?</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/white/1#story">White</a></li>
                    <li class="breadcrumb-item"><a href="{{strtolower($selector->action)}}/terror/1#story">Teror</a></li>
                </ol>
                @if($selector->article)
                    <span class="text-white">Aktivní action: <span class="text-info">{{  strtoupper($selector->action)  }}</span></span><br>
                    <span class="text-white">Aktivní příběh: <span class="text-info">{{  strtoupper($selector->article)}} | {{$selector->page  }}</span></span><br>
                    <span class="text-white"><span style="color:#99badd;">#99badd</span> (použít na postavy)<br>
                    <span class="text-white"><span style="color:#009933;">#009933</span> (použít na důležité)
                 @endif
                 {{-- //TODO -  still need to be changed and tested --}}
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
                    <input type="hidden" name="type" value="{{  $selector->action  }}">
                @endisset
                <button class="btn btn-success btn-block" value="submit" name="submit" type="submit" style="margin-top:2vh;">Odeslat na server</button>
                <p class="text-white" style="margin-top:2vh;"> * Pro vykonání jakékoliv akce je nutné kliknout na Odeslat na server nestačí pouze změnit url a dát ENTRER !!!!!</p>
                </form>
            </div>
        </div>
    </div>
</div>