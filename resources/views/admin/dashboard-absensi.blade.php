@extends('layout.main-admin')
@section('tittle-admin')
  Absensi
@endsection
@section('content-admin')

  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


  </style>

  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>

  </script>

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="modal fade" id="donwload" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="donwloadLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="donwloadLabel">Download Absensi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('download-absen')}}" method="POST">
        @csrf
        <div class="modal-body">
          <label>Periode</label>
          <select name="periode" id="">
            @foreach ($periodeGaji->get() as $pd)
              <option value="{{$pd->id}}">{{$pd->nama_periode}}</option>
            @endforeach
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-download pr-3"></i>Download</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Data Absensi</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Tambah Absensi</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Karyawan</h1>
      </div>
      <div class="mb-4 d-flex justify-content-start">
        {{-- <input type="number" name="" class="form-control w-25" placeholder="Tahun Absen"> --}}
        {{-- <a class="btn btn-primary text-light ml-4" href="{{route('download-absen')}}"><i class="fas fa-download pr-3"></i>Download</a> --}}
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#donwload">
          <i class="fas fa-download pr-3"></i>Download
        </button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Dapartemen</th>
              <th>Jabatan</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Dapartemen</th>
              <th>Jabatan</th>
              <th>Action</th>
            </tr>
          </tfoot>
          <tbody>

          </tbody>
        </table>
    </div>
    </div>
    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Absensi Karyawan</h1>
      </div>

      <div class="container">
        <form method="POST" action="{{route('absensi.store')}}">
          @csrf
          <div class="form-row">
            <div class="col-md-6 mb-3">
              <label for="validationDefault01">Tanggal Absen</label>
              <input type="date" id="tanggal" value="{{date('Y-m-d', strtotime(now()) )}}" name="tanggal" class="form-control">

            </div>
            <div class="col-md-6 mb-3">
              <label for="validationDefault02">Karyawan</label>
              <select name="karyawan_id" id="karyawan_id2" placeholder="Jabatan - Karyawan" class="form-control">
                  <option value=""></option>
                @foreach ($data as $gj)
                  <option value="{{$gj->id}}">{{$gj->jabatan?->nama_jabatan}} - {{$gj?->nama_lengkap}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="validationDefault01">Waktu</label>
              <input type="datetime-local" name="waktu" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label for="validationDefault01">Keterangan</label>
              <select name="kt" id="" placeholder="Waktu Absensi"  class="form-control">
                <option value=""></option>
                <option value="in">In</option>
                <option value="out">Out</option>
              </select>
            </div>


             <div class="col-md-6 mb-3">
              <label for="validationDefault01">Jabatan</label>
              <select id="jabatan" placeholder="jabatan"  class="form-control">
                <option value=""></option>
                <option value="1">CEO</option>
                <option value="2">Pemasaran</option>
              </select>
            </div>




          </div>
          <button class="btn btn-primary mt-3" type="submit">Tambah</button>
        </form>



        <div class="table-responsive mt-5">
          <table class="table table-bordered" id="data">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
              </tr>
            </tfoot>
            <tbody>

            </tbody>
          </table>
      </div>

      </div>
    </div>
  </div>
</div>


<script>
	var table = $('#dataTable').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax: {
			url: "{{ route('absensi.index') }}"
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
</script>
<script>
	var table2 = $('#data').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax: {
			url: "{{ route('absensi.create') }}",
      data:function(d){
        d.karyawan_id = $('#karyawan_id2').val(),
        d.tanggal = $('#tanggal').val(),
        d.search = $('input[type="search"]').val()
      }
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'karyawan_id', name: 'karyawan_id'},
				{data: 'tanggal', name: 'tanggal'},
				{data: 'jam_masuk', name: 'jam_masuk'},
				{data: 'jam_pulang', name: 'jam_pulang'},
		],
		});

    $(function(){
      $("#tanggal").change(function(){
          table2.draw();
      });
      $("#karyawan_id2").change(function(){
          table2.draw();
      });
    });

      $(document).ready(function () {

            $('#jabatan').on('change', function () {

                var idJabatan = this.value;



                $("#state-dropdown").html('');

                $.ajax({

                    url: "{{url('fetchKaryawan')}}",

                    type: "POST",

                    data: {

                        jabatan_id: idJabatan,

                        _token: '{{csrf_token()}}'

                    },

                    dataType: 'json',

                    success: function (result) {
                      var html = '';

                      console.log(result.states[0]);

                      var $select = $('#karyawan_pilihan').selectize({
                        maxItems: null,
                        valueField: 'id',
                        labelField: 'nama_lengkap',
                        searchField: 'nama_lengkap',
                        options: [
                          // {id: 1, nama_lengkap: 'Spectrometer', url: 'http://en.wikipedia.org/wiki/Spectrometers'},
                          // {id: 2, nama_lengkap: 'Star Chart', url: 'http://en.wikipedia.org/wiki/Star_chart'},
                          // {id: 3, nama_lengkap: 'Electrical Tape', url: 'http://en.wikipedia.org/wiki/Electrical_tape'}
                              result.states[0]


                        ],
                        create: false
                      });
                      

                    }

                });

            });

        });


</script>

  
  @endsection
