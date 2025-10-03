@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-success" href='/service/add'><i class="fa-solid fa-plus"></i> Add Service</a>
        <div class="row mt-2">
            <div class="col-lg-8 col-md-6 col-sm-12">
                <div class="container">
                    @include('service.usecases.get_all_list_service')
                    @include('service.usecases.hard_delete_service')
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                
            </div>
        </div>
    </div>
@endsection