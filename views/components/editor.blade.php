@use(Mlkali\Sa\Support\Enum)
@if (!$member->logged)
    @redirect('/?message=', Enum::USER_NOT_LOGGED)
@endif
@if (!$member->permission == 'admin' || !$member->permission == 'rewriter')
    @redirect("/member/{$member->username}?message=", Enum::USER_PERMISSION, '#member')
@endif
<article id="edit" style="width: 90vw;">
<div class="article-list">
    <div class="container">
        <div class="row">
            <div class="col-xl-10 offset-xl-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><span class="text-info">Action:</span></li>
                    <li class="breadcrumb-item @if($selector->action == 'create') text-warning @endif"><a href="/create#edit">Create</a></li>
                    <li class="breadcrumb-item @if($selector->action == 'update') text-warning @endif"><a href="/update#edit">Update</a></li>
                    <li class="breadcrumb-item @if($selector->action == 'delete') text-warning @endif"><a href="/delete#edit">Delete</a></li>
                </ol>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><span class="text-info">Příběh:</span></li>    
                    <li class="breadcrumb-item @if($selector->article == 'allwin') text-warning @endif"><a href="{{strtolower($selector->action)}}/allwin/1#edit">Allwin</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'samuel') text-warning @endif"><a href="{{strtolower($selector->action)}}/samuel/1#edit">Samuel</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'isama') text-warning @endif"><a href="{{strtolower($selector->action)}}/isama/1#edit">Isama</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'isamanw') text-warning @endif"><a href="{{strtolower($selector->action)}}/isamanw/1#edit">Isama - NW</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'isamanh') text-warning @endif"><a href="{{strtolower($selector->action)}}/isamanh/1#edit">Isama - NH</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'mry') text-warning @endif"><a href="{{strtolower($selector->action)}}/mry/1#edit">Mr. ?</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'white') text-warning @endif"><a href="{{strtolower($selector->action)}}/white/1#edit">White</a></li>
                    <li class="breadcrumb-item @if($selector->article == 'terror') text-warning @endif"><a href="{{strtolower($selector->action)}}/terror/1#edit">Teror</a></li>
                </ol> 
                @if (!isset($selector->action) || !isset($selector->article))
                    <p class="text-warning">*Zvolte <span class="text-info">Action:</span> a poté <span class="text-info">Příběh:</span></p>
                @elseif(!$article->articleBody && $selector->action != 'create')
                    <h4 class="text-warning">Stránka příběhu: <span class="text-danger fw-bolder">{{  $selector->article  }} {{  $selector->page  }}</span> nexistuje:</h4>
                    <p class="text-info"><a href='/create/{{  $selector->article  }}/{{  $selector->page  }}'> Vytvořit ?</a></p>
                @else
                    @form(['id' => 'myForm'])
                    <span class="text-white">Please use plain styles if you are not sure use: <strong>Odstranit formátování</strong></span>
                    <div class="main-container">
                        <div class="editor-container editor-container_classic-editor editor-container_include-block-toolbar" id="editor-container">
                            <div class="editor-container__editor">
                                <textarea name="content" id="editor">{{  $article->articleBody  }}</textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="{{  $selector->action  }}">
                    <input type="hidden" name="articleName" value="{{  $selector->article  }}">
                    <input type="hidden" name="articlePage" value="{{  $selector->page  }}">
                    <button class="btn btn-success btn-block" value="submit" name="submit" type="submit" style="margin-top:2vh;">Odeslat na server</button>
                    <p class="text-white" style="margin-top:2vh;"> * Pro vykonání jakékoliv akce je nutné kliknout na Odeslat na server nestačí pouze změnit url a dát ENTRER !!!!!</p>
                    </form>
                @endif
                <script type="importmap">
                    {
                        "imports": {
                            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.js",
                            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.0.0/"
                        }
                    }
                </script>
                <script type="module" src="@asset('js/main.js')"></script>
                <script>
                    let editorInstance;
                    // Initialize CKEditor 5
                    ClassicEditor
                        .create(document.querySelector('#editor'))
                        .then(editor => {
                            editorInstance = editor;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                </script>
                </div>
                <hr/>
                @isset($selector->article)
                    <div class="pagination justify-content-center">
                        {!!  $pagnition->previous_page()   !!}
                        {!!  $pagnition->main_pagnation()  !!}
                        {!!  $pagnition->next_page()  !!}
                    </div>            
                @endisset
            </div>
        </div>
    </div>
</div>
</article>