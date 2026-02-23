@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative text-center mx-auto py-5" style='max-width: 720px;'>
        <img src="{{asset('assets/journey.png')}}" alt='journey.png' class="img img-fluid w-100 mb-3">
        <h2>Curious to see your journey by vehicle?</h2>
        @include('journey.usecases.select_vehicle')
    </div>
@endsection