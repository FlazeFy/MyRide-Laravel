@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="container-fluid">
                    @include('help.usecases.auth_module')
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="container-fluid">
                    @include('help.usecases.vehicle_module')
                </div>
            </div>
        </div>
    </div>
@endsection