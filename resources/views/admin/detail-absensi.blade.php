@extends('layout.main-admin')
@section('tittle-admin')
  Absensi
@endsection
@section('content-admin')

{{-- modal --}}

@foreach ($karyawan->absensi as $abs)
<div class="modal fade" id="masuk-{{$abs->id}}" tabindex="-1" aria-labelledby="masuk-{{$abs->id}}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="masuk-{{$abs->id}}Label">Gambar Masuk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body m-auto">
        {{-- <img style="max-width: 100%" src="{{asset('storage/absen_masuk/'.$abs->url_masuk)}}" alt=""> --}}
        <img style="max-width: 100%" src="{{$abs->url_masuk}}" alt="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

@foreach ($karyawan->absensi as $abp)
<div class="modal fade" id="pulang-{{$abp->id}}" tabindex="-1" aria-labelledby="pulang-{{$abp->id}}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pulang-{{$abp->id}}Label">Gambar Pulang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body m-auto">
        <img style="max-width: 100%" src="{{asset('storage/absen_keluar/'.$abp->url_keluar)}}" alt="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

@foreach ($karyawan->absensi as $lks)
<div class="modal fade" id="lokasi-{{$lks->id}}" tabindex="-1" aria-labelledby="lokasi-{{$lks->id}}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="lokasi-{{$lks->id}}Label">Lokasi {{$lks->posisi_masuk}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body m-auto">
        <input type="text" name="post_masuk" id="post_masuk-{{$lks->id}}" value="{{$lks->posisi_masuk}}">
          @php
            $locations = ['Posisi Masuk', (float)$lks->posisi_masuk, (float)strrchr( $lks->posisi_masuk, ' ')];
          @endphp
        <div style="height: 500px; width: 500px" id="map"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

<div class="modal fade" id="download" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="downloadLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="downloadLabel">Download Absensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{route('download-detail-absen')}}" method="post">
        @csrf
        <div class="modal-body">
          <label for="">Periode</label>
          <select class="form-control" name="periode" id="">
            @foreach ($periodeGaji->get() as $pd)
              <option value="{{$pd->id}}">{{$pd->nama_periode}}</option>
            @endforeach
          </select>
          <input type="text" name="karyawan_id" readonly hidden value="{{$karyawan->id}}">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-download pr-3"></i>Download</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Absensi Karyawan</h1>
    <a href="{{route('absensi.index')}}" class="btn btn-warning hover-left">
      <i class="fas fa-caret-left mr-2"></i>
      Kembali
    </a>
  </div>
  <div class="mb-5">
    <h5>Nama   : {{$karyawan->nama_lengkap}}</h5>
    <h5>Divisi : {{$karyawan->departement->nama_departement}}</h5>
  </div>

  

    <div class="row mb-5">
      <div class="col-sm-5">
        <input type="number" class="form-control" id="bulan" name="bulan" placeholder="Bulan Absen (01 - 12)">
      </div>
      <div class="col-sm-5">
        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Tahun Absen">
      </div>
      {{-- <div class="col-sm-5">
        <select name="periode" id="">
          @foreach ($periodeGaji->get() as $pd)
            <option value="{{$pd->id}}">{{$pd->nama_periode}}</option>
          @endforeach
        </select>
      </div> --}}
      <div class="col-sm-2">
        {{-- <a href="{{route('download-detail-absen',$karyawan->id)}}" class="btn btn-primary text-light"><i class="fas fa-download pr-3"></i>Download</a> --}}
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#download">
          <i class="fas fa-download pr-3"></i>Download
        </button>
      </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Gambar Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Gambar Pulang</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Jam Masuk</th>
                  <th>Gambar Masuk</th>
                  <th>Jam Pulang</th>
                  <th>Gambar Pulang</th>
                  <th>Lokasi</th>
                  <th>Status</th>
                </tr>
            </tfoot>
            <tbody>
                
            </tbody>
        </table>
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
			url: "{{ route('karywan-absen', $karyawan->id) }}",
      data:function(d){
        d.tahun = $('#tahun').val(),
        d.bulan = $('#bulan').val(),
        d.search = $('input[type="search"]').val()
      }
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'tanggal', name: 'tanggal'},
				{data: 'jam_masuk', name: 'jam_masuk'},
				{data: 'url_masuk', name: 'url_masuk'},
				{data: 'jam_pulang', name: 'jam_pulang'},
				{data: 'url_keluar', name: 'url_keluar'},
				{data: 'lokasi-d', name: 'lokasi-d'},
				{data: 'status_absensi', name: 'status_absensi'},
				// {data: 'action', name: 'action', orderable: false, searchable: false},
		  ],
		}); 
    console.log(table);
    $("#tahun").keyup(function(){
        table.draw();
    });
    $("#bulan").keyup(function(){
        table.draw();
    });
</script>

<script type="text/javascript">
  function initMap() {
      const myLatLng = { lat: -8.5050152, lng: 114.9991588 };
      const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 9,
          center: myLatLng,
      });

      var locations = {{ Js::from($locations) }};
      console.log(locations);

      var infowindow = new google.maps.InfoWindow();

      var marker, i;
      
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[1], locations[2]),
        map: map
      });
      google.maps.event.addListener(marker, 'click', (function(marker) {
        return function() {
          infowindow.setContent(locations[0]);
          infowindow.open(map, marker);
        }
      })(marker));
      // for (i = 0; i < locations.length; i++) {  
      //       marker = new google.maps.Marker({
      //         position: new google.maps.LatLng(locations[i][1], locations[i][2]),
      //         map: map
      //       });
              
      //       google.maps.event.addListener(marker, 'click', (function(marker, i) {
      //         return function() {
      //           infowindow.setContent(locations[i][0]);
      //           infowindow.open(map, marker);
      //         }
      //       })(marker, i));
      // }
  }

  window.initMap = initMap;
</script>

@endsection