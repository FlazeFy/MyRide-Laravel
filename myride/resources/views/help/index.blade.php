@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="container-fluid">
                    @include('help.usecases.auth_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.trip_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.fuel_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.service_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.reminder_module')
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="container-fluid">
                    @include('help.usecases.vehicle_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.wash_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.inventory_module')
                </div>
                <div class="container-fluid">
                    @include('help.usecases.driver_module')
                </div>
            </div>
        </div>
    </div>
@endsection