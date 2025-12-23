@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <button class="btn btn-danger" onclick="window.location.href='/trip'"><i class="fa-solid fa-arrow-left"></i> Back</button>
        <div class="container-fluid w-100 mt-2">
            @include('trip.calendar.usecases.calendar')
        </div>
    </div>
@endsection
