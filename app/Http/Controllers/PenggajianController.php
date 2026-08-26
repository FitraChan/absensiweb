<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\PeriodeGaji;
use App\Models\TransGaji;
use App\Models\Lembur;


use App\Models\Gaji;
use Illuminate\Support\Facades\DB;

use App\Models\ItemGaji;
use App\Models\TransAbsen;
use App\Models\Message;


use App\Models\Cuti;

use App\Models\KategoriItem;
use App\Models\AturanPotongan;

use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use App\Models\Karyawan;
use App\Models\Absensi;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use PDF;


class PenggajianController extends Controller
{
  //



  // code...
  public function index(Karyawan $karyawan, Request $request)
  {
    $data = $karyawan->get();
    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($data) {
          return '
              <button type="button" class="btn btn-warning" onclick="gaji(' . $data->id . ')" ><i class="fas fa-pen"></i></button>

              ';
        })
        ->editColumn('departement_id', function ($row) {
          return $row->departement?->nama_departement;
        })
        ->editColumn('jabatan_id', function ($row) {
          return $row->jabatan?->nama_jabatan;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.dashboard-karyawan-gaji', compact('data'))->with(['cekNav' => 'payroll']);
  }

  public function gaji($id)
  {
    $kary = Karyawan::first();

    $per =  Gaji::where('karyawan_id', $kary->id)->orderBy('id', 'desc')->skip(1)->take(2)->first();

    $data['gaji'] = Gaji::where('periode_gaji_id', $per->periode_gaji_id)->where('karyawan_id', $id)->get();

    $data['periode'] = PeriodeGaji::where('id', $per->periode_gaji_id)->first();
    // code...
    foreach ($data['gaji'] as $x) {
      // code...

      foreach ($x->transGaji as $key) {
        // code...
        $key->itemGaji;

        foreach ($key->itemGaji as $a) {
          // code...

        }
      }
    }

    $data['kategori'] = KategoriItem::get();

    foreach ($data['kategori'] as $hai) {
      // code...
      $hai->itemGaji;

      foreach ($hai->itemGaji as $x) {
        // code...
        $x->transGaji;
      }
    }

    return view('admin.dashboard-gaji', $data)->with(['cekNav' => 'payroll']);
  }


  public function fetchGaji(Log $log, Request $request)
  {
    // code...
    $date = date('Y-m-d');

    $periodeGaji = PeriodeGaji::orderBy('id', 'desc')->first();


    $konfig = DB::table('konfigs')->first();


    // $uang_makan = $konfig->uang_makan;

    //==================================================================

    $karyawanAll = Karyawan::get();

    $lastGaji = Gaji::orderBy('id', 'desc')->first();



    $periodeLastGaji = PeriodeGaji::orderBy('id', 'desc')->first();


    // foreach ($karyawanAll as $key) {
    // code...
    $karyawanAll->chunk(100)->each(function ($chunk) use ($date, $periodeGaji, $konfig, $karyawanAll, $lastGaji, $periodeLastGaji, $request, $log) {
      foreach ($chunk as $key) {
        $this->insertTransGaji($request, $periodeLastGaji->mulai, $periodeLastGaji->selesai, $periodeGaji->id, $key->id, $key->jabatan_id, $log);
        //departemen_item_gaji
        $makan = DB::table('departemen_item_gaji')->where('departemen_id', $key->departement_id)->where('item_gaji_id', 2)->first();
        $lembur = DB::table('departemen_item_gaji')->where('departemen_id', $key->departement_id)->where('item_gaji_id', 3)->first();

        $uang_makan = $makan->nominal;

        $uang_lembur = $lembur->nominal ?? 0;

        //$uang_makan = 
        $salary = Gaji::leftJoin('tb_karyawan', 'tb_karyawan.id', '=', 'gajies.karyawan_id')->where('gajies.karyawan_id', $key->id)->where('periode_gaji_id', $periodeLastGaji->id)->orderBy('gajies.id', 'desc')->get();

        foreach ($salary as $a) {

          //  echo"masuk";
          if (Gaji::where('karyawan_id', $a->karyawan_id)->where('periode_gaji_id', $periodeGaji->id)->first() != null || Gaji::where('karyawan_id', $a->karyawan_id)->where('periode_gaji_id', $periodeGaji->id)->first() != "") { // Update Gaji Karyawan


            $gaji = Gaji::where('karyawan_id', $a->karyawan_id)->where('periode_gaji_id', $periodeGaji->id)->first();

            $karyawan = Karyawan::where('id', $gaji->karyawan_id);

            $cek = $gaji;

            $gapok = TransGaji::where('gaji_id', $cek->id)->where('item_gaji_id', 1)->sum('nominal');
            $periode = $periodeGaji->where('id', $cek->periode_gaji_id)->first();
            // ijin / sakit
            if (TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->first() != "") {
              $hasilIzin = $this->hitungPotonganBerdasarkanQty(
                jenisPotongan: 'izin',
                konfigId: 1,
                qty: '',
                sumberNilai: [
                  'uang_makan' => $uang_makan,
                  'gaji_pokok' => $gapok,
                ],
                periode: $periode,
                karyawan_id: $cek->karyawan_id,
                keterangan: 'I',
                log: $log
              );

              TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->update(['nominal' => $hasilIzin['potongan'], 'qty' => $hasilIzin['qty']]);
            }
            if (TransGaji::where('item_gaji_id', 13)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->first() != "") {

              $hasilSakit = $this->hitungPotonganBerdasarkanQty(
                jenisPotongan: 'sakit',
                konfigId: 1,
                qty: '',
                sumberNilai: [
                  'uang_makan' => $uang_makan,
                  'gaji_pokok' => $gapok,
                ],
                periode: $periode,
                karyawan_id: $cek->karyawan_id,
                keterangan: 'S',
                log: $log
              );

              TransGaji::where('item_gaji_id', 13)->where('gaji_id', $cek->id)->update(['nominal' => $hasilSakit['potongan'], 'qty' => $hasilSakit['qty']]);
            }






            // if (TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->first() != "") {

            //   $absensi1 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'I')->get());
            //   $izin = 1 / 30 * $gapok;
            //   TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->update(['nominal' => $izin, 'qty' => $absensi1]);

            //   $dataIjin = [
            //     'gaji_id' => $cek->id,
            //     'nominal' => $izin,
            //     'qty' => $absensi1,
            //   ];

            //   $logs = [
            //     'tanggal' => now(),
            //     'tabel' => 'TransGaji',
            //     'aksi' => 'Update',
            //     'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
            //     'ip' => $request->ip(),
            //     'keterangan' => json_encode(['data' => $dataIjin]),
            //     'serial' => url('fetchGaji'),
            //   ];


            //   $log->create($logs);
            // } // end of if
            if (TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->first() != "") {
              $hasilAlpha = $this->hitungPotonganBerdasarkanQty(
                jenisPotongan: 'alpha',
                konfigId: 1,
                qty: '',
                sumberNilai: [
                  'uang_makan' => $uang_makan,
                  'gaji_pokok' => $gapok,
                ],
                periode: $periode,
                karyawan_id: $cek->karyawan_id,
                keterangan: 'A',
                log: $log
              );

              TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->update(['nominal' => $hasilAlpha['potongan'], 'qty' => $hasilAlpha['qty']]);
            }
            // alpha
            // if (TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->first() != "") {

            //   $absensi2 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'A')->get());
            //   $alfa = 1 / 25 * $gapok;
            //   TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->update(['nominal' => $alfa, 'qty' => $absensi2]);

            //   $dataAlpha = [
            //     'gaji_id' => $cek->id,
            //     'nominal' => $alfa,
            //     'qty' => $absensi2,
            //   ];

            //   $logs2 = [
            //     'tanggal' => now(),
            //     'tabel' => 'TransGaji',
            //     'aksi' => 'Update',
            //     'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
            //     'ip' => $request->ip(),
            //     'keterangan' => json_encode(['data' => $dataAlpha]),
            //     'serial' => url('fetchGaji'),
            //   ];


            //   $log->create($logs2);
            // }

            // uang makan
            if (TransGaji::where('item_gaji_id', 2)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 2)->where('gaji_id', $cek->id)->first() != "") {

              $absensi3 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'H')->get());
              //   $konfig = DB::table('konfigs')->first();
              TransGaji::where('item_gaji_id', 2)->where('gaji_id', $cek->id)->update(['nominal' => $uang_makan, 'qty' => $absensi3]);

              $dataMakan = [
                'gaji_id' => $cek->id,
                'nominal' => $uang_makan,
                'qty' => $absensi3,
              ];

              $logs3 = [
                'tanggal' => now(),
                'tabel' => 'TransGaji',
                'aksi' => 'Update',
                'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                'ip' => $request->ip(),
                'keterangan' => json_encode(['data' => $dataMakan]),
                'serial' =>  url('fetchGaji'),
              ];


              $log->create($logs3);
            }

            $lupa_absen =  TransGaji::where('item_gaji_id', 5)->where('gaji_id', $cek->id)->first();
            //lupa absen

            if (!empty($lupa_absen->id)) {

              $hasilLupaAbsen = $this->hitungPotonganBerdasarkanQty(
                jenisPotongan: 'lupa_absen',
                konfigId: 1,
                qty: '',
                sumberNilai: [
                  'uang_makan' => $uang_makan,
                  'gaji_pokok' => $gapok,
                ],
                periode: $periode,
                karyawan_id: $cek->karyawan_id,
                keterangan: 'T',
                log: $log
              );

              TransGaji::where('item_gaji_id', 5)->where('gaji_id', $cek->id)->update(['nominal' => $hasilLupaAbsen['potongan'], 'qty' => $hasilLupaAbsen['qty']]);

              //   $absensi4 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'T')->get());

              //   $konfig = DB::table('konfigs')->first();

              //   $nominal =  $uang_makan; // ga dapat uang makan

              //   TransGaji::where('item_gaji_id', 5)->where('gaji_id', $cek->id)->update(['nominal' => $nominal, 'qty' => $absensi4]);

              //   $dataLupa = [
              //     'gaji_id' => $cek->id,
              //     'nominal' => $nominal,
              //     'qty' => $absensi4,
              //   ];

              //   $logs4 = [
              //     'tanggal' => now(),
              //     'tabel' => 'TransGaji',
              //     'aksi' => 'Update',
              //     'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
              //     'ip' => $request->ip(),
              //     'keterangan' => json_encode(['data' => $dataLupa]),
              //     'serial' =>  url('fetchGaji'),
              //   ];


              //   $log->create($logs4);
            }

            //bpjs

            if (TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)->first() != "") {
              $aturan = AturanPotongan::query()
                ->where('konfig_id', 1)
                ->where('jenis_potongan', 'bpjs')
                ->where('is_active', 1)
                ->orderByDesc('qty_mulai')
                ->first();

              TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)
                ->update(['nominal' => $aturan->nilai_potongan, 'qty' => 1]);

              //   TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)->update(['nominal' => 40000, 'qty' => 1]);
            }

            $transTelat = TransGaji::where('item_gaji_id', 4)
              ->where('gaji_id', $cek->id)
              ->first();
            // Telat ot- in / ot -out
            if ($transTelat) {

              $absensiT = Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])
                ->where('karyawan_id', $cek->karyawan_id)->get();
              $totalPotongan = 0;
              $jumlahKejadian = 0;

              foreach ($absensiT as $itemAbsensi) {



                /*
                |--------------------------------------------------------------------------
                | Hitung keterlambatan masuk
                |--------------------------------------------------------------------------
                */
                $hasilMasuk = $this->hitungPotonganKeterlambatan(
                  ot: $itemAbsensi->ot_in,
                  konfigId: $konfig->id,
                  sumberNilai: [
                    'uang_makan' => $uang_makan,
                    'gaji_pokok' => $gapok,
                  ],
                );

                $totalPotongan += $hasilMasuk['potongan'];
                $jumlahKejadian += $hasilMasuk['qty'];

                /*
                |--------------------------------------------------------------------------
                | Hitung pulang lebih awal
                |--------------------------------------------------------------------------
                */
                $hasilPulang = $this->hitungPotonganKeterlambatan(
                  ot: $itemAbsensi->ot_out,
                  konfigId: $konfig->id,
                  sumberNilai: [
                    'uang_makan' => $uang_makan,
                    'gaji_pokok' => $gapok,
                  ],
                );

                $totalPotongan += $hasilPulang['potongan'];
                $jumlahKejadian += $hasilPulang['qty'];

                // foreach ($absensiT as $key_emp) {

                //   $ot_in = $key_emp->ot_in;

                // // code...
                // if($ot_in >= -15 &&  $ot_in <= -1 && $ot_in != null && $key_emp->status_absensi != 'T'){



                //    $telat =round($uang_makan / 2); //30000
                //    // cek nominal nya ada apa kagak??
                //    $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //    if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //        $totalTelat = $cekTrans->nominal + $telat;                    
                //        TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);


                //    }else{


                //        TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);

                //   }


                // }elseif ($ot_in >= -30 &&  $ot_in <= -15 && $ot_in != null && $key_emp->status_absensi != 'T') {

                //   // code...
                //   $telat =round(0.75*$uang_makan);

                //   $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //   if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null) {

                //     $totalTelat = $cekTrans->nominal + $telat;


                //     TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);

                //     }else{
                //       TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);


                //     }
                // }elseif ( $ot_in >= -59 && $ot_in <= -30 && $ot_in != null && $key_emp->status_absensi != 'T') {



                //   $telat =round($uang_makan);

                //   $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //   if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //     $totalTelat = $cekTrans->nominal + $telat;
                //       TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);



                //     }else{
                //       TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);


                //     }
                // }elseif($ot_in <= -59 &&  $ot_in != null && $key_emp->status_absensi != 'T'){



                //         $telat =round($uang_makan);

                //         $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //         if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //           $totalTelat = $cekTrans->nominal + $telat;


                //             TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);

                //           }else{


                //             TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);


                //           }

                // }

                //    $ot_out = $key_emp->ot_out;

                //    if($ot_out >= -15 &&  $ot_out <= -1 && $ot_out != null && $key_emp->status_absensi != 'T'){

                //       $telat =   round($uang_makan / 2); //30000
                //       // cek nominal nya ada apa kagak??
                //       $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //       if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //         $totalTelat = $cekTrans->nominal + $telat;
                //         TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);

                //         // echo"<pre>";
                //         // print_r("sudah ada tunggakan ".$totalTelat ."  ot_out >= -15 &&  ot_out <= -1 karyawan_id ".$cek->karyawan_id);
                //         // echo"</pre>";
                //         }else{
                //           TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);

                //           // echo"<pre>";
                //           // print_r($telat ."  ot_out >= -15 &&  ot_out <= -1 karyawan_id".$cek->karyawan_id." tanggal ".$key_emp->tanggal);
                //           // echo"</pre>";
                //         }


                //    }elseif ($ot_out >= -30 &&  $ot_out <= -15 && $ot_out != null && $key_emp->status_absensi != 'T') {
                //      // code...
                //      $telat =round(0.75*$uang_makan); // 60000
                //      // cek nominal nya ada apa kagak??
                //      $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //      if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //        $totalTelat = $cekTrans->nominal + $telat;

                //        TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);

                //        // echo"<pre>";
                //        // print_r("sudah ada tunggakan ".$totalTelat ."  ot_out >= -30 &&  ot_out <= -15 karyawan_id".$cek->karyawan_id);
                //        // echo"</pre>";
                //        }else{
                //          TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);

                //          // echo"<pre>";
                //          // print_r($telat ."  ot_out >= -30 &&  ot_out <= -15 karyawan_id ".$cek->karyawan_id." tanggal ".$key_emp->tanggal);
                //          // echo"</pre>";
                //        }
                //    }elseif ($ot_out >= -59 && $ot_out <= -30 && $ot_out != null && $key_emp->status_absensi != 'T') {
                //      // code...
                //      $telat =round($uang_makan); //120000
                //      // cek nominal nya ada apa kagak??
                //      $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //      if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //        $totalTelat = $cekTrans->nominal + $telat;
                //         TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);
                //        //
                //        // echo"<pre>";
                //        // print_r("sudah ada tunggakan ".$totalTelat ." ot_out  <= -30 ".$cek->id." tanggal ".$key_emp->tanggal." karyawan_id ".$cek->karyawan_id);
                //        // echo"</pre>";

                //        }else{
                //          TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);

                //          // echo"<pre>";
                //          // print_r($telat ." ot_out  <= -30 ".$cek->id." tanggal ".$key_emp->tanggal." karyawan_id ".$cek->karyawan_id);
                //          // echo"</pre>";
                //        }
                //    }elseif($ot_out <= -59 &&  $ot_out != null && $key_emp->status_absensi != 'T'){

                //      $telat =round($uang_makan);

                //      $cekTrans =   TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->first();
                //      if($cekTrans->nominal != 0 ||   $cekTrans->nominal != null){

                //        $totalTelat = $cekTrans->nominal + $telat;

                //        // echo"<pre>";
                //        // print_r(" sudah ada tunggakan sebelumnya". $totalTelat ." ot_out  <= -59 ".$cek->id." tanggal ".$key_emp->tanggal." karyawan_id ".$cek->karyawan_id);
                //        // echo"</pre>";
                //          TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $totalTelat, 'qty'=>1]);

                //        }else{
                //          TransGaji::where('item_gaji_id', 4)->where('gaji_id', $cek->id)->update(['nominal'=> $telat, 'qty'=>1]);

                //           // echo"<pre>";
                //           // print_r($total ." ot_out  <= -59 ".$cek->id." tanggal ".$key_emp->tanggal." karyawan_id ".$cek->karyawan_id);
                //           // echo"</pre>";


                //        }
                //    }

                //lembur ==============================

                $cekLembur = Lembur::where('karyawan_id', $key->id)->where('tgl_pengajuan', 'LIKE', $itemAbsensi->tanggal)->first();
                $lembur;
                if ($itemAbsensi->ot_out > 0 && optional($cekLembur)->sts_pengajuan == 1) {
                  $hasil_lembur = floor($itemAbsensi->ot_out / 60);

                  if ($hasil_lembur > 0) {
                    $lembur = $hasil_lembur * $uang_lembur;

                    $cekLembur =   TransGaji::where('item_gaji_id', 3)->where('gaji_id', $cek->id)->first();

                    if ($cekLembur->nominal != 0 ||   $cekLembur->nominal != null) {
                      $totalLembur = $cekLembur->nominal + $lembur;
                      TransGaji::where('item_gaji_id', 3)->where('gaji_id', $cek->id)->update(['nominal' => $totalLembur, 'qty' => 1]);
                    } else {
                      TransGaji::where('item_gaji_id', 3)->where('gaji_id', $cek->id)->update(['nominal' => $lembur, 'qty' => 1]);
                    }
                  } // end  if($hasil_lembur > 0){
                } // end  if($key->ot_out > 0){

              } // end of foreach ($absensiT as $itemAbsensi) {



              $transTelat->update([
                'nominal' => round($totalPotongan),
                'qty'     => $jumlahKejadian,
              ]);
            } // end of  if ($transTelat) {
          } // end of if (Gaji::where('karyawan_id', $a->karyawan_id)->where('periode_gaji_id', $periodeGaji->id)->first() != null || Gaji::where('karyawan_id', $a->karyawan_id)->where('periode_gaji_id', $periodeGaji->id)->first() != "") { // Update Gaji Karyawan

        } // end of for   foreach($salary as $a)

        $dataM = [
          'user_id' => $key->id,
          'receiver_id' => auth()->guard('karyawan')->user()->id,
          'message' => 'Silahkan Cek Report Gaji PeriodeGaji ' . date('d-m-Y', strtotime($periodeLastGaji->mulai)) . ' - ' . date('d-m-Y', strtotime($periodeLastGaji->selesai)),
          'is_read' => 1,
          'is_read_user' => 0,
          'jenis_id' => 0,

        ];
        Message::create($dataM);
      }
    });  // end of for  $karyawanAll->chunk(60)->each(function ($chunk) use ($startDate, $getTransAbsensi) {


    //   $pesan_akhir = Message::where('user_id',$request->karyawan_id)->orderBy('id','desc')->first();
    // $last = $pesan_akhir->message.' Status '.$status;


    $this->cuti();
    return back()->with('success', 'Gaji Behasil diFetch');
  }

  private function hitungPotonganBerdasarkanQty(
    $jenisPotongan,
    $konfigId,
    $qty,
    array $sumberNilai,
    $periode,
    $karyawan_id,
    $keterangan,
    $log
  ): array {
    // if ($qty <= 0) {
    //     return [
    //         'potongan'      => 0,
    //         'qty'           => 0,
    //         'nilai_dasar'   => 0,
    //         'aturan_id'     => null,
    //         'nama_aturan'   => null,
    //     ];
    // }

    $aturan = AturanPotongan::query()
      ->where('konfig_id', $konfigId)
      ->where('jenis_potongan', $jenisPotongan)
      ->where('is_active', 1)
      ->orderByDesc('qty_mulai')
      ->first();

    $absensi1 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $karyawan_id)->where('status_absensi', $keterangan)->get());

    // $nominal = !empty($uangMakan) ? $uangMakan : $gajiPokok;

    $nominal = (float) (
      $sumberNilai[$aturan->sumber_potongan]
      ?? 0
    );






    if (!$aturan) {
      return [
        'potongan'      => 0,
        'qty'           => 0,
        'nilai_dasar'   => 0,
        'aturan_id'     => null,
        'nama_aturan'   => null,
      ];
    }

    // $persenPotongan = (float) $aturan->nilai_potongan;

    $potongan = 0;

    if ($aturan->tipe_nilai === 'persen') {

      $potongan =
        ((float) $aturan->nilai_potongan / 100)
        * $nominal;
    } else {

      $potongan =
        (float) $aturan->nilai_potongan;
    }

    $logs = [
      'tanggal' => now(),
      'tabel' => 'TransGaji',
      'aksi' => 'Update',
      'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
      'ip' => '',
      'keterangan' => json_encode([
        'potongan' => round($potongan),
        'qty'      => $absensi1,
        'jenis_potongan' => $jenisPotongan,
      ]),
      'serial' => url('fetchGaji'),
    ];


    $log->create($logs);



    return [
      'potongan' => round($potongan),
      'qty'      => $absensi1,

    ];
  }

  private function hitungPotonganKeterlambatan(
    $ot,
    int $konfigId,
    array $sumberNilai
  ): array {
    /*
    |--------------------------------------------------------------------------
    | Null atau nol berarti tidak ada keterlambatan
    |--------------------------------------------------------------------------
    */
    if (is_null($ot) || (float) $ot >= 0) {
      return [
        'potongan' => 0,
        'qty'      => 0,
      ];
    }

    /*
    |--------------------------------------------------------------------------
    | Data ot bernilai negatif, contoh -15.
    | Ubah menjadi positif agar cocok dengan tabel aturan: 15 menit.
    |--------------------------------------------------------------------------
    */
    $menitTerlambat = abs((int) $ot);

    $aturan = AturanPotongan::where(
      'konfig_id',
      $konfigId
    )
      ->where('is_active', 1)
      ->where(
        'menit_mulai',
        '<=',
        $menitTerlambat
      )
      ->where(function ($query) use ($menitTerlambat) {
        $query
          ->whereNull('menit_selesai')
          ->orWhere(
            'menit_selesai',
            '>=',
            $menitTerlambat
          );
      })
      ->orderByDesc('menit_mulai')
      ->first();



    if (!$aturan) {
      return [
        'potongan' => 0,
        'qty'      => 0,
      ];
    }

    $nominal = (float) (
      $sumberNilai[$aturan->sumber_potongan]
      ?? 0
    );

    $potongan = 0;

    if ($aturan->tipe_nilai === 'persen') {

      $potongan =
        ((float) $aturan->nilai_potongan / 100)
        * $nominal;
    } else {

      $potongan =
        (float) $aturan->nilai_potongan;
    }


    // echo "<pre>";
    // print_r(" Persen potongan: $persenPotongan%,  Uang makan: $uangMakan%,  Potongan: $potongan");
    // echo "</pre>";



    return [
      'potongan' => round($potongan),
      'qty'      => 1,
    ];
  }

  public function insertTransGaji($request, $mulai, $selesai, $periode_id, $karyawan_id, $jabatan_id, $log)
  {
    // code...

    $dateMulai = \Carbon\Carbon::createFromFormat('Y-m-d', $mulai);
    $dateAkhir = \Carbon\Carbon::createFromFormat('Y-m-d', $selesai);
    // Tambahkan satu bulan
    $newDateMulai = $dateMulai->addMonth();
    $newDateAkhir = $dateAkhir->addMonth();



    // Dapatkan tanggal baru dalam format 'Y-m-d'
    $newDateStringMulai = $newDateMulai->format('Y-m-d');
    $newDateStringAkhir = $newDateAkhir->format('Y-m-d');


    // $periode_next = DB::table('periode_gaji')->where('mulai', $newDateString)->first();

    $gaji_next = Gaji::where('periode_gaji_id', $periode_id)->where('karyawan_id', $karyawan_id)->first();

    $getTransAbsen;
    $getGaji;

    if (empty($gaji_next->id)) {

      $dataPer = [
        'karyawan_id' => $karyawan_id,
        'periode_gaji_id' =>  $periode_id,
      ];



      $getGaji =  Gaji::create($dataPer);


      $dept = Karyawan::where('id', $karyawan_id)->first();

      $item =  DB::table('departemen_item_gaji as a')
        ->leftJoin('item_gajies as b', 'a.item_gaji_id', '=', 'b.id')
        ->where('a.departemen_id', $dept->departement_id)
        ->orderBy('b.kategori_item_id', 'asc')
        ->select('b.id', 'a.nominal')
        ->get();

      foreach ($item as $a) {
        // code...
        $nominal_gaji;
        $qty_trans;
        if ($a->id == 1) {

          //$nom = DB::table('tb_jabatan')->where('id', $jabatan_id)->first();

          $nominal_gaji = !empty($a->nominal) ? $a->nominal : 0;
          $qty_trans = 1;
        } else {

          $nominal_gaji = 0;
          $qty_trans = 0;
        }

        $dataTrans = [
          'gaji_id' =>   $getGaji->id,
          'karyawan_id' => $karyawan_id,
          'item_gaji_id' => $a->id,
          'qty' => $qty_trans,
          'nominal' => $nominal_gaji,
        ];

        TransGaji::create($dataTrans);

        $logs5 = [
          'tanggal' => now(),
          'tabel' => 'TransGaji',
          'aksi' => 'Update',
          'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode(['data' => $dataTrans]),
          'serial' => url('fetchGaji'),
        ];


        $log->create($logs5);
      } // end of foreach ($item as $a) {

    } // end of   if(empty($gaji_next->id)){
  }






  public function cuti()
  {
    $year = date('Y');

    $last = Cuti::orderBy('id', 'desc')->first();

    // jika belum ada data cuti
    if (!$last) {

      $karyawan = Karyawan::all();

      foreach ($karyawan as $key) {

        Cuti::create([
          'karyawan_id' => $key->id,
          'tahun'       => $year,
          'total_hari'  => 0,
          'jatah_days'  => 12
        ]);
      }

      return response()->json([
        'message' => 'Cuti awal berhasil dibuat'
      ]);
    }

    // reset cuti tahun baru
    if ($year > $last->tahun) {

      $cuti = Cuti::where('tahun', $last->tahun)->get();

      foreach ($cuti as $key) {

        Cuti::create([
          'karyawan_id' => $key->karyawan_id,
          'tahun'       => $year,
          'total_hari'  => 0,
          'jatah_days'  => 12
        ]);
      }
    }

    return response()->json([
      'message' => 'success'
    ]);
  }


  public function downloadMultipleAbsen(Request $request)
  {
    $kary = Karyawan::first();
    // $per =  Gaji::where('karyawan_id',$kary->id)->orderBy('id','desc')->skip(1)->take(2)->first();
    $per =  Gaji::where('karyawan_id', $kary->id)->orderBy('id', 'desc')->first();
    $data['periode'] = PeriodeGaji::where('id', $per->periode_gaji_id)->first();
    $periode = PeriodeGaji::where('id', $per->periode_gaji_id)->first();

    $trans = TransAbsen::where('periode_gaji_id', $per->periode_gaji_id)->get();

    $gaji = Gaji::where('periode_gaji_id', $per->periode_gaji_id)->first();

    $pdfFiles = [];
    $storagePath = storage_path('app/public/pengguna/');

    if (!file_exists($storagePath)) {
      mkdir($storagePath, 0777, true);
    }

    // Generate multiple PDF files
    foreach ($trans as $z) {

      // Ambil gapok karyawan
      $gapok = DB::table('departemen_item_gaji')
        ->where('departemen_id', $z->karyawan->departement_id)
        ->where('item_gaji_id', 1)
        ->value('nominal');


     

      // Ambil aturan potongan karyawan/perusahaan
      $aturanPotongan = DB::table('aturan_potongan')
        ->where('konfig_id', 1)
        ->where('is_active', 1)
        ->get();

      /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

      $totalPotongan = 0;
      $totalOtIn = 0;
      $totalOtOut = 0;

      $totH = 0;
      $totI = 0;
      $totS = 0;
      $totC = 0;
      $totA = 0;
      $totT = 0;

      /*
    |--------------------------------------------------------------------------
    | LOOP ABSENSI
    |--------------------------------------------------------------------------
    */

      foreach ($z->absensi as $absen) {

       $makan = DB::table('departemen_item_gaji')->where('departemen_id', $absen->karyawan->departement_id)->where('item_gaji_id', 2)->first();


      $uangMakan = $makan->nominal;

        $potongan = 0;

        $otIn = (int) ($absen->ot_in ?? 0);
        $otOut = (int) ($absen->ot_out ?? 0);

        /*
        |--------------------------------------------------------------------------
        | TERLAMBAT MASUK
        |--------------------------------------------------------------------------
        */

        if ($otIn < 0 && $absen->status_absensi != 'T') {

          $menitTerlambat = abs($otIn);

          $aturan = $aturanPotongan
            ->where('jenis_potongan', 'terlambat')
            ->filter(function ($item) use ($menitTerlambat) {

              $mulai = (int) ($item->menit_mulai ?? 0);
              $selesai = $item->menit_selesai !== null
                ? (int) $item->menit_selesai
                : PHP_INT_MAX;

              return $menitTerlambat >= $mulai
                && $menitTerlambat <= $selesai;
            })
            ->first();

          if ($aturan) {

            if ($aturan->tipe_nilai == 'nominal') {

              $telatIn = (float) $aturan->nilai_potongan;
            } else {

              $dasar = $aturan->sumber_potongan == 'gaji_pokok'
                ? $gapok
                : $uangMakan;

              $telatIn = ($aturan->nilai_potongan / 100) * $dasar;
            }

            $potongan += $telatIn;
          }

          $totalOtIn += $otIn;
        }


        /*
        |--------------------------------------------------------------------------
        | PULANG LEBIH CEPAT
        |--------------------------------------------------------------------------
        */

        if ($otOut < 0 && $absen->status_absensi != 'T') {

          $menitPulangCepat = abs($otOut);

          $aturan = $aturanPotongan
            ->where('jenis_potongan', 'terlambat')
            ->filter(function ($item) use ($menitPulangCepat) {

              $mulai = (int) ($item->menit_mulai ?? 0);

              $selesai = $item->menit_selesai !== null
                ? (int) $item->menit_selesai
                : PHP_INT_MAX;

              return $menitPulangCepat >= $mulai
                && $menitPulangCepat <= $selesai;
            })
            ->first();

          if ($aturan) {

            if ($aturan->tipe_nilai == 'nominal') {

              $telatOut = (float) $aturan->nilai_potongan;
            } else {

              $dasar = $aturan->sumber_potongan == 'gaji_pokok'
                ? $gapok
                : $uangMakan;

              $telatOut = ($aturan->nilai_potongan / 100) * $dasar;
            }

            $potongan += $telatOut;
          }

          $totalOtOut += $otOut;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS ABSENSI
        |--------------------------------------------------------------------------
        */

        switch ($absen->status_absensi) {

          case 'H':

            $totH++;

            break;


          case 'C':

            $totC++;

            break;


          case 'I':

            $totI++;

            $aturan = $aturanPotongan
              ->where('jenis_potongan', 'izin')
              ->where('qty_mulai', '<=', 1)
              ->first();

            if ($aturan) {

              if ($aturan->tipe_nilai == 'nominal') {

                $potongan += $aturan->nilai_potongan;
              } else {

                $dasar = $aturan->sumber_potongan == 'gaji_pokok'
                  ? $gapok
                  : $uangMakan;

                $potongan +=
                  ($aturan->nilai_potongan / 100) * $dasar;
              }
            }

            break;


          case 'S':

            $totS++;

            $aturan = $aturanPotongan
              ->where('jenis_potongan', 'sakit')
              ->where('qty_mulai', '<=', 1)
              ->first();

            if ($aturan) {

              if ($aturan->tipe_nilai == 'nominal') {

                $potongan += $aturan->nilai_potongan;
              } else {

                $dasar = $aturan->sumber_potongan == 'gaji_pokok'
                  ? $gapok
                  : $uangMakan;

                $potongan +=
                  ($aturan->nilai_potongan / 100) * $dasar;
              }
            }

            break;


          case 'A':

            $totA++;

            $aturan = $aturanPotongan
              ->where('jenis_potongan', 'alpha')
              ->where('qty_mulai', '<=', 1)
              ->first();

            if ($aturan) {

              if ($aturan->tipe_nilai == 'nominal') {

                $potongan += $aturan->nilai_potongan;
              } else {

                $dasar = $aturan->sumber_potongan == 'gaji_pokok'
                  ? $gapok
                  : $uangMakan;

                $potongan +=
                  ($aturan->nilai_potongan / 100) * $dasar;
              }
            }

            break;


          case 'T':

            $totT++;

            $aturan = $aturanPotongan
              ->where('jenis_potongan', 'lupa_absen')
              ->where('qty_mulai', '<=', 1)
              ->first();

            if ($aturan) {

              if ($aturan->tipe_nilai == 'nominal') {

                $potongan += $aturan->nilai_potongan;
              } else {

                $dasar = $aturan->sumber_potongan == 'gaji_pokok'
                  ? $gapok
                  : $uangMakan;

                $potongan +=
                  ($aturan->nilai_potongan / 100) * $dasar;
              }
            }

            break;
        }


        $totalPotongan += $potongan;

        /*
        |--------------------------------------------------------------------------
        | SIMPAN HASIL PER ABSENSI
        |--------------------------------------------------------------------------
        */

        $absen->potongan_hitung = round($potongan);
      }


      /*
    |--------------------------------------------------------------------------
    | DATA UNTUK BLADE
    |--------------------------------------------------------------------------
    */

      $data = [

        'mulai' => strftime(
          '%d %B %Y',
          strtotime($z->periodeGaji->mulai)
        ),

        'selesai' => strftime(
          '%d %B %Y',
          strtotime($z->periodeGaji->selesai)
        ),

        'nama' => $z->karyawan->nama_lengkap,

        'absensi' => $z->absensi,

        'gapok' => $gapok,

        'konfig' => DB::table('konfigs')->first(),

        'total_potongan' => round($totalPotongan),

        'total_ot_in' => $totalOtIn,

        'total_ot_out' => $totalOtOut,

        'tot_h' => $totH,

        'tot_i' => $totI,

        'tot_s' => $totS,

        'tot_c' => $totC,

        'tot_a' => $totA,

        'tot_t' => $totT,
      ];


      /*
    |--------------------------------------------------------------------------
    | GENERATE PDF
    |--------------------------------------------------------------------------
    */

      $pdf = Pdf::loadView(
        'admin.cetakPdfAbsensiMultiple',
        $data
      );


      $filename = "employee_{$z->karyawan->nama_lengkap}.pdf";

      $pdf->save(
        $storagePath . $filename
      );

      $pdfFiles[] = $storagePath . $filename;
    }

    // Buat ZIP
    $zipFileName = storage_path('app/public/multiple_absensi.zip');
    $zip = new ZipArchive;

    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
      foreach ($pdfFiles as $file) {
        $zip->addFile($file, basename($file));
      }
      $zip->close();
    }

    // Hapus file PDF setelah ZIP dibuat
    foreach ($pdfFiles as $file) {
      unlink($file);
    }

    // Download ZIP
    return response()->download($zipFileName)->deleteFileAfterSend(true);
  }



  public function downloadMultipleGaji(Request $request)
  {
    $kary = Karyawan::first();

    if (!$kary) {
      return back()->with('error', 'Data karyawan tidak ditemukan.');
    }

    $per = Gaji::where('karyawan_id', $kary->id)
      ->orderByDesc('id')
      ->first();

    if (!$per) {
      return back()->with('error', 'Data gaji tidak ditemukan.');
    }

    $periode = PeriodeGaji::find($per->periode_gaji_id);

    $gaji = Gaji::with([
      'karyawan.departement',
      'karyawan.jabatan',
      'periodeGaji',
      'transGaji.itemGaji',
    ])
      ->where('periode_gaji_id', $per->periode_gaji_id)
      ->get();

    $pdfFiles = [];

    $storagePath = storage_path('app/public/pengguna/');

    if (!file_exists($storagePath)) {
      mkdir($storagePath, 0777, true);
    }

    foreach ($gaji as $z) {

      if (!$z->karyawan) {
        continue;
      }

      $deptId = $z->karyawan->departement?->id;

      $kategoriDepartemen = collect();

      if ($deptId) {
        $kategoriDepartemen = DB::table('kategori_items as a')
          ->leftJoin(
            'item_gajies as b',
            'b.kategori_item_id',
            '=',
            'a.id'
          )
          ->rightJoin(
            'departemen_item_gaji as c',
            'c.item_gaji_id',
            '=',
            'b.id'
          )
          ->where('c.departemen_id', $deptId)
          ->orderBy('a.id')
          ->select('a.id')
          ->distinct()
          ->get();
      }

      $data = [
        'try'          => $kategoriDepartemen,
        'kategori'     => KategoriItem::all(),
        'transGaji'    => $z->transGaji,
        'nama_lengkap' => $z->karyawan->nama_lengkap,
        'nama_jabatan' => $z->karyawan?->jabatan?->nama_jabatan,
        'bulan'        => date(
          'm',
          strtotime($z->periodeGaji->mulai)
        ),
        'tahun'        => date(
          'Y',
          strtotime($z->periodeGaji->mulai)
        ),
        'periode'      => $periode,
        'gaji'         => $z,
      ];

      // $pdf = Pdf::loadView(
      //     'admin.cetakGajiMultiple',
      //     $data
      // )->setPaper('a4', 'portrait');

      $pdf = Pdf::loadView(
        'admin.cetakGaji',
        $data
      )->setPaper('a4', 'portrait');

      $nama = Str::slug(
        $z->karyawan->nama_lengkap,
        '_'
      );

      $filename = "slip_gaji_{$nama}.pdf";

      $path = $storagePath . $filename;

      $pdf->save($path);

      $pdfFiles[] = $path;
    }

    if (empty($pdfFiles)) {
      return back()->with(
        'error',
        'Tidak ada PDF yang berhasil dibuat.'
      );
    }

    $zipFileName = storage_path(
      'app/public/slip_gaji_' .
        date('Ymd_His') .
        '.zip'
    );

    $zip = new ZipArchive();

    if (
      $zip->open(
        $zipFileName,
        ZipArchive::CREATE |
          ZipArchive::OVERWRITE
      ) !== true
    ) {
      return back()->with(
        'error',
        'Gagal membuat file ZIP.'
      );
    }

    foreach ($pdfFiles as $file) {
      $zip->addFile(
        $file,
        basename($file)
      );
    }

    $zip->close();

    foreach ($pdfFiles as $file) {
      if (file_exists($file)) {
        unlink($file);
      }
    }

    return response()
      ->download(
        $zipFileName,
        'Slip_Gaji_' .
          $periode->nama_periode .
          '.zip'
      )
      ->deleteFileAfterSend(true);
  }
}
