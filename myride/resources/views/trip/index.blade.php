@extends('layouts.main_layout')

<div class="position-relative">
    <button class="btn btn-nav-page" onclick="window.location.href='/'"><i class="fa-solid fa-house"></i> Back to Home</button>
    <button class="btn btn-nav-page bg-success" onclick="init_map()"><i class="fa-solid fa-refresh"></i> Show All Trip</button>
    <button class="btn btn-nav-page bg-success" onclick="window.location.href='/trip/add'"><i class="fa-solid fa-plus"></i> Add Trip</button>
    <div class="row mx-3">
        <div class="col-lg-8 col-md-7 col-sm-12">
            @include('trip.usecases.get_map_board')
        </div>
        <div class="col-lg-4 col-md-5 col-sm-12">
            @include('trip.usecases.get_trip_list')
        </div>
    </div>
</div>