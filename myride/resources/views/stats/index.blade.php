@extends('layouts.main_layout')

@section('content')
    <div class="p-3">
        <div class="row">
            <div class="col">
                @include('stats.usecases.get_csv_export')
            </div>
            <div class="col">
                
            </div>
        </div>
        <div class="row text-center">
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="container">
                    @include('stats.usecases.get_total_trip_by_category')
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="container">
                    @include('stats.usecases.get_total_vehicle_by_type')
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">

            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="container">
                    @include('stats.usecases.get_total_trip_by_origin')
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="container">
                    @include('stats.usecases.get_total_trip_by_destination')
                </div>
            </div>
        </div>
    </div>
@endsection