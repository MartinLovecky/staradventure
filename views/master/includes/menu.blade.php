<nav class="navbar navbar-light navbar-expand-lg">
    <div class="container"><a class="navbar-brand" href="/"><img class="img-fluid" src="@asset('img/android-chrome-256x256.png')" style="height: 70px;width: 70px;margin-right: -20px;" alt="brand" width="70" height="70">tarAdventure</a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-1" type="button"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse text-center" id="navcol-1">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link @if($selector->article == 'allwin'){{'active'}}@endif" href="/show/allwin/1">Allwin</a></li>
                <li class="nav-item"><a class="nav-link @if($selector->article == 'samuel'){{'active'}}@endif" href="/show/samuel/1">Samuel</a></li>
                <li class="nav-item dropdown" style="margin-top: 2vh;"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" data-bs-auto-close="outside" href="#" style="margin: auto;margin-top: -2vh;">Isama&nbsp;</a>
                    <div class="dropdown-menu text-center bg-secondary" id="isama-drop">
                        <a class="dropdown-item @if($selector->article == 'isama'){{'active'}}@endif" href="/show/isama/1">Isama</a>
                        <a class="dropdown-item @if($selector->article == 'isamanh'){{'active'}}@endif" href="/show/isamanh/1">Nový horizont</a>
                        <a class="dropdown-item @if($selector->article == 'isamanw'){{'active'}}@endif" href="/show/isamanw/1">Nový vesmír</a>
                    </div>
                </li>
                <li class="nav-item dropend" style="margin-top: 2vh;"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" data-bs-auto-close="outside" href="#" style="margin: auto;margin-top: -2vh;">Ostatní</a>
                    <div class="dropdown-menu text-center bg-secondary" id="else-drop">
                        <a class="dropdown-item @if($selector->article == 'angel'){{'active'}}@endif" href="/show/angel/1">Angel & Eklips</a>
                        <a class="dropdown-item @if($selector->article == 'mry'){{'active'}}@endif" href="/show/mry/1">Mr. ?</a>
                        <a class="dropdown-item @if($selector->article == 'white'){{'active'}}@endif" href="/show/white/1">White Star</a>
                        <a class="dropdown-item @if($selector->article == 'terror'){{'active'}}@endif" href="/show/terror/1">Terror</a>
                        <a class="dropdown-item @if($selector->article == 'hyperion'){{'active'}}@endif" href="/show/hyperion/1">Hyperion</a>
                        <a class="dropdown-item @if($selector->article == 'demoni'){{'active'}}@endif" href="/show/demoni/1">Démoni</a> 
                    </div>
                </li>
            </ul>
        </div>
        @if ($member->logged)
        <div class="container d-flex justify-content-center justify-content-xl-end" id="menu">
            <img class="img-fluid rounded-circle" style="width: 70px;height: 70px;margin-right: 1vh;margin-top: -4vh;" src="@asset('img/avatars/'.$member->avatar)">
            <div>
                <div class="dropdown ms-auto" style="margin-top: 2vh;"><a class="dropdown-toggle" aria-expanded="false" data-bs-toggle="dropdown" data-bs-auto-close="outside" href="#" style="margin: auto;">{{$member->username}}</a>
                    <div class="dropdown-menu text-center bg-secondary" style="margin-left: -5vh;">
                        <a class="dropdown-item" href="/member/{{$member->username}}">Profil</a>
                        <a class="dropdown-item" href="/updatemember#updatemember">Upravit profil</a>
                        @if ($member->permission == 'admin' || $member->permission == 'rewriter')
                            <a class="dropdown-item" href="/update">Editor</a>
                            <a class="dropdown-item" href="/usertable">Permissions</a>
                        @endif
                        <a class="dropdown-item" href="/logout">Odhlásit</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</nav>