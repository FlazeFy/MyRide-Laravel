@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>

    <div class="position-relative">
        <a class="btn btn-success" href='/service/add'><i class="fa-solid fa-plus"></i> Add Service</a>
        @include('service.usecases.get_export_service')
        <div class="row mt-2">
            <div class="col-lg-8 col-md-6 col-sm-12">
                <div class="container">
                    @include('service.usecases.get_all_list_service')
                    @include('service.usecases.hard_delete_service')
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="container">
                    @include('service.usecases.get_list_service_spending_per_vehicle')
                </div>
            </div>
        </div>
    </div>
@endsection