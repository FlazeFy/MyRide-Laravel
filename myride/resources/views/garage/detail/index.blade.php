@extends('layouts.main_layout')

<script src="{{ asset('/js/chart_v1.0.js')}}"></script>

@section('content')
    @php
        $carouselId = 'carouselTrip';
    @endphp
    <script>
        const token = "<?= session()->get('token_key'); ?>"
    </script>

    <div class="d-block mx-auto p-3">
        <div class="d-flex justify-content-start mb-2">
            <button class="btn btn-danger me-2" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
            @include('garage.detail.usecases.delete_vehicle')
            @include('garage.detail.usecases.recover_vehicle')
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                @include('garage.detail.usecases.get_vehicle_detail')
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_driver')
                </div>
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_wash_history')
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_monthly_trip_stats')
                </div>
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_clean_summary')
                </div>
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_summary_trip')
                </div>
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_trip_history')
                </div>
            </div>
        </div>
    </div>
@endsection