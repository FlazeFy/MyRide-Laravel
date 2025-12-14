@extends('layouts.main_layout')

@section('content')
    <div class="mx-auto" style="max-width: 720px;">
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
@endsection