@extends('backend.layouts.master')
@section('title', 'Update Police Station')

@section('backend')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Police Station</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Police Station</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary">
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('admin.p_s.update', $p_s) }}" method="POST">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select class="form-control select2bs4" style="width: 100%;"
                                                        name="country_id" required>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                @if ($country->id === $p_s->country_id) {{ 'selected' }} @endif>
                                                                {{ $country->en_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <select class="form-control select2bs4" style="width: 100%;"
                                                        name="state_id" required>
                                                        @foreach ($states as $item)
                                                            <option value="{{ $item->id }}"
                                                                @if ($item->id === $p_s->state->state_id) {{ 'selected' }} @endif>
                                                                {{ $item->en_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @foreach (config('app.languages') as $locale => $locale_name)
                                    <div class="form-group">
                                        <label for="{{ $locale }}_name">{{ $locale_name }} Name*</label>
                                        <input type="text" class="form-control" id="{{ $locale }}_name"
                                            value="{{ $p_s->{$locale . '_name'} }}" name="{{ $locale }}_name">
                                    </div>
                                @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('jsScript')
    {{-- submenu dependency --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('select[name="country_id"]').on('change', function() {
                var country_id = $(this).val();
                if (country_id) {
                    $.ajax({
                        url: "{{ url('/general/get-state/') }}/" + country_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var d = $('select[name="state_id"]').empty();
                            $('select[name="state_id"]').append(
                                '<option>Select state</option>');
                            $.each(data, function(key, value) {
                                $('select[name="state_id"]').append(
                                    '<option value="' +
                                    value.id + '">' + value
                                    .en_name + '</option>');
                            });
                        },
                    });
                } else {
                    alert('danger');
                }
            });
        });
    </script>
@endsection
