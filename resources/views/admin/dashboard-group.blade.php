@extends('layout.main-admin')
@section('tittle-admin')
  Group Jadwal
@endsection
@section('content-admin')

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Tambah Group</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{route('group.store')}}" method="post">
        @csrf
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Nama Group</label>
              <input type="text" name="nama_grup" class="form-control" id="inputEmail4">
            </div>
            <div class="form-group col-md-12">
              <label for="inputEmail4">Keterangan Group</label>
              <textarea name="keterangan" id="" class="form-control"></textarea>
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

@foreach ($data as $agm)
  <div class="modal fade" id="ubah-{{$agm->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="ubah-{{$agm->id}}Label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ubah-{{$agm->id}}Label">Ubah Data</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('agama.update', $agm->id)}}" method="post">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Agama</label>
                <input type="text" name="nama_agama" value="{{$agm->nama_agama}}" class="form-control" id="inputEmail4">
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

{{-- @foreach ($data as $dtl)
<div class="modal fade" id="detail-{{$dtl->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="detail-{{$dtl->id}}Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detail-{{$dtl->id}}Label">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{$dtl->}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>
@endforeach --}}


<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Group Jadwal Karyawan</h1>
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
                    <th>Nama Group</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama Group</th>
                  <th>Keterangan</th>
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
			url: "{{ route('group.index') }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'nama_grup', name: 'nama_grup'},
				{data: 'keterangan', name: 'keterangan'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		],
		});
</script>

@endsection
