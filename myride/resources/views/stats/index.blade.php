@extends('layouts.main_layout')

<div class="p-3">
    <button class="btn btn-nav-page" onclick="window.location.href='/'" style="top: var(--spaceMD); left: var(--spaceMD);"><i class="fa-solid fa-house"></i> Back to Home</button>
    <div class="row">
        <div class="col">
            @include('stats.usecases.get_csv_export')
        </div>
        <div class="col">
            
        </div>
    </div>
</div>