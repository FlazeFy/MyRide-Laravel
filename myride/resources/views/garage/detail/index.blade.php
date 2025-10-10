@extends('layouts.main_layout')

<script src="{{ asset('/js/chart_v1.0.js')}}"></script>

@section('content')
    <div class="d-block mx-auto p-3">
        <div class="d-flex justify-content-between">
            <button class="btn btn-danger" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                @include('garage.detail.usecases.get_vehicle_detail')
                <div class="container">
                    <h2>Driver</h2>
                    @include('garage.detail.usecases.get_vehicle_driver')
                </div>
                <div class="container">
                    <h2>Wash History</h2>
                    @include('garage.detail.usecases.get_vehicle_wash_history')
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_monthly_trip_stats')
                </div>
                <div class="container">
                    @include('garage.detail.usecases.get_vehicle_summary_trip')
                </div>
                <div class="container">
                    <h2>Trip History</h2>
                    @include('garage.detail.usecases.get_vehicle_trip_history')
                </div>
            </div>
        </div>
    </div>
@endsection