@extends('layout.main-admin')
@section('tittle-admin')
  Pengguna
@endsection
@section('content-admin')

<!-- Modal -->

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Pengguna</h1>
  </div>
  <div class="row mb-5">
  <div class="col-sm-2">
    <a href="{{url('fetchGaji')}}" class="btn btn-primary mt-3">Fetch Gaji</a>
  </div>

  <div class="col-sm-1">
    <a href="{{url('downloadMultipleGaji')}}" class="btn btn-primary mt-3">PDF Payroll</a>
  </div>

  <div class="col-sm-1">
    <a href="{{url('downloadMultipleAbsen')}}" class="btn btn-primary mt-3">PDF Absensi</a>
  </div>
</div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Dapartement</th>
                    <th>Jabatan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Dapartement</th>
                  <th>Jabatan</th>
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
			url: "{{ route('penggajian.index') }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'nama_lengkap', name: 'nama_lengkap'},
				{data: 'email', name: 'email'},
				{data: 'departement_id', name: 'departement_id'},
				{data: 'jabatan_id', name: 'jabatan_id'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		],
		});


    function gaji(id) {


      var uri = "{{ url('gaji')  }}"+"/"+id;
      var win = window.open(uri, '_blank');


    }
</script>



@endsection
