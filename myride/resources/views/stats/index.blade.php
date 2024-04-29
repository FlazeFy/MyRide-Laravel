@extends('layouts.main_layout')

@section('content')
    <div class="p-3">
        <button class="btn btn-nav-page" onclick="window.location.href='/'" style="top: var(--spaceMD); left: var(--spaceMD);"><i class="fa-solid fa-house"></i> Back to Home</button>
        <div class="row">
            <div class="col">
                @include('stats.usecases.get_csv_export')
            </div>
            <div class="col">
                
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                @include('stats.usecases.get_total_trip_by_category')
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                @include('stats.usecases.get_total_vehicle_by_type')
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                @include('stats.usecases.get_total_trip_by_origin')
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                @include('stats.usecases.get_total_trip_by_destination')
            </div>
        </div>
    </div>
@endsection