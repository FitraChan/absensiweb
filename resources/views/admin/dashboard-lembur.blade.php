@extends('layout.main-admin')
@section('tittle-admin')
  Jabatan
@endsection
@section('content-admin')

<!-- Modal -->

<!-- Modal Tambah Lembur -->
<div
    class="modal fade"
    id="staticBackdrop"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    aria-labelledby="staticBackdropLabel"
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    Tambah Data Lembur
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form
                action="{{ route('lembur.store') }}"
                method="POST"
            >
                @csrf

                <div class="modal-body">

                    <!-- Nama Karyawan -->
                    <div class="form-group">
                        <label for="karyawan_id">
                            Nama Karyawan
                        </label>

                        <select
                            name="karyawan_id"
                            id="karyawan_id"
                            class="form-control"
                            required
                        >
                            <option value="">
                                -- Pilih Karyawan --
                            </option>

                            @foreach ($karyawan as $k)
                                <option value="{{ $k->id }}">
                                    {{ $k->nama_lengkap }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- Durasi -->
                    <div class="form-group">
                        <label for="durasi">
                            Durasi (Jam)
                        </label>

                        <input
                            type="number"
                            name="durasi"
                            id="durasi"
                            class="form-control"
                            min="1"
                            step="0.5"
                            placeholder="Contoh: 2"
                            required
                        >
                    </div>

                    <!-- Keterangan -->
                    <div class="form-group">
                        <label for="keterangan">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            id="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan keterangan lembur"
                            required
                        ></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>



@foreach ($data as $dp)
  <div class="modal fade" id="data-{{$dp->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="data-{{$dp->id}}Label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="data-{{$dp->id}}Label">Ubah Data</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('lembur.update', $dp->id)}}" method="post">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Nama Karyawan</label>
                <input type="text" value="{{$dp->karyawan->nama_lengkap}}" readonly class="form-control" id="inputEmail4">
                <input type="hidden" name="id" value="{{$dp->id}}" readonly class="form-control" id="inputEmail4">
                <input type="hidden" name="karyawan_id" value="{{$dp->karyawan->id}}" readonly class="form-control" id="inputEmail4">

              </div>
              <div class="form-group col-md-12">
                <label for="inputEmail4">Jabatan</label>
                <input type="tetx" value="{{$dp->karyawan->jabatan->nama_jabatan}}" readonly class="form-control" id="inputEmail4">
              </div>

              <div class="form-group col-md-12">
                <label for="inputEmail4">Tanggal Pengajuan</label>

                <?php  $date =  date('d-m-Y H:i:s', strtotime($dp->tgl_pengajuan)); ?>
                <input type="tetx" value="{{$date }}" readonly class="form-control" id="inputEmail4">
              </div>


              <div class="form-group col-md-6">
                <label for="inputPassword4">Status Pesetujuan</label>
                <select name="sts_pengajuan" class="form-control">
                  <option value=0>--Pilih--</option>
                  <option value=1>DISETUJUI</option>
                  <option value=2>TIDAK DISETUJUI</option>
                </select>
              </div>

              <div class="form-group col-md-12">
                <label for="inputPassword4">Keterangan</label>
                <textarea name="" readonly class="form-control">{{$dp->keterangan}}</textarea>
              </div>


            </div>
          </div>
          <div class="modal-footer">
            <button type="riset" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Ubah</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach


<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Jabatan</h1>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
      Tambah
    </button>
 
  </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Durasi (jam)</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status Pengajuan</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script>
	var table = $('#dataTable').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax: {
			url: "{{ route('lembur.index') }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'karyawan.nama_lengkap', name: 'karyawan.nama_lengkap'},
        	{data: 'durasi', name: 'durasi'},
          {data: 'tgl_pengajuan', name: 'tgl_pengajuan'},
          {data: 'sts_pengajuan', name: 'sts_pengajuan'},


				{data: 'action', name: 'action', orderable: false, searchable: false},
		],
		});
</script>

@endsection
