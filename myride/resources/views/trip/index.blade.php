@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <button class="btn btn-danger me-2" onclick="initMap()"><i class="fa-solid fa-refresh"></i> Show All Trip</button>
        <button class="btn btn-success" onclick="window.location.href='/trip/add'"><i class="fa-solid fa-plus"></i> Add Trip</button>
        <div class="row mt-3">
            <div class="col-lg-8 col-md-7 col-sm-12">
                @include('trip.usecases.get_map_board')
            </div>
            <div class="col-lg-4 col-md-5 col-sm-12">
                @include('trip.usecases.get_trip_list')
                @include('trip.usecases.hard_delete_trip')
            </div>
        </div>
    </div>
@endsection