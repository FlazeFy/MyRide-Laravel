<style>
    .carousel-inner, .carousel-item {
        height: 100% !important;
        max-height: 100vh !important;
    }
    .carousel-caption {
        color: var(--whiteColor) !important;
        right: 0;
        left: 0;
        background: var(--darkColor) !important;
        bottom: 0;
        margin: 0;
        width: 100%;
        text-align: left;
        padding: var(--spaceJumbo) var(--spaceJumbo) 16vh var(--spaceJumbo);
    }
    .carousel-indicators {
        display: block !important;
        margin: var(--spaceXLG) !important;
    }
    .carousel-indicators button {
        height: 10vh !important;
        width: 18vh !important;
        margin: 0 var(--spaceMD) !important;
        border-width: var(--spaceMini);
    }
    .carousel-indicators button:hover {
        border: var(--spaceMini) solid var(--primaryColor);
    }
</style>

<?php
    use App\Helpers\Converter;
?>

<div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        @php($i = 0)

        @foreach($dt_all_vehicle as $dt)
            <button type="button" data-bs-target="#carouselExampleDark" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.35)), url('http://127.0.0.1:8000/assets/car_default.jpg');" 
                data-bs-slide-to="{{$i}}" class="active" aria-current="true" aria-label="Slide {{$i + 1}}"></button>
            @php($i++)
        @endforeach
    </div>
    <div class="carousel-inner">
        @php($ext_class = 'active')

        @foreach($dt_all_vehicle as $dt)
            <div class="carousel-item {{$ext_class}}" data-bs-interval="10000">
                <img src="{{ asset('/assets/car_default.jpg') }}" class="d-block w-100" alt="...">
                <div class="carousel-caption">
                    <div class="d-flex justify-content-between position-relative">
                        <div class="bg-dark py-2 px-4 position-absolute rounded-pill" style="top: -110px;"><div class="w-100 rounded-pill py-2 mb-1" style="background:{{$dt->vehicle_color}}; height: var(--spaceMD);"></div>Color</div>
                        <div><h1>{{$dt->vehicle_merk}} - {{$dt->vehicle_name}}</h1></div>
                        <div>
                            <span class="bg-success text-white px-4 py-3 rounded-pill me-2">{{$dt->vehicle_type}}</span>
                            <span class="bg-primary text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-car"></i> {{$dt->vehicle_category}}</span>
                            @php($mark_fuel = 'success')
                            @if($dt->vehicle_fuel_status == 'Empty')
                                @php($mark_fuel = 'danger')
                            @elseif($dt->vehicle_fuel_status == 'Low')
                                @php($mark_fuel = 'warning')
                            @endif

                            <span class="bg-{{$mark_fuel}} text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-gas-pump"></i> {{$dt->vehicle_fuel_status}}</span>
                            <span class="bg-success text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-location-arrow"></i> {{Converter::convert_price_k($dt->vehicle_distance)}} Km</span>
                            <span class="bg-primary text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-user"></i> {{$dt->vehicle_capacity}}</span>

                            @php($mark_status = 'success')
                            @if($dt->vehicle_status == 'Broken')
                                @php($mark_status = 'warning')
                            @elseif($dt->vehicle_status == 'Fatal Broken')
                                @php($mark_status = 'danger')
                            @endif

                            <span class="bg-{{$mark_status}} text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-wrench"></i> {{$dt->vehicle_status}}</span>
                        </div>
                    </div>
                    <h3>{{$dt->vehicle_plate_number}}</h3><hr>
                    <h5>{{$dt->vehicle_desc}}</h5>

                    @if($dt->updated_at != null)
                        <h6 class="fst-italic" style="font-size: var(--textXMD); text-align: end;">Last Updated {{$dt->updated_at}}</h6>
                    @endif
                </div>
            </div>
            @php($ext_class = '')
        @endforeach
    </div>
    <button class="carousel-control-prev vehicle-other ms-4" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next vehicle-other me-4" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>