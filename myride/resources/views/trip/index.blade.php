@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>
    <style>
        .carousel-button-holder {
            position: block !important;
            top: 0 !important;  
            right: 60px;
        }
        @media (min-width: 768px) {
            .carousel-button-holder {
                right: 0;
            }
        }
    </style>

    <div class="position-relative">
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-success" href='/trip/add'><i class="fa-solid fa-plus"></i> Trip</a>
            @include('trip.usecases.get_export_trip')
            <button class="btn btn-danger" onclick="initMap()"><i class="fa-solid fa-refresh"></i><span class="d-none d-md-inline"> Show All Trip</span></button>
            <div id="carousel-nav-holder"></div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-8 col-md-7 col-sm-12">
                @include('trip.usecases.get_map_board')
            </div>
            <div class="col-lg-4 col-md-5 col-sm-12">
                @include('trip.usecases.get_trip_list')
                @include('trip.usecases.hard_delete_trip')
                @include('trip.usecases.put_trip')
            </div>
        </div>
    </div>
@endsection