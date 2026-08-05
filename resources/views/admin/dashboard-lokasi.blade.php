@extends('layout.main-admin')
@section('tittle-admin')
  Detail Lokasi
@endsection
@section('content-admin')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-5">
    <h1 class="h3 mb-0 text-gray-800">Lokasi Absen Karyawan</h1>
    <a href="{{route('karywan-absen', $absensi->karyawan_id)}}" class="btn btn-warning hover-left">
      <i class="fas fa-caret-left mr-2"></i>
      Kembali
    </a>
  </div>

  @if ($absensi->posisi_masuk == null && $absensi->posisi_pulang==null)
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    Karyawan Belum Melakukan Relokasi
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

    @php
    if ($absensi->posisi_masuk != null && $absensi->posisi_pulang != null)
      $locations = [
        ['Posisi Masuk', (float)$absensi->posisi_masuk, (float)strrchr( $absensi->posisi_masuk, ' ')],
        ['Posisi Pulang', (float)$absensi->posisi_pulang, (float)strrchr( $absensi->posisi_pulang, ' ')],
      ];
    else if($absensi->posisi_masuk != null )
      $locations = [
        ['Posisi Masuk', (float)$absensi->posisi_masuk, (float)strrchr( $absensi->posisi_masuk, ' ')],
      ];
    else if($absensi->posisi_pulang != null)
      $locations = [
        ['Posisi Pulang', (float)$absensi->posisi_pulang, (float)strrchr( $absensi->posisi_pulang, ' ')],
      ];
    else
      $locations = [];
    @endphp

  <div id="map" style="height: 500px; width: 65rem" class="m-auto"></div>
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

<script type="text/javascript">
  function initMap() {
      const myLatLng = { lat: -8.5050152, lng: 114.9991588 };
      const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 10,
          center: myLatLng,
      });

      var locations = {{ Js::from($locations) }};
      console.log(locations);

      var infowindow = new google.maps.InfoWindow();

      var marker, i;
            
      for (i = 0; i < locations.length; i++) {  
            marker = new google.maps.Marker({
              position: new google.maps.LatLng(locations[i][1], locations[i][2]),
              map: map
            });
              
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
              return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
              }
            })(marker, i));
      }
  }

  window.initMap = initMap;
</script>
@endsection