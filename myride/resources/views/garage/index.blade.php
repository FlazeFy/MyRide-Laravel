@extends('layouts.main_layout')

<div class="position-relative">
    <button class="btn btn-nav-page" onclick="window.location.href='/'" style="top: var(--spaceMD); left: var(--spaceMD);"><i class="fa-solid fa-house"></i> Back to Home</button>
    @include('garage.usecases.get_vehicle_list')
</div>