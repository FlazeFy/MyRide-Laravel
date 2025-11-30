@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-success" href='/driver/add'><i class="fa-solid fa-plus"></i> Driver</a>
            @include('driver.usecases.get_assigned_driver')
            @include('driver.usecases.get_export_driver')
        </div>
        <div class="container-fluid mt-2">
            @include('driver.usecases.get_all_list_driver')
            @include('driver.usecases.hard_delete_driver')
            @include('driver.usecases.put_driver')
            @include('driver.usecases.hard_delete_driver_from_vehicle')
            @include('driver.usecases.get_trip_history')
            @include('driver.usecases.post_vehicle_driver')
        </div>
    </div>
@endsection