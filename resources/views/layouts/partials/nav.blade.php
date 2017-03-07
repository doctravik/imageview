<nav class="nav has-shadow">
    <div class="container">
        <div class="nav-left">
            <a class="nav-item" href="{{ url('/') }}">
                {{-- <img src="{{ Storage::url('images/logo.png') }}" class="image is-32x32" alt="CoffeeFun logo"> --}}
                <h3 class="title is-3">&nbsp; Imageview</h3>
            </a>
        </div>
        <span class="nav-toggle"  id="nav-toggle">
            <span></span><span></span><span></span>
        </span>

        <div class="nav-right nav-menu" id="auth-menu">
            @if (Auth::guest())
                <a href="{{ url('/register') }}"
                    class="nav-item {{ $route == 'register' ? 'is-active' : '' }}">Sign up</a>
                <a href="{{ url('/login') }}" 
                    class="nav-item {{ $route == 'login' ? 'is-active' : '' }}">Login</a>
            @else
                <span class="nav-item">{{ Auth::user()->name }}</span>
                <a class="nav-item" href="{{ url('/logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">logout</a>

                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
            @endif
        </div>
    </div>
</nav>