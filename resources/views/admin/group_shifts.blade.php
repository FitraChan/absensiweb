@extends('layout.main-admin')
@section('tittle-admin')
  Jadwal Karyawan
@endsection
@section('content-admin')

<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Jadwal</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <form action="{{route('simpanGroupKerja')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editId">

                    <div class="form-group col-md-6">
                      <label for="inputEmail4">Jam Kerja</label>
                      <select name="jam_kerja_id" class="form-control">
                        @foreach ($jamKerja as $dpt)

                        <?php
                          $mulai = date('H:i:s',strtotime($dpt->waktu_mulai));
                          $akhir = date('H:i:s',strtotime($dpt->waktu_akhir));
                         ?>
                          <option value="{{$dpt->id}}"><?= $mulai.' - '.$akhir ?></option>
                        @endforeach
                      </select>

                      <input type="hidden" name="grup_id" id="grup_id" class="form-control">
                      <input type="hidden" name="tanggal" id="tanggal" class="form-control">


                    </div>



                    <div class="form-group col-md-6">
                      <label for="inputEmail4">Tanggal Akhir</label>
                       <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <h2>Jadwal Kerja Karyawan</h2>

    <table id="shiftTable" class="table">
    <thead>
        <tr>
            <th>Nama Group</th>

            <th>Senin</th>
            <th>Selasa</th>
            <th>Rabu</th>
            <th>Kamis</th>
            <th>Jumat</th>
        </tr>
    </thead>
</table>

</div>

<script>

    $('#shiftTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('groupKerja') }}",
        columns: [
            { data: 'nama_grup', name: 'nama_grup' },

            { data: 'senin', name: 'senin', orderable: false, searchable: false },
            { data: 'selasa', name: 'selasa', orderable: false, searchable: false },
            { data: 'rabu', name: 'rabu', orderable: false, searchable: false },
            { data: 'kamis', name: 'kamis', orderable: false, searchable: false },
            { data: 'jumat', name: 'jumat', orderable: false, searchable: false },
        ]
    });

    // Tambah shift (event delegation)


    $(document).on('click', '.add-shift', function() {

       let tanggal = $(this).data('date');
        let grup = $(this).data('grup');
      // console.log(grup);
       $('#tanggal').val(tanggal);
       $('#grup_id').val(grup);

         $('#editModal').modal('show');
      // alert('Karyawan ID: ' + karyawanId + '\nTanggal: ' + tanggal);
   });

   // Event untuk jam kerja yang sudah ada
   $(document).on('click', '.shift-info', function() {
       let grup = $(this).data('grup');
       let tanggal = $(this).data('date');



       $('#tanggal').val(tanggal);
       $('#grup_id').val(grup);

         $('#editModal').modal('show');
    //   alert('Shift Karyawan ID: ' + karyawanId + '\nTanggal: ' + tanggal);
   });

</script>
@endsection
