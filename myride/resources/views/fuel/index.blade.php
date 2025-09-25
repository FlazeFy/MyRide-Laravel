@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-success" href='/fuel/add'><i class="fa-solid fa-plus"></i> Add Fuel</a>
        @include('fuel.usecases.get_export_fuel')
        <div class="row mt-2">
            <div class="col-lg-8 col-md-6 col-sm-12">
                <div class="container">
                    @include('fuel.usecases.get_all_list_fuel')
                    @include('fuel.usecases.hard_delete_fuel')
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="container">
                    @include('fuel.usecases.get_fuel_summary')
                </div>
                <div class="container">
                    @include('fuel.usecases.get_all_fuel_monitor')
                </div>
            </div>
        </div>
    </div>
@endsection