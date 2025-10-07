@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>

    <div class="position-relative">
        @include('driver.usecases.get_export_driver')
        <div class="container mt-2">
            @include('driver.usecases.get_all_list_driver')
            @include('driver.usecases.hard_delete_driver')
        </div>
    </div>
@endsection