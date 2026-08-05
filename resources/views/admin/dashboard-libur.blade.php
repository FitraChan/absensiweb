@extends('layout.main-admin')
@section('tittle-admin')
  Setting Libur
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
      <form action="{{url('penentuanLiburStore')}}" method="post">
        @csrf
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Nama Libur</label>
              <input type="text" name="nama_libur" class="form-control" id="inputEmail4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Tanggal Mulai</label>
              <input type="date" name="tanggal_mulai" class="form-control" id="inputEmail4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Tanggal Akhir</label>
              <input type="date" name="tanggal_akhir" class="form-control" id="inputEmail4">
            </div>
          </div>
          <div class="form-row">

              <div class="form-group col-md-12">
                <label for="inputPassword4">Tipe</label>
                <select name="tipe" class="form-control">
                  <option value=0>--Pilih--</option>
                  <option value=1>Public</option>
                  <option value=2>Perusahaan</option>
                </select>
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
        <form action="{{url('penentuanLiburUpdate',$dp->id )}}" method="post">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="inputEmail4">Nama Libur</label>
                <input type="text" name="nama_libur" value="{{$dp->nama_libur}}" class="form-control" id="inputEmail4">
                <input type="hidden" name="id" value="{{$dp->id}}" class="form-control" id="inputEmail4">


              </div>

              <?php

              $tanggalRange = json_decode($dp->tanggal);

              $tanggalMulai = \Carbon\Carbon::parse($tanggalRange[0])->format('Y-m-d');
              $tanggalAkhir = \Carbon\Carbon::parse($tanggalRange[1])->format('Y-m-d');

               ?>
              <div class="form-group col-md-12">
                <label for="inputEmail4">Waktu Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="form-control" id="inputEmail4">
              </div>
              <div class="form-group col-md-12">
                <label for="inputEmail4">Waktu Selesai</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="form-control" id="inputEmail4">
              </div>



                  <div class="form-group col-md-12">
                    <label for="inputPassword4">Tipe</label>
                    <select name="tipe" class="form-control">
                      <option {{ 1 == $dp->tipe?'selected':''}} value="{{ 1 }}"> Public </option>
                      <option {{ 2 == $dp->tipe?'selected':''}} value="{{ 2 }}"> Perusahaan </option>
                    </select>
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
    <h1 class="h3 mb-0 text-gray-800">Setting Libur</h1>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
      Tambah
    </button>

  </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Hari Libur</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            </tbody>
        </table>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script>
	var table = $('#dataTable').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax:  "{{ url('penentuanLibur') }}",

  		columns: [
  				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
  				{data: 'nama_libur', name: 'nama_libur'},
          {
            data: 'tanggal',
            title: 'Tanggal',
            render: function(data, type, row) {

            //  console.log(data);
            let decoded = $('<div>').html(data).text(); // pakai jQuery

              let tanggalArray = JSON.parse(decoded);

              console.log(tanggalArray[0]); // "2025-04-22"

                if (Array.isArray(tanggalArray) ) {
                    const start = moment(tanggalArray[0]).format('DD-MM-YYYY');
                    const end = moment(tanggalArray[1]).format('DD-MM-YYYY');
                    return `${start} s/d ${end}`;
                } else {
                    return '-';
                }
            }
        },
        {data: 'tipe', name: 'tipe'},
  			{data: 'action', name: 'action', orderable: false, searchable: false},
  		],
		});
</script>

@endsection
