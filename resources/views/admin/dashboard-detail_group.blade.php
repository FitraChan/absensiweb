@extends('layout.main-admin')
@section('tittle-admin')
  DetailGroup
@endsection
@section('content-admin')
<div class="container-fluid">

  <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Tambah Jam Kerja Group</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('detailGroup.store')}}" method="post">
        @csrf
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Nama Group</label>
                <input type="text" readonly value="{{$groupJadwal->nama_grup}}" class="form-control" id="inputEmail4">
                <input type="text" readonly hidden name="group_jadwal_id" value="{{$groupJadwal->id}}" class="form-control" >
              </div>
              <div class="form-group col-md-12">
                <label for="inputPassword4">Jam Kerja</label>
                <select name="jam_kerja_id" class="form-control" placeholder="Jam Kerja">
                  <option value=""></option>
                  @foreach ($jamKerja->get() as $dt)
                    <option value="{{$dt->id}}">{{$dt->nama_shift}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Tambah</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="karyawan" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="karyawanLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Tambah Karyawan Group</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('group.update', $groupJadwal->id)}}" method="post">
        @csrf
        @method('PUT')
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Nama Group</label>
                <input type="text" readonly value="{{$groupJadwal->nama_grup}}" class="form-control" id="inputEmail4">
                <input type="text" readonly hidden name="group_jadwal_id" value="{{$groupJadwal->id}}" class="form-control" >
              </div>
              <div class="form-group col-md-12">
                <label for="inputPassword4">Karyawan</label>
                <select name="karyawan_id" class="form-control" placeholder="Jabatan - Karyawan">
                  <option value=""></option>
                  @foreach ($kry as $dt)
                    <option value="{{$dt->id}}">{{$dt->jabatan->nama_jabatan}} - {{$dt->nama_lengkap}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Tambah</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Group Karyawan</h1>
    <a href="{{route('group.index')}}" class="btn btn-warning hover-left">
      <i class="fas fa-caret-left mr-2"></i>
      Kembali
    </a>
  </div>
  
  <div class="mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Detail Jam Kerja</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Nama Karyawan</a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
          <h5 class=" mb-0 text-gray">Jam Kerja Karyawan</h5>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
            <i class="fas fa-calendar-plus pr-3"></i>Tambah Jam Kerja
          </button>
        </div>

        <div class="table-responsive container">
          <table class="table table-bordered" id="dataTable">
              <thead>
                  <tr>
                      <th>No</th>
                      <th>Shift</th>
                      <th>Datang</th>
                      <th>Pulang</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Shift</th>
                    <th>Datang</th>
                    <th>Pulang</th>
                    <th>Action</th>
                  </tr>
              </tfoot>
              <tbody>
                  
              </tbody>
          </table>
      </div>
      </div>
      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
          <h5 class=" mb-0 text-gray">Karyawan dalam Group</h5>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#karyawan">
            <i class="fas fa-user-plus pr-3"></i>Tambah Karyawan
          </button>
        </div>

        <div class="table-responsive container">
          <table class="table table-bordered" id="table">
              <thead>
                  <tr>
                      <th>No</th>
                      <th>Nama Karyawan</th>
                      <th>Jabatan</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Action</th>
                  </tr>
              </tfoot>
              <tbody>
                  
              </tbody>
          </table>
      </div>
    </div>
  </div>
</div>

<style scoped>
  .hover-left i{
    transition: 0.5s;
  }
  .hover-left:hover > i{
    padding-right: 15px;
    transition: 0.5s;
  }
</style>

<script>
	var table = $('#dataTable').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax: {
			url: "{{ route('group.show', $groupJadwal->id) }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'jam_kerja_id', name: 'jam_kerja_id'},
				{data: 'datang', name: 'datang'},
				{data: 'pulang', name: 'pulang'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		  ],
		}); 
</script>

<script>
	var table2 = $('#table').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax: {
			url: "{{ route('group.edit', $groupJadwal->id) }}",
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'nama_lengkap', name: 'nama_lengkap'},
				{data: 'jabatan_id', name: 'jabatan_id'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		  ],
		}); 
</script>
<script type="application/javascript">
  function ubahData(target, msg) {
    Swal.fire({
      title: 'Yakin Mengubah 2??',
      text: "Pastikan Yang Kamu Pilih Benar",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
              type: "PUT",
              url: target,
              data:{
                  _token: '{{csrf_token()}}'
              },
              success: function(url){
                  console.log(url);
                  Swal.fire(
                      'Terhapus!',
                      'Status Berhasil Di Hapus !!',
                      'success'
                  ),
                  table2.draw();
              }
          });
        }
      })
  }
</script>
@endsection