@extends('layout.main-admin')
@section('tittle-admin')
  Absensi
@endsection
@section('content-admin')

{{-- modal --}}
@foreach ($data->get() as $lks)

<!-- Modal -->
<div class="modal fade" id="data-{{$lks->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="data-{{$lks->id}}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="data-{{$lks->id}}Label">Penyetujuan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('karywanAbsen.update', $lks->id)}}">

          <?php   $dates = json_decode($lks->tanggal, true);  ?>
          @csrf
          @method('PUT')
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail4">Nama Karyawan</label>
              <input type="tetx" value="{{$lks->karyawan->nama_lengkap}}" readonly class="form-control" id="inputEmail4">
              <input type="hidden" name="id" value="{{$lks->id}}" readonly class="form-control" id="inputEmail4">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail4">Jabatan</label>
              <input type="tetx" value="{{$lks->karyawan->jabatan->nama_jabatan}}" readonly class="form-control" id="inputEmail4">
            </div>
        <?php
          $a = 1;
        for ($i=0; $i < $lks->durasi ; $i++) {

            if(!empty($dates[$i]) ){

              $tanggal = date('d-m-Y', strtotime($dates[$i]));
                       ?>
                        <div class="form-group col-md-6">
                          <label for="inputPassword4">Tanggal {{ $a++ }}</label>
                          <input type="text" value="{{ $tanggal }}" readonly class="form-control" id="inputPassword4">
                        </div>
                    <?php }
        }
        ?>
            <div class="form-group col-md-6">
              <label for="inputPassword4">Durasi</label>
              <input type="text" value="{{$lks->durasi}}" readonly class="form-control" id="inputPassword4">
            </div>
            <div class="form-group col-md-12">
              <label for="inputPassword4">Keperluan</label>
              <textarea name="" readonly class="form-control">{{$lks->keperluan}}</textarea>
            </div>
            <div class="form-group col-md-6">
              <label for="inputPassword4">Tanggal Pesetujuan</label>
              <input type="text" name="tgl_persetujuan" value="{{date(now())}}" readonly class="form-control" id="inputPassword4">
            </div>
            <div class="form-group col-md-6">
              <label for="inputPassword4">Status Pesetujuan</label>
              <select name="status" class="form-control">
                <option value="MENUNGGU">MENUNGGU</option>
                <option value="DISETUJUI">DISETUJUI</option>
                <option value="TIDAK DISETUJUI">TIDAK DISETUJUI</option>
              </select>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Ubah</button>
      </div>
    </form>
    </div>
  </div>
</div>




@endforeach

<div
    class="modal fade"
    id="modal-status-absensi"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    aria-labelledby="modal-status-absensi-Label"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5
                    class="modal-title"
                    id="modal-status-absensi-Label"
                >
                    Update Status Absensi
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('absensi-status.update-range') }}"
            >
                @csrf
                @method('POST')

                <div class="modal-body">

                    <div class="form-row">

                        {{-- Karyawan --}}
                        <div class="form-group col-md-6">
                            <label for="karyawan_id">
                                Nama Karyawan
                            </label>

                            <select
                                name="karyawan_id"
                                id="karyawan_id"
                                class="form-control"
                                required
                            >
                                <option value="">
                                    -- Pilih Karyawan --
                                </option>

                                @foreach ($karyawan as $item)
                                    <option
                                        value="{{ $item->id }}">
                                        {{ $item->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                       

                        {{-- Tanggal Mulai --}}
                        <div class="form-group col-md-6">
                            <label for="tanggal_mulai_{{ $lks->id }}">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="tanggal_mulai"
                                class="form-control"
                                value="{{ old('tanggal_mulai') }}"
                                required
                            >
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div class="form-group col-md-6">
                            <label for="tanggal_selesai">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                id="tanggal_selesai"
                                class="form-control"
                                value="{{ old('tanggal_selesai') }}"
                                required
                            >
                        </div>

                        {{-- Status Absensi --}}
                        <div class="form-group col-md-6">
                            <label for="status_absensi">
                                Status Absensi
                            </label>

                            <select
                                name="status_absensi"
                                id="status_absensi"
                                class="form-control"
                                required
                            >
                                <option value="">
                                    -- Pilih Status --
                                </option>

                                <option value="I">
                                    IZIN
                                </option>

                                <option value="S">
                                    SAKIT
                                </option>

                                <option value="A">
                                    ALPHA
                                </option>

                                <option value="C">
                                    CUTI
                                </option>
                            </select>
                        </div>

                        {{-- Tanggal Persetujuan --}}
                        <div class="form-group col-md-6">
                            <label>
                                Tanggal Persetujuan
                            </label>

                            <input
                                type="text"
                                name="tgl_persetujuan"
                                value="{{ now()->format('d-m-Y H:i:s') }}"
                                class="form-control"
                                readonly
                            >
                        </div>

                        {{-- Keterangan --}}
                        <div class="form-group col-md-12">
                            <label for="keterangan">
                                Keterangan
                            </label>

                            <textarea
                                name="keterangan"
                                id="keterangan"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan alasan izin, sakit, alpha, atau cuti"
                            >{{ old('keterangan') }}</textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Tutup
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        onclick="return confirm(
                            'Apakah Anda yakin ingin mengubah status absensi pada rentang tanggal tersebut?'
                        )"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>




<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Keterangan Absensi Karyawan</h1>
  </div>

  <button
    type="button"
    class="btn btn-primary"
    data-toggle="modal"
    data-target="#modal-status-absensi"
>
    Tambah Izin
</button>

<br />

<br />

    <div class="row mb-5">
      <div class="col-sm-5">
        {{-- <input type="text" class="form-control" id="bulan" name="bulan" placeholder="Bulan Absen"> --}}
        <select name="bulan" id="bulan" class="form-control">
          <option value="">Bulan</option>
          <option value="01">Januari</option>
          <option value="02">Februari</option>
          <option value="03">Maret</option>
          <option value="04">April</option>
          <option value="05">Mei</option>
          <option value="06">Juni</option>
          <option value="07">Juli</option>
          <option value="08">Agustus</option>
          <option value="09">September</option>
          <option value="10">Oktober</option>
          <option value="11">November</option>
          <option value="12">Desember</option>
        </select>
      </div>
      <div class="col-sm-5">
        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Tahun Absen">
      </div>
      <div class="col-sm-2">
        <button class="btn btn-primary"><i class="fas fa-download pr-3"></i>Download</button>
      </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Jenis Absen</th>
                    <th>Status</th>
                      <th>Sisa Cuti</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama Karyawan</th>

                  <th>Jenis Absen</th>
                  <th>Status</th>
                  <th>Sisa Cuti</th>

                  <th>Action</th>
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
			url: "{{ route('karywanAbsen.index') }}",
      data:function(d){
        d.tahun = $('#tahun').val(),
        d.bulan = $('#bulan').val(),
        d.search = $('input[type="search"]').val()
      }
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'karyawan_id', name: 'karyawan_id'},
				{data: 'jenis_absen', name: 'jenis_absen'},
				{data: 'status', name: 'status'},
                {data: 'cuti.jatah_days', name: 'cuti.jatah_days', defaultContent: '0'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		  ],
		});
    $(function(){
      $("#tahun").keyup(function(){
          table.draw();
      });
      $("#bulan").change(function(){
          table.draw();
      });
    });
</script>

@endsection
