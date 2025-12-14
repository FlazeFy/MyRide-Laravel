@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="mx-auto" style="max-width: 720px;">
        <div class="container-fluid">
            @include('profile.usecases.get_active_req')
            @include('profile.usecases.get_profile')
        </div>
        <div class="container-fluid">
            <h2>Setting</h2><hr>
            @include('profile.usecases.set_auto_bg_mode')
            @include('profile.usecases.set_light_dark_mode')
        </div>
    </div>
@endsection