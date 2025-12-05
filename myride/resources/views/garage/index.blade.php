@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>
    
    <div class="position-relative">
        <a class="btn btn-success" href='/garage/add'><i class="fa-solid fa-plus"></i> Vehicle</a>
        @include('garage.usecases.get_vehicle_list')
    </div>
@endsection