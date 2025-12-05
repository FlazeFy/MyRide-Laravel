@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-success" href='/wash/add'><i class="fa-solid fa-plus"></i> Wash</a>
            @include('wash.usecases.get_export_wash')
        </div>
        <div class="row mt-3">
            <div class="col-xl-9 col-lg-8 col-md-12">
                <div class="container-fluid">
                    @include('wash.usecases.get_all_list_wash')
                    @include('wash.usecases.hard_delete_wash')
                    @include('wash.usecases.put_finish_wash')
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-12">
                <div class="container-fluid">
                    @include('wash.usecases.get_wash_summary')
                </div>
            <div>
        </div>
    </div>
@endsection