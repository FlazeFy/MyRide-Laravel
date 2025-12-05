@php
    $path = request()->path();
@endphp
<nav class="navbar navbar-expand-lg">
    <div class="d-inline-block w-100 d-flex flex-wrap gap-3 align-items-center">
        @if($path !== '/')
            <div class="d-inline-block">
                <button class="toogle_nav-button btn btn-primary px-3 close"><i class="fa-solid fa-bars"></i></button>
            </div>
        @endif
        <div>
            <h5 class="navbar-brand"><a href="/">
                @php
                    if ($path === '/' || $path === '') {
                        echo "MyRide";
                    } else {
                        $segments = explode('/', trim($path, '/'));
                        $titleParts = ["MyRide"];

                        foreach ($segments as $segment) {
                            // Skip UUID
                            if (preg_match('/^[0-9a-fA-F-]{36}$/', $segment)) {
                                continue;
                            }

                            $wash = str_replace(['-', '_'], ' ', $segment);
                            $titleParts[] = ucwords($wash);
                        }

                        echo implode(' > ', $titleParts);
                    }
                @endphp
            </a></h5>
            <div class="navbar-collapse-mobile flex-wrap gap-2">
                <a class="btn btn-primary px-3" href="<?= Route::current()->uri() === "/" ? "/dashboard" : "/profile"?>"><i class="fa-solid fa-user"></i><span class="button-text"> {{session()->get('username_key')}}</span></a>
                <a class="btn btn-success px-3 open-notification-btn" data-bs-toggle="popover" title="Reminder" data-bs-placement="left" data-bs-custom-class="wide-popover" data-bs-html="true" 
                    data-bs-content="<div id='reminder-holder-1'></div>"><i class="fa-solid fa-bell"></i>
                </a>
                <a class="btn btn-danger px-3" data-bs-target="#modalSignOut" data-bs-toggle="modal"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
        
        @if(session()->get('token_key'))
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item"><a class="btn btn-primary px-3" href="<?= Route::current()->uri() === "/" ? "/dashboard" : "/profile"?>"><i class="fa-solid fa-user"></i><span class="button-text"> {{session()->get('username_key')}}</span></a></li>
                <li class="nav-item">
                    <a class="btn btn-success px-3 open-notification-btn" data-bs-toggle="popover" title="Reminder" data-bs-placement="left" data-bs-html="true" data-bs-custom-class="wide-popover" data-bs-content="<div id='reminder-holder-2'></div>">
                        <i class="fa-solid fa-bell"></i>
                    </a>
                </li>
                <li class="nav-item"><a class="btn btn-danger px-3" data-bs-target="#modalSignOut" data-bs-toggle="modal"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </div>
        @endif
        @include('others.notification')
    </div>
</nav>