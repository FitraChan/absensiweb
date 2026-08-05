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
              <form action="{{route('jadwalKerja.store')}}" method="post" enctype="multipart/form-data">
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

                      <input type="hidden" name="karyawan_id" id="karyawan_id" class="form-control">
                      <input type="hidden" name="tanggal" id="tanggal" class="form-control">


                    </div>
                    <button type="submit" class="btn btn-success mt-2">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <h2>Jadwal Kerja Karyawan</h2>
<div class="form-group col-md-6">
  <!-- <input type="date" id="datePicker" value="{{ now()->startOfWeek()->format('Y-m-d') }}"> -->

  <div class="d-flex justify-content-center mt-3">

</div>
</div>



<nav aria-label="Page navigation">
    <ul class="pagination">
        <!-- Tombol Prev -->
        <li class="page-item">
            <a class="page-link" onclick="prev()" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <!-- Menampilkan Nomor Halaman -->

        <li class="page-item">
            <div class="page-link">  <i class="fas fa-calendar-alt"></i> &nbsp; <span id='ahad' style="color: black;"> <?= date('d-m-Y',strtotime($dates['senin'])) ?> s/d  <?= date('d-m-Y',strtotime($dates['jumat'])) ?>  </span> </div>
        </li>


        <!-- Tombol Next -->
        <li class="page-item">
            <a class="page-link" href="#" onclick="next()"  aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>

    <table id="shiftTable" class="table">
    <thead>
        <tr>
            <th>Nama Karyawan</th>

            <th>Senin <br> <div id="date1">   <?= date('d-m-Y',strtotime($dates['senin'])) ?> </div></th>
            <th>Selasa <br> <div id="date2"> <?= date('d-m-Y',strtotime($dates['selasa'])) ?> </div></th>
            <th>Rabu <br> <div id="date3">  <?= date('d-m-Y',strtotime($dates['rabu']))?> </div></th>
            <th>Kamis <br> <div id="date4"> <?= date('d-m-Y',strtotime($dates['kamis'])) ?> </div></th>
            <th>Jumat <br> <div id="date5">  <?= date('d-m-Y',strtotime($dates['jumat'])) ?> </div></th>
        </tr>
    </thead>
</table>

</div>


<!-- Load jQuery -->
<script>

  let count = 0; // Inisialisasi nilai awal

  let count2 = 0; // Inisialisasi nilai awal
  let senin = 0;
  let selasa = 0;
  let rabu = 0;
  let kamis = 0;
  let jumat = 0;



var table =    $('#shiftTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url : "{{ route('jadwalKerja.index') }}",
            data:function(d){
              var dateStr = "<?=$dates['senin']; ?>";
              //count += 7;
              d.start_date = add(dateStr,count);
            }


        },
        columns: [
            { data: 'nama_lengkap', name: 'nama_lengkap' },

            { data: 'senin', name: 'senin'},
            { data: 'selasa', name: 'selasa'},
            { data: 'rabu', name: 'rabu'},
            { data: 'kamis', name: 'kamis'},
            { data: 'jumat', name: 'jumat'},
        ]
    });

    $(function(){


      // $("#datePicker").change(function(){
      //   var dateStr = $("#datePicker").val();
      //   var parts = dateStr.split("-"); // Memisahkan tahun, bulan, dan hari
      //   var newFormat = parts[2] + "-" + parts[1] + "-" + parts[0]; // Mengatur ulang
      //
      //   $("#date1").text(newFormat);
      //   $("#date2").text(add(dateStr,1));
      //   $("#date3").text(add(dateStr,2));
      //   $("#date4").text(add(dateStr,3));
      //   $("#date5").text(add(dateStr,4));
      //
      //     table.draw();
      // });

    });

    function add(dateStr, numb)
    {
      var result1 = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() + numb));
      var hasil1  = result1.toISOString().substr(0, 10);
      var parts1 = hasil1.split("-"); // Memisahkan tahun, bulan, dan hari
      var newFormat1 = parts1[2] + "-" + parts1[1] + "-" + parts1[0]; // Mengatur ulang
      return newFormat1;

    }

    function next() {
      var dateStr = "<?=$dates['senin']; ?>";
      var parts = dateStr.split("-"); // Memisahkan tahun, bulan, dan hari
      var newFormat = parts[2] + "-" + parts[1] + "-" + parts[0]; // Mengatur ulang


      count += 7;
      count2 = count + 4;

    //  senin = count + 1;
      selasa = count + 1;
      rabu =  count + 2;
      kamis  = count + 3;
      jumat  = count + 4;




              $("#date1").text(add(dateStr,count));
              $("#date2").text(add(dateStr,selasa));
              $("#date3").text(add(dateStr,rabu));
              $("#date4").text(add(dateStr,kamis));
              $("#date5").text(add(dateStr,jumat));

      $("#ahad").text(add(dateStr,count)+" s/d "+add(dateStr,count2));

        table.draw();

      //console.log(newFormat);
    }


    function prev() {
      var dateStr = "<?=$dates['senin']; ?>";


      count -= 7;
      count2 = count + 4;


      selasa = count + 1;
      rabu =  count + 2;
      kamis  = count + 3;
      jumat  = count + 4;

      $("#date1").text(add(dateStr,count));
      $("#date2").text(add(dateStr,selasa));
      $("#date3").text(add(dateStr,rabu));
      $("#date4").text(add(dateStr,kamis));
      $("#date5").text(add(dateStr,jumat));

      $("#ahad").text(add(dateStr,count)+" s/d "+add(dateStr,count2));

        table.draw();

      //console.log(newFormat);
    }




    // Tambah shift (event delegation)


    $(document).on('click', '.add-shift', function() {
       let karyawanId = $(this).data('employee');
       let tanggal = $(this).data('date');

       $('#tanggal').val(tanggal);
       $('#karyawan_id').val(karyawanId);

         $('#editModal').modal('show');


      // alert('Karyawan ID: ' + karyawanId + '\nTanggal: ' + tanggal);
   });

   // Event untuk jam kerja yang sudah ada
   $(document).on('click', '.shift-info', function() {
       let karyawanId = $(this).data('employee');
       let tanggal = $(this).data('date');

       $('#tanggal').val(tanggal);
       $('#karyawan_id').val(karyawanId);

         $('#editModal').modal('show');
    //   alert('Shift Karyawan ID: ' + karyawanId + '\nTanggal: ' + tanggal);
   });


   $('#shiftTable').on('page.dt', function() {

     console.log('hai');
    // var info = table.page.info(); // Ambil info halaman saat ini
    //
    // if (info.page > info.previousPage) {
    //     count += 7; // Jika klik Next, maju 7 hari
    // } else if (info.page < info.previousPage) {
    //     count -= 7; // Jika klik Prev, mundur 7 hari
    // }
});

</script>
@endsection
