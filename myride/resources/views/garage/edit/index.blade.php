@extends('layouts.main_layout')

@section('content')
    <div class="d-block mx-auto p-3" style="max-width: 1080px;">
        <button class="btn btn-nav-page" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
        @include('garage.edit.usecases.put_vehicle_data')
    </div>
@endsection