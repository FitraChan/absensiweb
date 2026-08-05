<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature div {
            display: inline-block;
            margin-left: 50px;
        }
    </style>
</head>
<body>



              <?php
 $row = [];
// ini kodingan untuk mencari td yang akan di rowspan
               foreach ($kategori as $x) {
                ?>
                      <?php
                      // echo"<pre>";
                      // print_r($x->itemGaji);
                      // echo"</pre>";
                           $rowspan  = count($x->itemGaji);

                           foreach($x->itemGaji as $a)  {
                              //  $row[$a->id] =  $rowspan;
                            ?>
                            <?php
                           }
                           ?>
              <?php
            }

            $hit = [];
            foreach ($try as $key) {
              $hit[] = $key->id;
            }

            $counts = array_count_values($hit);

            foreach ($hit as $value) {
                $row[]  = $counts[$value];
            }

                          // echo"<pre>";
                          // print_r($row);
                          // echo"</pre>";
 //foreach ($gaji as $z) {

//   $bulan =  date('m',strtotime($z->periodeGaji->mulai ));

   // $tahun =  date('Y',strtotime($z->periodeGaji->mulai ));
  ?>

  <div class="container">
      <div class="header">
          <h1>PT. MITRA BISNIS CIPTA KARYA</h1>
          <p>Jl. Raya Puputan Renon No.86 Denpasar</p>
          <p>Telp. 0361244445, Fax. 264773</p>
          <p><a href="http://www.mbcconsulting.id">www.mbcconsulting.id</a> | E-Mail: info@mbcconsulting.id</p>
      </div>

  <h2>Payroll Slip Bulan <?= $bulan ?>  Tahun  <?=  $tahun  ?> </h2>
  <p><strong>Name: <?= $nama_lengkap ?></strong> </p>

  <p><strong>Title: <?= $nama_jabatan ?></strong> </p>



     <table>
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
 foreach ($transGaji as $key ) {
   // code...

      $namaKat;

      $idItem =  !empty($key->setPayroll->item_gaji_id) ? $key->setPayroll->item_gaji_id : 0 ;


      // echo"<pre>";
      // print_r($idItem);
      // echo"</pre>";

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



                        // echo"<pre>";
                        // print_r($tr_td);
                        // echo"</pre>";

              ?>
                        <tr>
                            <?= $tr_td; ?>

                            <td><?= $key->itemGaji->nama_item_gaji; ?></td>
                            <td><?=  number_format($nom); ?></td>
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
          <td><strong><?= number_format($total_all) ?></strong></td>
      </tr>
    </tbody>
    </table>

    <div class="signature">
        <div>
            <p>Denpasar,</p>
            <p>Dir Finance</p>
            <br><br>
            <p><strong></strong></p>
        </div>
        <div>
            <br><br>
            <p><strong></strong></p>
        </div>
    </div>
    </div>
      <?php

//  } // end of foreach ($gaji as $z) { ?>





</body>
</html>
