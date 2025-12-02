@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>

    <div class="position-relative">
        @include('garage.back_garage_button')
        <div class="container-fluid mt-2">
            @include('garage.add.usecases.post_vehicle')
        </div>
    </div>
@endsection