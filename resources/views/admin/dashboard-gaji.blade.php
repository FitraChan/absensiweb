@extends('layout.main-admin')
@section('tittle-admin')
  Penggajian
@endsection
@section('content-admin')

<!-- Modal -->





<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Payroll Slip <?= $periode->nama_periode; ?> <?= date('Y'); ?></h1>


  </div>

    <div class="table-responsive">
      <?php
$row = [];
// ini kodingan untuk mencari td yang akan di rowspan
       foreach ($kategori as $x) {

        ?>
              <?php
                $rowspan  = count($x->itemGaji);

               foreach($x->itemGaji as $a)  {
                  $row[] = $rowspan;

                ?>
                    <?php
                  }
                   ?>
      <?php }

foreach ($gaji as $z) {

?>


<p><strong>Name: <?= $z->karyawan->nama_lengkap ?></strong> </p>

<p><strong>Title: <?= $z->karyawan->jabatan->nama_jabatan ?></strong> </p>



  <table class="table table-bordered" >
  <thead>
    <tr>
        <th>Category</th>
        <th>Item Name</th>
        <th>Nominal</th>
        <th>Qtt</th>
        <th>Total</th>
    </tr>
  </thead>
  <tbody>

        <?php
        $no = 0;

          $nama  = [];

                    $total_sum = [];
                    $total_pot = [];
        foreach ($z->transGaji as $key ) {
        // code...

        $namaKat;

        $nama[] = $key->itemGaji->kategoriItems->nama_kategori;

         if($nama[0] == $key->itemGaji->kategoriItems->nama_kategori){


          if(count($nama) > 1){
              $namaKat = '';
              unset($nama[0]);
              // Untuk mere-index array (jika diperlukan)
              $nama = array_values($nama);

          }else{

              $namaKat = $nama[0];

          }

        }else{

            $namaKat = $nama[1];

            unset($nama[0]);

            $nama = array_values($nama);

        }

        // echo"<pre>";
        // print_r($namaKat);
        // echo"</pre>";

               $nom = !empty($key->nominal) ? $key->nominal : 0;
                 $qty = !empty($key->qty) ? $key->qty : 0;

                 $total = $nom * $qty;

// kalau non potongan maka....
          if($key->itemGaji->kategoriItems->id != 3){
                           $total_sum[] = $total;
              }else{

                            $total_pot[] = $total;
              }

                   $tr_td;

                        if($namaKat != ''){
                              $tr_td = " <td rowspan = $row[$no]>$namaKat  </td>";
                        }else{
                              $tr_td = "";
                        }

              ?>
                        <tr>
                            <?= $tr_td; ?>
                            <td><?= $key->itemGaji->nama_item_gaji; ?></td>
                            <td><?=  $nom; ?></td>
                            <td> <?=  $qty; ?></td>
                              <td> <?= number_format($total) ?> </td>
                        </tr>

                  <?php

                      $no++;
          ?>

        <?php } // end of foreach ($z->transGaji as $key ) {

$total_all = array_sum($total_sum) - array_sum($total_pot);
          ?>
        <tr>
          <td colspan="4"><strong>Total</strong></td>
          <td><strong> <?= number_format($total_all) ?></strong></td>
        </tr>
        </tbody>
        </table>

        <?php
        } // end of foreach ($gaji as $z) { ?>
    </div>
</div>



@endsection
