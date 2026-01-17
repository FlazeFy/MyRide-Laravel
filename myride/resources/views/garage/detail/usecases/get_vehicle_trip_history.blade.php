<style>
    .btn-trip-box {
        padding: var(--spaceLG) !important;
        margin-bottom: var(--spaceLG);
        width: 100%;
        text-align: left !important;
        border: 1.5px solid var(--firstColor) !important;
        border-radius: var(--roundedMD) !important;
    }
    .btn-trip-box:hover {
        transform: scale(1.025);
    }
</style>

<h2 class="mb-3">Trip History</h2><hr>
<div class="carousel-parent pb-3">
    <div id="{{$carouselId}}" class="carousel slide position-relative">
        <div class="carousel-inner"></div>
        <div id="carousel-nav-holder"></div>
    </div>
</div>