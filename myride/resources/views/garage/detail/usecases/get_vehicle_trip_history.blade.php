<style>
    .btn-trip-box {
        color: var(--whiteColor) !important;
        padding: var(--spaceLG) !important;
        margin-bottom: var(--spaceLG);
        width: 100%;
        text-align: left !important;
        border: 1.5px solid var(--whiteColor) !important;
        border-radius: var(--roundedMD) !important;
    }
    .btn-trip-box:hover {
        transform: scale(1.025);
    }
</style>

<div class="carousel-parent">
    <div id="{{$carouselId}}" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators"></div>
        <div class="carousel-inner"></div>
        <div id="carousel-nav-holder"></div>
    </div>
</div>