<style>
    .btn-menu-landing {
        width: 100%;
        border-radius: var(--roundedLG);
        height: 90vh;
        -webkit-transition: all 0.4s !important;
        -o-transition: all 0.4s !important;
        transition: all 0.4s !important;
        border: transparent solid var(--primaryColor);
        /* z-index: 999 !important; */
    }
    .carousel-item .row > div:nth-child(2) .btn:hover {
        transform: scale(1.075) !important;
    }
    .carousel-item .row > div:nth-child(1) .btn:hover, .carousel-item .row > div:nth-child(3) .btn:hover {
        transform: scale(1) !important;
    }
    .btn-menu-landing:hover {
        border: var(--spaceMini) solid var(--primaryColor);
    }
    .carousel-item {
        padding-top: var(--spaceJumbo);
        padding-inline:  var(--spaceJumbo);
    }
</style>


<div id="carouselExampleControls" class="carousel slide" style="height: 100vh;" data-bs-ride="carousel">
    <div class="carousel-inner h-100">
        <div class="carousel-item h-100 active">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/trip.jpg');" onclick="window.location.href='/trip'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Trip</h2>
                    </button>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/garage.jpg');" onclick="window.location.href='/garage'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">My Garage</h2>
                    </button>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/service.jpg');" onclick="window.location.href='/service'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Service</h2>
                    </button>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/wash.jpg');" onclick="window.location.href='/clean'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Cleanliness</h2>
                    </button>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/stats.jpg');" onclick="window.location.href='/stats'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Statistic</h2>
                    </button>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/driver.jpg');" onclick="window.location.href='/driver'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Driver</h2>
                    </button>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/help.jpg');" onclick="window.location.href='/help'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Help Center</h2>
                    </button>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/setting.jpg');" onclick="window.location.href='/setting'">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Setting</h2>
                    </button>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <button class="btn btn-menu-landing" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4),rgba(0, 0, 0, 0.55)), url('http://127.0.0.1:8000/assets/signout.jpg');"
                        data-bs-toggle="modal" data-bs-target="#modalSignOut">
                        <h2 class="text-white position-absolute" style="bottom: var(--spaceLG); left: var(--spaceLG);">Sign Out</h2>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>