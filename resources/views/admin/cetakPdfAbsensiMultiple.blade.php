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

        .header,
        .footer {
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

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
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


    <?php //foreach ($trans as $z) { 
    ?>
    <div class="container">
        <?php setlocale(LC_TIME, 'id_ID.UTF-8'); // Mengatur locale ke bahasa Indonesia 
        ?>
        <h2> <b>Laporan Kehadiran Karyawan </b> </h2>
        <h4> Periode <?= strftime('%d %B %Y', strtotime($mulai)); ?> - <?= $selesai; ?> </h4>
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
                    <td colspan="2"> <b> <?= $nama; ?> </b> </td>

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
                $tot_t = [];



                $uang_makan = $konfig->uang_makan;
                foreach ($absensi as $key): ?>

                    <tr>

                        <td>
                            <?= strftime(
                                '%A, %d %B %Y',
                                strtotime($key->tanggal)
                            ); ?>
                        </td>

                        <td>
                            <?= !empty($key->jam_masuk)
                                ? date('d/m H:i:s', strtotime($key->jam_masuk))
                                : '-'; ?>
                        </td>

                        <td>
                            <?= !empty($key->jam_pulang)
                                ? date('d/m H:i:s', strtotime($key->jam_pulang))
                                : '-'; ?>
                        </td>

                        <td>
                            <?= !empty($key->jam_kerja_id)
                                ? date('H:i', strtotime($key->jamKerja->waktu_mulai))
                                . ' - ' .
                                date('H:i', strtotime($key->jamKerja->waktu_akhir))
                                : '-'; ?>
                        </td>

                        <td>
                            <?= $key->ot_in ?? 0; ?>
                        </td>

                        <td>
                            <?= $key->ot_out ?? 0; ?>
                        </td>

                        <td>
                            <?= number_format($key->potongan_hitung ?? 0); ?>
                        </td>

                        <td>
                            <?= $key->status_absensi ?? '-'; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>


                $total_all = array_sum($tot_potongan);
                $total_ot = array_sum($tot_ot_in) + array_sum($tot_ot_out);

                ?>

                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> Telat <?//= $total_ot; ?> </td>
                    <td> <?//= number_format($total_all); ?></td>
                    <td> </td>
                </tr>
                <?php
                $ket = ['Hadir', 'Cuti ', 'Sakit', 'Izin', 'Alfa', 'Lupa Absen (in/out)', 'D'];

                $angka = [array_sum($tot_h), array_sum($tot_c), array_sum($tot_s), array_sum($tot_i), array_sum($tot_a), array_sum($tot_t), ''];
                for ($i = 0; $i < 7; $i++) {
                    // code...
                ?>
                    <tr>
                        <td style='text-align: right;'><?= $ket[$i]; ?> </td>
                        <td><?= $angka[$i]; ?> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                    </tr>
                <?php }

                $tot_uang_makan = $angka[0] * $konfig->uang_makan;
                ?>



                <tr>
                    <td colspan="2"> Uang Makan <?= $angka[0] ?> X <?= number_format($konfig->uang_makan); ?></td>

                    <td> <?= number_format($tot_uang_makan); ?> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>

                <tr>

                    <td colspan="2"> Potongan Uang Makan </td>


                    <td> <?//= number_format($total_all); ?></td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>




            </tbody>
        </table>



    </div>


</body>

</html>