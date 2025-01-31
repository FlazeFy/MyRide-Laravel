@extends('layouts.main_layout')

@section('content')
    <div class="d-block mx-auto p-3">
        <div class="d-flex justify-content-between">
            <button class="btn btn-nav-page" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
        </div>
        <h2>Detail Vehicle</h2>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                @include('garage.detail.usecases.get_vehicle_detail')
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">

            </div>
        </div>
    </div>
@endsection