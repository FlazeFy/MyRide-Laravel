<style>
    .welcome-section {
        margin: 10vh 0; 
        max-width: 960px;
        display: block;
        margin-inline: auto;
    }
    
    @media (min-width: 768px) and (max-width: 991px) {
        .welcome-section {
            margin: 7vh 0; 
        } 
    }
    @media (max-width: 575px) {
        .welcome-section {
            margin: 3vh 0; 
        } 
    }
</style>

<div class="welcome-section">
    <h1 class="fw-bold">Welcome to MyRide</h1>
    @if(session()->get('username_key'))
        <h3 class="fw-bold">{{session()->get('username_key')}}</h3>
    @endif
    <img src="{{asset('assets/logo_nocap.png')}}" style='width:30vh;'>
    <h2 class="fw-bold">One App, Total Vehicle Care</h2>
    <hr>
    <p>Where You Can Manage And Monitoring Your Vehicle. From Vehicle Listing, Service & Wash Schedule, Trip History
        Driver, Monitoring Your Stats, Live Tracking, and Many More
    </p>
    <div class="d-flex flex-wrap gap-2 gap-md-4 justify-content-center align-items-center">
        @if(session()->get('username_key'))
            <a class="btn btn-success" href="/dashboard"><i class="fa-solid fa-play"></i> Explore Now!</a>
        @else
            <div class="mt-2 text-start border border-2 border-dark p-3 rounded-3">
                <h6>Sign In With</h6>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a class="btn btn-success" href="/login"><i class="fa-solid fa-play"></i> Basic Account</a>
                    <a class="btn btn-success"><i class="fa-brands fa-google"></i> Google Sign In</a>
                </div>
            </div>
        @endif
        @if(session()->get('username_key'))
            <div class="d-block d-md-none">
                <a class="btn btn-success px-3" href="#"><i class="fa-solid fa-bell fa-lg"></i></a>
                <a class="btn btn-danger px-3" data-bs-target="#modalSignOut" data-bs-toggle="modal"><i class="fa-solid fa-right-from-bracket fa-lg"></i></a>
            </div>
        @else
            <a class="btn btn-primary" href="/register"><i class="fa-solid fa-arrow-right-to-bracket"></i> Register</a>
        @endif
    </div>
</div>