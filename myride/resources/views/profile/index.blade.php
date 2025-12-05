@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="container-fluid">
                @include('profile.usecases.get_active_req')
                @include('profile.usecases.get_profile')
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            
        </div>
    </div>
@endsection