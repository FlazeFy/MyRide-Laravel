@extends('layouts.main_layout')

<script src="{{ asset('/js/chart_v1.0.js')}}"></script>

@section('content')
    @php
        $carouselId = 'carouselTrip';
    @endphp
    <script>
        const token = "<?= session()->get('token_key'); ?>"
    </script>

    <div class="d-block mx-auto">
        <div class="d-flex justify-content-start mb-3 gap-2">
            @include('garage.back_garage_button')
            <a class="btn btn-warning" href='/garage/edit/<?= $id ?>'>
                <i class="fa-solid fa-pen-to-square"></i><span class="d-none d-lg-inline"> Edit</span>
            </a>
            @include('garage.detail.usecases.delete_vehicle')
            @include('garage.detail.usecases.recover_vehicle')
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_detail')
                </div>
                <div id="vehicle_img_url-holder"></div>
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_driver')
                </div>
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_wash_history')
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_monthly_trip_stats')
                </div>
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_wash_summary')
                </div>
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_summary_trip')
                </div>
                <div class="container-fluid">
                    @include('garage.detail.usecases.get_vehicle_trip_history')
                </div>
            </div>
        </div>
    </div>
@endsection