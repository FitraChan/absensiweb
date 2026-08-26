@extends('layout.main-admin')

@section('tittle-admin')
Item Gaji
@endsection

@section('content-admin')

{{-- MODAL TAMBAH ITEM GAJI --}}
<div class="modal fade"
    id="staticBackdrop"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    aria-labelledby="staticBackdropLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="staticBackdropLabel">
                    Tambah Item Gaji
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>


            <form
                action="{{ route('item-gaji.store') }}"
                method="POST">

                @csrf

                <div class="modal-body">

                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label>
                                Nama Item Gaji
                            </label>

                            <input
                                type="text"
                                name="nama_item_gaji"
                                class="form-control"
                                value="{{ old('nama_item_gaji') }}"
                                required>

                        </div>

                    </div>


                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label>
                                Kategori Item
                            </label>

                            <select
                                name="kategori_item_id"
                                class="form-control"
                                required>

                                <option value="">
                                    -- Pilih Kategori --
                                </option>

                                @foreach($kategori as $item)

                                <option
                                    value="{{ $item->id }}"
                                    {{ old('kategori_item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_kategori }}
                                </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label>
                                No Urut
                            </label>

                            <input
                                type="number"
                                name="no_urut"
                                class="form-control"
                                value="{{ old('no_urut') }}"
                                min="1"
                                required>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Close

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Tambah

                    </button>

                </div>

            </form>

        </div>
    </div>

</div>


{{-- MODAL EDIT ITEM GAJI --}}
@foreach ($rows as $dp)

<div class="modal fade"
    id="ubah-{{ $dp->id }}"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    aria-labelledby="ubah-{{ $dp->id }}Label"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="ubah-{{ $dp->id }}Label">

                    Ubah Item Gaji

                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>


            <form
                action="{{ route('item-gaji.update', $dp->id) }}"
                method="POST">

                @csrf
                @method('PUT')


                <div class="modal-body">


                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label>
                                Nama Item Gaji
                            </label>

                            <input
                                type="text"
                                name="nama_item_gaji"
                                value="{{ $dp->nama_item_gaji }}"
                                class="form-control"
                                required>

                        </div>

                    </div>


                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label>
                                Kategori Item
                            </label>

                            <select
                                name="kategori_item_id"
                                class="form-control"
                                required>

                                <option value="">
                                    -- Pilih Kategori --
                                </option>

                                @foreach($kategori as $item)

                                <option
                                    value="{{ $item->id }}"
                                    {{ $dp->kategori_item_id == $item->id ? 'selected' : '' }}>

                                    {{ $item->nama_kategori }}

                                </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label>
                                No Urut
                            </label>

                            <input
                                type="number"
                                name="no_urut"
                                value="{{ $dp->no_urut }}"
                                class="form-control"
                                min="1"
                                required>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Close

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Ubah

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endforeach
<div class="container-fluid">

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Item Gaji</h1>
   
  </div>

<div class="mb-3">
    <button
        type="button"
        class="btn btn-primary mb-3"
        data-toggle="modal"
        data-target="#staticBackdrop">

        + Tambah Item Gaji

    </button>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

 

<div class="table-responsive">

    <table class="table table-bordered" id="dataTable">

        <thead>
            <tr>
                <th>No</th>
                <th>Nama Item Gaji</th>
                <th>Kategori</th>
                <th>No Urut</th>
                <th>Action</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <th>No</th>
                <th>Nama Item Gaji</th>
                <th>Kategori</th>
                <th>No Urut</th>
                <th>Action</th>
            </tr>
        </tfoot>

        <tbody>

            @foreach($rows as $index => $item)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $item->nama_item_gaji }}
                </td>

                <td>
                    {{ $item->kategoriItems->nama_kategori ?? '-' }}
                </td>

                <td>
                    {{ $item->no_urut }}
                </td>

                <td>

                    <button
                        type="button"
                        class="btn btn-warning btn-sm"
                        data-toggle="modal"
                        data-target="#ubah-{{ $item->id }}">

                        Edit

                    </button>

                    <form
                        action="{{ route('item-gaji.destroy', $item->id) }}"
                        method="POST"
                        style="display:inline;"
                        onsubmit="return confirm('Yakin ingin menghapus item gaji ini?')">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger btn-sm">
                            Hapus
                        </button>

                    </form>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>
</div>

@endsection