@extends('layout.main-admin')
@section('tittle-admin')
  Periode Gaji
@endsection
@section('content-admin')

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Tambah Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{route('gaji.store')}}" method="post">
        @csrf
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Nama Bulan</label>
              <input type="text" name="nama_periode" class="form-control" id="inputEmail4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Tanggal Mulai</label>
              <input type="date" name="mulai" class="form-control" id="inputEmail4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Tanggal Selesai</label>
              <input type="date" name="selesai" class="form-control" id="inputEmail4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Tanggal Cetak</label>
              <input type="date" value="{{date('Y-m-d', strtotime(now()))}}" name="tgl_cetak" class="form-control" id="inputEmail4">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="riset" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

@foreach ($data as $dp)
  <div class="modal fade" id="ubah-{{$dp->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="ubah-{{$dp->id}}Label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ubah-{{$dp->id}}Label">Ubah Data</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('gaji.update', $dp->id)}}" method="post">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Nama Bulan</label>
                <input type="text" name="nama_periode" value="{{$dp->nama_periode}}" class="form-control" id="inputEmail4">
              </div>
              <div class="form-group col-md-12">
                <label for="inputEmail4">Tanggal Mulai</label>
                <input type="datetime-local" name="mulai" value="{{date('Y-m-d\TH:i', strtotime($dp->mulai))}}" class="form-control" id="inputEmail4">
              </div>
              <div class="form-group col-md-12">
                <label for="inputEmail4">Tanggal Selesai</label>
                <input type="datetime-local" name="selesai" value="{{date('Y-m-d\TH:i', strtotime($dp->selesai))}}" class="form-control" id="inputEmail4">
              </div>
              <div class="form-group col-md-12">
                <label for="inputEmail4">Tanggal Cetak</label>
                <input type="datetime-local" name="tgl_cetak" value="{{date('Y-m-d\TH:i', strtotime($dp->tgl_cetak))}}" class="form-control" id="inputEmail4">
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
    <h1 class="h3 mb-0 text-gray-800">Periode Gaji Karyawan</h1>
    <button type="button" onclick="cek()" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
      Tambah
    </button>

  </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Bulan</th>
                    <th>Tanggal Awal</th>
                    <th>Tanggal Akhir</th>
                    <th>Tanggal Cetak</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama Bulan</th>
                  <th>Tanggal Awal</th>
                  <th>Tanggal Akhir</th>
                  <th>Tanggal Cetak</th>
                  <th>Action</th>
                </tr>
            </tfoot>
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
			url: "{{ route('gaji.index') }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'nama_periode', name: 'nama_periode'},
				{data: 'mulai', name: 'mulai'},
				{data: 'selesai', name: 'selesai'},
				{data: 'tgl_cetak', name: 'tgl_cetak'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		],
		});


  
</script>

@endsection
