@extends('layout.main-admin')
@section('tittle-admin')
  Jabatan
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
      <form action="{{route('jabatan.store')}}" method="post">
        @csrf
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Nama Jabatan</label>
              <input type="text" name="nama_jabatan" class="form-control" id="inputEmail4">
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
        <form action="{{route('jabatan.update', $dp->id)}}" method="post">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Nama Jabatan</label>
                <input type="text" name="nama_jabatan" value="{{$dp->nama_jabatan}}" class="form-control" id="inputEmail4">
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
    {{-- <button class="btn btn-primary">Tambah</button> --}}
  </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Jabatan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama Jabatan</th>
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
			url: "{{ route('jabatan.index') }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'nama_jabatan', name: 'nama_jabatan'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		],
		});
</script>

@endsection
