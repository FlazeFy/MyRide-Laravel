@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative text-center mx-auto py-5" style='max-width: 720px;'>
        @include('partner.usecases.all_trip_partner')
    </div>
@endsection