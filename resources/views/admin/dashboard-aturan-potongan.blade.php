@extends('layout.main-admin')

@section('tittle-admin')
Aturan Potongan
@endsection


@section('content-admin')


<div class="container-fluid">
<div class="mb-3">

    <button
        type="button"
        class="btn btn-primary"
        data-toggle="modal"
        data-target="#tambahAturanPotongan">

        <i class="fas fa-plus"></i>

        Tambah Aturan Potongan

    </button>

</div>


<div class="table-responsive">

    <table
        class="table table-bordered"
        id="aturan"
        width="100%">

        <thead>

            <tr>

                <th>No</th>

                <th>Jenis Potongan</th>

                <th>Nama Aturan</th>

                <th>Menit</th>

                <th>Qty</th>

                <th>Tipe Nilai</th>

                <th>Nilai Potongan</th>

                <th>Sumber</th>

                <th>Status</th>

                <th>Action</th>

            </tr>

        </thead>


        <tfoot>

            <tr>

                <th>No</th>

                <th>Jenis Potongan</th>

                <th>Nama Aturan</th>

                <th>Menit</th>

                <th>Qty</th>

                <th>Tipe Nilai</th>

                <th>Nilai Potongan</th>

                <th>Sumber</th>

                <th>Status</th>

                <th>Action</th>

            </tr>

        </tfoot>


        <tbody>
        </tbody>

    </table>

</div>
</div>



{{-- =====================================
     MODAL TAMBAH
===================================== --}}

<div
    class="modal fade"
    id="tambahAturanPotongan"
    tabindex="-1"
    role="dialog">


    <div
        class="modal-dialog modal-lg"
        role="document">


        <div class="modal-content">


            <form
                action="{{ route('aturan-potongan.store') }}"
                method="POST">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">

                        Tambah Aturan Potongan

                    </h5>


                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>
                            &times;
                        </span>

                    </button>

                </div>


                <div class="modal-body">


                    <div class="row">


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Konfigurasi ID
                                </label>

                                <input
                                    type="number"
                                    name="konfig_id"
                                    class="form-control"
                                    value="1"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Jenis Potongan
                                </label>

                                <select
                                    name="jenis_potongan"
                                    class="form-control"
                                    required>

                                    <option value="">
                                        -- Pilih Jenis --
                                    </option>

                                    <option value="terlambat">
                                        Terlambat
                                    </option>

                                    <option value="alpha">
                                        Alpha
                                    </option>

                                    <option value="izin">
                                        Izin
                                    </option>

                                    <option value="sakit">
                                        Sakit
                                    </option>

                                    <option value="lupa_absen">
                                        Lupa Absen
                                    </option>

                                    <option value="bpjs">
                                        BPJS
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-12">

                            <div class="form-group">

                                <label>
                                    Nama Aturan
                                </label>

                                <input
                                    type="text"
                                    name="nama_aturan"
                                    class="form-control"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Menit Mulai
                                </label>

                                <input
                                    type="number"
                                    name="menit_mulai"
                                    class="form-control">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Menit Selesai
                                </label>

                                <input
                                    type="number"
                                    name="menit_selesai"
                                    class="form-control">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Qty Mulai
                                </label>

                                <input
                                    type="number"
                                    name="qty_mulai"
                                    class="form-control">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Qty Selesai
                                </label>

                                <input
                                    type="number"
                                    name="qty_selesai"
                                    class="form-control">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Tipe Nilai
                                </label>

                                <select
                                    name="tipe_nilai"
                                    class="form-control"
                                    required>

                                    <option value="nominal">
                                        Nominal
                                    </option>

                                    <option value="persen">
                                        Persen
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Nilai Potongan
                                </label>

                                <input
                                    type="number"
                                    name="nilai_potongan"
                                    class="form-control"
                                    step="0.01"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Sumber Potongan
                                </label>

                                <select
                                    name="sumber_potongan"
                                    class="form-control">

                                    <option value="">
                                        -- Pilih --
                                    </option>

                                    <option value="gaji_pokok">
                                        Gaji Pokok
                                    </option>

                                    <option value="uang_makan">
                                        Uang Makan
                                    </option>

                                    <option value="tunjangan">
                                        Tunjangan
                                    </option>

                                    <option value="nominal">
                                        Nominal Tetap
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Status
                                </label>

                                <select
                                    name="is_active"
                                    class="form-control"
                                    required>

                                    <option value="1">
                                        Aktif
                                    </option>

                                    <option value="0">
                                        Tidak Aktif
                                    </option>

                                </select>

                            </div>

                        </div>


                    </div>


                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Tutup

                    </button>


                    <button
                        type="submit"
                        class="btn btn-primary">

                        Simpan

                    </button>

                </div>


            </form>


        </div>

    </div>

</div>



{{-- =====================================
     MODAL UPDATE
===================================== --}}

@foreach($data as $dp)

<div
    class="modal fade"
    id="ubah-{{ $dp->id }}"
    tabindex="-1"
    role="dialog">


    <div
        class="modal-dialog modal-lg"
        role="document">


        <div class="modal-content">


            <form
                action="{{
                    route(
                        'aturan-potongan.update',
                        $dp->id
                    )
                }}"
                method="POST">

                @csrf
                @method('PUT')


                <div class="modal-header">

                    <h5 class="modal-title">

                        Ubah Aturan Potongan

                    </h5>


                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>
                            &times;
                        </span>

                    </button>

                </div>


                <div class="modal-body">


                    <div class="row">


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Konfigurasi ID
                                </label>

                                <input
                                    type="number"
                                    name="konfig_id"
                                    class="form-control"
                                    value="{{ $dp->konfig_id }}"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Jenis Potongan
                                </label>

                                <select
                                    name="jenis_potongan"
                                    class="form-control"
                                    required>


                                    @php

                                    $jenis = [
                                        'terlambat'
                                            => 'Terlambat',

                                        'alpha'
                                            => 'Alpha',

                                        'izin'
                                            => 'Izin',

                                        'sakit'
                                            => 'Sakit',

                                        'lupa_absen'
                                            => 'Lupa Absen',

                                        'bpjs'
                                            => 'BPJS',
                                    ];

                                    @endphp


                                    @foreach(
                                        $jenis
                                        as $key => $label
                                    )

                                    <option
                                        value="{{ $key }}"
                                        {{
                                            $dp->jenis_potongan
                                            == $key
                                            ? 'selected'
                                            : ''
                                        }}>

                                        {{ $label }}

                                    </option>

                                    @endforeach


                                </select>

                            </div>

                        </div>


                        <div class="col-md-12">

                            <div class="form-group">

                                <label>
                                    Nama Aturan
                                </label>

                                <input
                                    type="text"
                                    name="nama_aturan"
                                    class="form-control"
                                    value="{{
                                        $dp->nama_aturan
                                    }}"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Menit Mulai
                                </label>

                                <input
                                    type="number"
                                    name="menit_mulai"
                                    class="form-control"
                                    value="{{
                                        $dp->menit_mulai
                                    }}">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Menit Selesai
                                </label>

                                <input
                                    type="number"
                                    name="menit_selesai"
                                    class="form-control"
                                    value="{{
                                        $dp->menit_selesai
                                    }}">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Qty Mulai
                                </label>

                                <input
                                    type="number"
                                    name="qty_mulai"
                                    class="form-control"
                                    value="{{
                                        $dp->qty_mulai
                                    }}">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Qty Selesai
                                </label>

                                <input
                                    type="number"
                                    name="qty_selesai"
                                    class="form-control"
                                    value="{{
                                        $dp->qty_selesai
                                    }}">

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Tipe Nilai
                                </label>

                                <select
                                    name="tipe_nilai"
                                    class="form-control">

                                    <option
                                        value="nominal"
                                        {{
                                            $dp->tipe_nilai
                                            == 'nominal'
                                            ? 'selected'
                                            : ''
                                        }}>

                                        Nominal

                                    </option>

                                    <option
                                        value="persen"
                                        {{
                                            $dp->tipe_nilai
                                            == 'persen'
                                            ? 'selected'
                                            : ''
                                        }}>

                                        Persen

                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Nilai Potongan
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    name="nilai_potongan"
                                    class="form-control"
                                    value="{{
                                        $dp->nilai_potongan
                                    }}"
                                    required>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Sumber Potongan
                                </label>

                                <select
                                    name="sumber_potongan"
                                    class="form-control"
                                    required>

                                    <option value="">
                                        -- Pilih Sumber Potongan --
                                    </option>

                                    <option
                                        value="uang_makan"
                                        {{ $dp->sumber_potongan == 'uang_makan' ? 'selected' : '' }}>
                                        Uang Makan
                                    </option>

                                    <option
                                        value="total_gaji"
                                        {{ $dp->sumber_potongan == 'total_gaji' ? 'selected' : '' }}>
                                        Total Gaji
                                    </option>

                                    <option
                                        value="gaji_pokok"
                                        {{ $dp->sumber_potongan == 'gaji_pokok' ? 'selected' : '' }}>
                                        Gaji Pokok
                                    </option>

                                </select>

                            </div>
                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Status
                                </label>

                                <select
                                    name="is_active"
                                    class="form-control">

                                    <option
                                        value="1"
                                        {{
                                            $dp->is_active
                                            ? 'selected'
                                            : ''
                                        }}>

                                        Aktif

                                    </option>

                                    <option
                                        value="0"
                                        {{
                                            !$dp->is_active
                                            ? 'selected'
                                            : ''
                                        }}>

                                        Tidak Aktif

                                    </option>

                                </select>

                            </div>

                        </div>


                    </div>


                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Tutup

                    </button>


                    <button
                        type="submit"
                        class="btn btn-warning">

                        Update

                    </button>

                </div>


            </form>


        </div>

    </div>

</div>

@endforeach


<script>



    $('#aturan').DataTable({

        processing: true,

        serverSide: true,

        ajax: "{{ route('aturan-potongan.index') }}",

        columns: [

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },

            {
                data: 'jenis_potongan',
                name: 'jenis_potongan'
            },

            {
                data: 'nama_aturan',
                name: 'nama_aturan'
            },

            {
                data: null,

                render: function (data) {

                    let mulai =
                        data.menit_mulai ?? '-';

                    let selesai =
                        data.menit_selesai ?? '-';

                    return mulai + ' - ' + selesai;
                }
            },

            {
                data: null,

                render: function (data) {

                    let mulai =
                        data.qty_mulai ?? '-';

                    let selesai =
                        data.qty_selesai ?? '-';

                    return mulai + ' - ' + selesai;
                }
            },

            {
                data: 'tipe_nilai',
                name: 'tipe_nilai'
            },

            {
                data: 'nilai',
                name: 'nilai_potongan'
            },

            {
                data: 'sumber_potongan',
                name: 'sumber_potongan',

                render: function (data) {
                    return data ?? '-';
                }
            },

            {
                data: 'status',
                name: 'is_active'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }

        ]

    });



</script>



@endsection