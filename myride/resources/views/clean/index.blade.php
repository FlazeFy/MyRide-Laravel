@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <a class="btn btn-success" href='/clean/add'><i class="fa-solid fa-plus"></i> Clean</a>
        @include('clean.usecases.get_export_clean')
        <div class="row mt-3">
            <div class="col-xl-9 col-lg-8 col-md-12">
                <div class="container-fluid">
                    @include('clean.usecases.get_all_list_clean')
                    @include('clean.usecases.hard_delete_clean')
                    @include('clean.usecases.put_finish_clean')
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-12">
                <div class="container-fluid">
                    @include('clean.usecases.get_clean_summary')
                </div>
            <div>
        </div>
    </div>
@endsection