@extends('layout.main-admin')
@section('tittle-admin')
  Pengaturan
@endsection
@section('content-admin')


<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

   <style>
       #map {
           height: 500px;
           width: 100%;
       }
   </style>

<div class="modal fade" id="LihatGambar" tabindex="-1" aria-labelledby="LihatGambarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="LihatGambarLabel">Gambar Profile Sebelumnya</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body m-auto">
        <img class="mw-100" src="{{asset('storage/profile/'.$data->gambar)}}" alt="" srcset="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengaturan</h1>
  </div>

  <form method="POST" action="{{route('update-profile', $data->id)}}" enctype="multipart/form-data" class="row">
    @csrf
    @method('PUT')

    <div class="col-md-6 mb-3">
      <label for="inputEmail4" class="form-label">Email</label>
      <input type="email" class="form-control" name="email" value="{{$data->email}}">
    </div>
    <div class="col-md-6 mb-3">
      <label for="inputEmail4" class="form-label">Nama Perusahaan</label>
      <input type="text" class="form-control" name="nama_perusahaan" value="{{$data->nama_perusahaan}}">
    </div>
    <div class="col-md-6 mb-3">
      <label for="inputEmail4" class="form-label">No Telpon</label>
      <input type="number" class="form-control" name="telepon" value="{{$data->telepon}}">
    </div>
    <div class="col-md-6 mb-3">
      <label for="inputEmail4" class="form-label">Link Website</label>
      <input type="text" class="form-control" name="website" value="{{$data->website}}">
    </div>
    <div class="col-12 mb-3">
      <label for="inputAddress" class="form-label">Alamat</label>
      <input type="text" class="form-control" name="alamat" value="{{$data->alamat}}">
    </div>
    <div class="col-6 mb-3">
      <label for="inputAddress" class="form-label pb-2">Map</label>
      <div id="map"></div>
    </div>
    <div class="col-12 mb-3">

      <p for="inputAddress" class="form-label">Lattitude</p>
       <input type="text" class="form-control" name="lat" id="lat">

    </div>


    <div class="col-12 mb-3">


       <p for="inputAddress" class="form-label">Langitude</p>
        <input type="text" class="form-control" name="lang" id="lang">
    </div>

    <div class="col-12 mt-2">
      <button type="submit" class="btn btn-primary">Ubah</button>
    </div>
  </form>

</div>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

   <script>
   var map = L.map('map').setView([-8.461505694920898, 115.25756353573254], 10);
  // console.log(L.latlng);
 //dark
       var dark_matrix = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
       dark_matrix.addTo(map);

    //   L.Control.geocoder().addTo(map);

    var geocoder = L.Control.geocoder({
        defaultMarkGeocode: false
    }).addTo(map);

    var marker;

    // Tangani event geocode
    geocoder.on('markgeocode', function(e) {
      var bbox = e.geocode.bbox;
        var bounds = L.latLngBounds(bbox);
        map.fitBounds(bounds);

        var latlng = e.geocode.center;

        document.getElementById("lat").value =  latlng.lat;
        document.getElementById("lang").value =  latlng.lng;

        // Jika marker sudah ada, update posisinya
        if (marker) {
          marker.setLatLng(latlng);
        } else {
            // Jika marker belum ada, buat marker baru
            marker = L.marker(latlng, {
                draggable: true // Aktifkan fitur drag
            }).addTo(map);
        }

        // Event listener untuk mendapatkan posisi marker setelah di-drag
        marker.on('dragend', function() {
            var position = marker.getLatLng();

               document.getElementById("lat").value =  position.lat;
                document.getElementById("lang").value =  position.lng;
        });
    });


   </script>

@endsection
