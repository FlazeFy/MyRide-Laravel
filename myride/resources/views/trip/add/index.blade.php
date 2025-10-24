@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <button class="btn btn-danger" onclick="window.location.href='/trip'"><i class="fa-solid fa-arrow-left"></i> Browse All Trip</button>
        <div class="container w-100 mt-4">
            @include('trip.add.usecases.post_trip')
        </div>
    </div>
@endsection
