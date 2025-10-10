@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>

    <div class="position-relative">
        <a class="btn btn-success" href='/driver/add'><i class="fa-solid fa-plus"></i> Add Driver</a>
        @include('driver.usecases.get_assigned_driver')
        @include('driver.usecases.get_export_driver')
        <div class="container mt-2">
            @include('driver.usecases.get_all_list_driver')
            @include('driver.usecases.hard_delete_driver')
            @include('driver.usecases.put_driver')
            @include('driver.usecases.hard_delete_driver_from_vehicle')
        </div>
    </div>
@endsection