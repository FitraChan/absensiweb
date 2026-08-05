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
            font-size: 9px;
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


<?php foreach ($trans as $z) { ?>
    <div class="container">
      <?php setlocale(LC_TIME, 'id_ID.UTF-8'); // Mengatur locale ke bahasa Indonesia ?>
      <h2>  <b>Laporan Kehadiran Karyawan </b> </h2>
    <h4>   Periode <?= strftime('%d %B %Y',strtotime($z->periodeGaji->mulai)); ?> -  <?= strftime('%d %B %Y',strtotime($z->periodeGaji->selesai)); ?> </h4>
      <br />
        <table>
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Jadwal</th>
                    <th>E-OT</th>
                    <th>OT</th>
                    <th>Potongan</th>
                      <th>Status</th>
                </tr>
            </thead>
            <tbody>

              <tr>
                  <td colspan="2">  <b> <?= $z->karyawan->nama_lengkap; ?> </b> </td>

                  <td> </td>
                  <td> </td>
                  <td> </td>
                  <td> </td>
                  <td> </td>
                  <td> </td>
              </tr>

<?php
$tot_potongan = [];
$tot_ot_in = [];
$tot_ot_out = [];

$tot_h = [];
$tot_i = [];
$tot_s = [];
$tot_c = [];
$tot_a = [];

 foreach ($z->absensi as $key) {


    $jam_masuk = !empty($key->jam_masuk) ? date('d/m H:i:s' ,strtotime($key->jam_masuk)) : 0;
    $jam_pulang = !empty($key->jam_pulang) ? date('d/m H:i:s' ,strtotime($key->jam_pulang)) : 0;
    $jam_kerja_mulai = !empty($key->jam_kerja_id) ? date('H:i', strtotime($key->jamKerja->waktu_mulai))  : 0;

    $jam_kerja_pulang = !empty($key->jam_kerja_id) ? date('H:i', strtotime($key->jamKerja->waktu_akhir))  : 0;

    $ot_in = !empty($key->ot_in) ? $key->ot_in : 0;
    $ot_out = !empty($key->ot_out) ? $key->ot_out : 0;
    $telat_in = 0;
    $telat_out = 0;

    $potongan = 0;

      if($ot_in < 0){
              if($ot_in >= -15 &&  $ot_in <= -1 && $ot_in != null && $key->status_absensi != 'T'){
                 $telat_in =round(0.01*$gapok); //30000
              }elseif ($ot_in >= -30 &&  $ot_in <= -15 && $ot_in != null && $key->status_absensi != 'T') {
                $telat_in =round(0.02*$gapok);
              }elseif ($ot_in <= -30 && $ot_in != null && $key->status_absensi != 'T') {
                $telat_in =round(0.04*$gapok);
              }

      }

    if($ot_out < 0){
             if($ot_out >= -15 &&  $ot_out <= -1 && $ot_out != null && $key->status_absensi != 'T'){
                $telat_out =round(0.01*$gapok); //30000
             }elseif ($ot_out >= -30 &&  $ot_out <= -15 && $ot_out != null && $key->status_absensi != 'T') {
                  $telat_out =round(0.02*$gapok);
             }elseif ($ot_out <= -30 && $ot_out != null && $key->status_absensi != 'T') {
                  $telat_out =round(0.04*$gapok);
             }
        }

      $potongan =    $telat_in + $telat_out;




      if($key->status_absensi == 'A'){
         $potongan =  1/20 * $gapok;

         $tot_a[] = 1;
      }

      if($key->status_absensi == 'T'){

         $potongan=round(1/20*$gapok);
      }

      if($key->status_absensi == 'I'){
        $tot_i[] = 1;
            $potongan = 1/25 * $gapok;
      }

      if($key->status_absensi == 'S'){
        $tot_s[] = 1;
            $potongan = 1/25 * $gapok;
      }

      if($key->status_absensi == 'C'){

          $tot_c[] = 1;
      }

      if($key->status_absensi == 'H'){

          $tot_h[] = 1;
      }

      $tot_potongan[] = $potongan;
      if($ot_in < 0){
            $tot_ot_in[] = $ot_in;
      }

      if($ot_out < 0){
            $tot_ot_out[] = $ot_out;
      }

?>
<tr style="height: 6px;">
  <td > <?=  strftime('%A, %d %B %Y',strtotime($key->tanggal)); ?></td>
  <td> <?= $jam_masuk ; ?> </td>
  <td>   <?= $jam_pulang ;?> </td>
  <td>  <?= $jam_kerja_mulai." - ".$jam_kerja_pulang;  ?></td>
  <td> <?=  $ot_in; ?> </td>
  <td> <?=  $ot_out; ?> </td>
  <td> <?=  number_format($potongan) ?> </td>
  <td> <?=  $key->status_absensi; ?> </td>



</tr>

<?php } // end foreach ($absen as $key) {


$total_all = array_sum($tot_potongan);
$total_ot = array_sum($tot_ot_in) + array_sum($tot_ot_out);

?>

<tr>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> Telat <?= $total_ot; ?> </td>
    <td> <?= number_format($total_all); ?></td>
    <td> </td>
</tr>
<?php
$ket = ['H','C','S','I','A','D'];

$angka = [array_sum($tot_h),array_sum($tot_c),array_sum($tot_s), array_sum($tot_i),array_sum($tot_a),'' ];
 for ($i=0; $i < 6 ; $i++) {
  // code...
 ?>
    <tr>
        <td style='text-align: right;'><?= $ket[$i]; ?> </td>
        <td><?= $angka[$i]; ?> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>  </td>
        <td>  </td>
        <td> </td>
    </tr>
<?php }

  $tot_uang_makan = $angka[0] * $konfig->uang_makan;
?>



              <tr>
                  <td colspan="2"> Uang Makan <?=$angka[0] ?> X <?= number_format($konfig->uang_makan); ?></td>

                  <td> <?=  number_format($tot_uang_makan); ?> </td>
                  <td> </td>
                  <td> </td>
                  <td>  </td>
                  <td>  </td>
                  <td> </td>
              </tr>

              <tr>

              <td colspan="2"> Potongan Uang Makan </td>


                  <td> <?= number_format($total_all); ?></td>
                  <td> </td>
                  <td> </td>
                  <td>  </td>
                  <td>  </td>
                  <td> </td>
              </tr>




            </tbody>
        </table>



    </div>

    <?php } //  foreach ($trans as $z) { ?>
</body>
</html>
