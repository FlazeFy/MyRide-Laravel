@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        @include('garage.usecases.get_vehicle_list')
    </div>
@endsection