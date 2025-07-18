@extends('layouts.main_layout')

<script src="{{ asset('/js/chart_v1.0.js')}}"></script>

@section('content')
    <div class="d-block mx-auto p-3">
        <div class="d-flex justify-content-between">
            <button class="btn btn-nav-page" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <h2>Detail Vehicle</h2>
                @include('garage.detail.usecases.get_vehicle_detail')
                <h2>Wash History</h2>
                @include('garage.detail.usecases.get_vehicle_wash_history')
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                @include('garage.detail.usecases.get_vehicle_monthly_trip_stats')
                @include('garage.detail.usecases.get_vehicle_summary_trip')
                <h2>Trip History</h2>
                @include('garage.detail.usecases.get_vehicle_trip_history')
            </div>
        </div>
    </div>
@endsection