@extends('layout.main-admin')

@section('tittle-admin')
    Tambah Profile
@endsection

@section('content-admin')

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">

<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
    }
</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 text-gray-800">
            Tambah Profile Perusahaan
        </h1>

        <a href="{{ route('profile',1) }}"
           class="btn btn-secondary">

            <i class="fa fa-arrow-left"></i>
            Kembali

        </a>

    </div>


    <form method="POST" action="{{ route('profile.store') }}">

        @csrf

        <div class="card shadow-sm">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Email</label>

                        <input type="email"
                               class="form-control"
                               name="email"
                               value="{{ old('email') }}">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label>Nama Perusahaan</label>

                        <input type="text"
                               class="form-control"
                               name="nama_perusahaan"
                               value="{{ old('nama_perusahaan') }}"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label>No Telepon</label>

                        <input type="text"
                               class="form-control"
                               name="telepon"
                               value="{{ old('telepon') }}">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label>Website</label>

                        <input type="text"
                               class="form-control"
                               name="website"
                               value="{{ old('website') }}">

                    </div>


                    <div class="col-12 mb-3">

                        <label>Alamat</label>

                        <textarea class="form-control"
                                  name="alamat"
                                  rows="3">{{ old('alamat') }}</textarea>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label>Latitude</label>

                        <input type="text"
                               class="form-control"
                               name="lat"
                               id="lat"
                               value="{{ old('lat') }}">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label>Longitude</label>

                        <input type="text"
                               class="form-control"
                               name="lang"
                               id="lang"
                               value="{{ old('lang') }}">

                    </div>


                    <div class="col-12 mb-3">

                        <label class="mb-2">
                            Lokasi Kantor
                        </label>

                        <div id="map"></div>

                    </div>

                </div>

            </div>


            <div class="card-footer bg-white">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Simpan

                </button>

                <a href="{{ route('profile', 1) }}"
                   class="btn btn-secondary">

                    Batal

                </a>

            </div>

        </div>

    </form>

</div>


<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"
/>

<style>
    #map {
        width: 100%;
        height: 500px;
    }
</style>

<div id="map"></div>

<!-- Input koordinat -->
<input type="text" id="lat" name="lat">
<input type="text" id="lang" name="lang">


<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>

var map = L.map('map').setView(
    [-8.461505694920898, 115.25756353573254],
    10
);

L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        attribution: '&copy; OpenStreetMap contributors'
    }
).addTo(map);


var marker;


/**
 * Set marker dan update koordinat
 */
function setMarker(lat, lng)
{
    var latlng = [lat, lng];

    document.getElementById('lat').value = lat;
    document.getElementById('lang').value = lng;

    if (marker) {

        marker.setLatLng(latlng);

    } else {

        marker = L.marker(latlng, {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function() {

            var position = marker.getLatLng();

            document.getElementById('lat').value =
                position.lat;

            document.getElementById('lang').value =
                position.lng;

        });
    }

    map.setView(latlng, 17);
}


/**
 * Klik peta
 */
map.on('click', function(e) {

    setMarker(
        e.latlng.lat,
        e.latlng.lng
    );

});


/**
 * PENCARIAN LOKASI
 */
var geocoder = L.Control.geocoder({

    defaultMarkGeocode: false,

    placeholder: 'Cari alamat / lokasi...',

    errorMessage: 'Lokasi tidak ditemukan',

    showResultIcons: true

})
.on('markgeocode', function(e) {

    var result = e.geocode;

    var lat = result.center.lat;
    var lng = result.center.lng;

    // Set marker
    setMarker(lat, lng);

})
.addTo(map);

</script>

@endsection