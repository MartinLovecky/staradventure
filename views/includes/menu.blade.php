<nav class="navbar navbar-expand-lg bg-body-dark-subtle">
    <div class="container-fluid">
        <a class="navbar-brand nav-link" style="margin-bottom: 1vh;">
            <img class="img-fluid" src="@asset('img/favicon_io/favicon-32x32.png')" alt="brand">StarAdventure
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link @if($selector->article == 'allwin'){{'active'}}@endif" href="/show/allwin/1#story">Allwin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($selector->article == 'samuel'){{'active'}}@endif" href="/show/samuel/1#story">Samuel</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Isama</a>
                    <ul class="dropdown-menu text-center bg-secondary">
                        <li><a class="dropdown-item @if($selector->article == 'isama'){{'active'}}@endif" href="/show/isama/1#story">Isama</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'isamanh'){{'active'}}@endif" href="/show/isamanh/1#story">Nový horizont</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'isamanw'){{'active'}}@endif" href="/show/isamanw/1#story">Nový vesmír</a></li>
                    </ul>
                </li>
                <li class="nav-item dropend">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Ostatní</a>
                    <ul class="dropdown-menu text-center bg-secondary">
                        <li><a class="dropdown-item @if($selector->article == 'angel'){{'active'}}@endif" href="/show/angel/1#story">Angel & Eklips</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'mry'){{'active'}}@endif" href="/show/mry/1#story">Mr. ?</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'white'){{'active'}}@endif" href="/show/white/1#story">White Star</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'terror'){{'active'}}@endif" href="/show/terror/1#story">Terror</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'hyperion'){{'active'}}@endif" href="/show/hyperion/1#story">Hyperion</a></li>
                        <li><a class="dropdown-item @if($selector->article == 'demoni'){{'active'}}@endif" href="/show/demoni/1#story">Démoni</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto d-flex align-items-center">
                <li class="nav-item dropdown d-flex align-items-center">
                    <img class="img-fluid rounded-circle" style="width: 70px; height: 70px; margin-right: 1vh;" src='@asset("img/avatars/{$member->avatar}")'>
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{$member->username}}
                    </a>
                    <ul class="dropdown-menu text-center bg-secondary">
                        <li><a class="dropdown-item" href="/member/{{$member->username}}#member">Profil</a></li>
                        <li><a class="dropdown-item" href="/updatemember#updatemember">Upravit profil</a></li>
                        @if ($member->permission == 'admin' || $member->permission == 'rewriter')
                        <li><a class="dropdown-item" href="/update#edit">Editor</a></li>
                        <li><a class="dropdown-item" href="/usertable#usertable">Permissions</a></li>
                        @endif
                        <li><a class="dropdown-item" href="/logout">Odhlásit</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>