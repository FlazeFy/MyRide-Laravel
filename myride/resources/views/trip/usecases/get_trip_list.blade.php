<style>
    .btn-trip-box {
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

<?php 
    use App\Helpers\Converter;
?>

@php
    $carouselId = 'carouselTrip';
@endphp

<div class="carousel-parent">
    <div id="{{$carouselId}}" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators"></div>
        <div class="carousel-inner py-4"></div>

        @include('others.button.button_navigate_carousel', ['carouselId' => $carouselId])
    </div>
</div>

<script type="text/javascript">
    function show_location(lat1, long1, lat2, long2){
        refresh_map(lat1, long1)

        place_marker_and_pan_to(lat1, long1, map)
        place_marker_and_pan_to(lat2, long2, map)
    }

    function refresh_map(lat, long) {
        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: lat, lng: long},
            zoom: 12,
        });
    }

    function place_marker_and_pan_to(lat, long, map) {
        const latLong = { lat: lat, lng: long}

        new google.maps.Marker({
            position: latLong,
            map: map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png',
                scaledSize: new google.maps.Size(40, 40),
            }
        });
        map.panTo(latLong)
    }
</script>