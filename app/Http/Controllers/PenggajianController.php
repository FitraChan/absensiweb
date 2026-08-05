<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\PeriodeGaji;
use App\Models\TransGaji;
use App\Models\Lembur;
use App\Models\AturanKeterlambatan;


use App\Models\Gaji;
use Illuminate\Support\Facades\DB;

use App\Models\ItemGaji;
use App\Models\TransAbsen;
use App\Models\Message;


use App\Models\Cuti;

use App\Models\KategoriItem;
use App\Models\KaryawanAbsen;

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
    // $kary = Karyawan::first();
    // $per =  Gaji::where('karyawan_id',$kary->id)->orderBy('id','desc')->skip(1)->take(2)->first();

    //  $selectPeriode  = Gaji::where('periode_gaji_id', $per->periode_gaji_id)->first();

    //$selectPeriode = Gaji::orderBy('id','desc')->first();

    //$periodeGaji=PeriodeGaji::where('id',$selectPeriode->periode_gaji_id)->first();

    $periodeGaji = PeriodeGaji::orderBy('id', 'desc')->first();


    $konfig = DB::table('konfigs')->first();


    // $uang_makan = $konfig->uang_makan;

    //==================================================================

    $karyawanAll = Karyawan::get();

    $lastGaji = Gaji::orderBy('id', 'desc')->first();

    //$periodeLastGaji = PeriodeGaji::where('id',$lastGaji->periode_gaji_id)->first();

    $periodeLastGaji = PeriodeGaji::orderBy('id', 'desc')->first();


    // foreach ($karyawanAll as $key) {
    // code...
    $karyawanAll->chunk(100)->each(function ($chunk) use ($date, $periodeGaji, $konfig, $karyawanAll, $lastGaji, $periodeLastGaji, $request, $log) {
      foreach ($chunk as $key) {
        $this->insertTransGaji($request, $periodeLastGaji->mulai, $periodeLastGaji->selesai, $periodeGaji->id, $key->id, $key->jabatan_id, $log);
        //departemen_item_gaji
        $makan = DB::table('departemen_item_gaji')->where('departemen_id', $key->departement_id)->where('item_gaji_id', 1)->first();
        $lembur = DB::table('departemen_item_gaji')->where('departemen_id', $key->departement_id)->where('item_gaji_id', 2)->first();


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

              $absensi1 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'I')->get());
              $izin = 1 / 30 * $gapok;
              TransGaji::where('item_gaji_id', 6)->where('gaji_id', $cek->id)->update(['nominal' => $izin, 'qty' => $absensi1]);

              $dataIjin = [
                'gaji_id' => $cek->id,
                'nominal' => $izin,
                'qty' => $absensi1,
              ];

              $logs = [
                'tanggal' => now(),
                'tabel' => 'TransGaji',
                'aksi' => 'Update',
                'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                'ip' => $request->ip(),
                'keterangan' => json_encode(['data' => $dataIjin]),
                'serial' => url('fetchGaji'),
              ];


              $log->create($logs);
            } // end of if


            // alpha
            if (TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->first() != "") {

              $absensi2 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'A')->get());
              $alfa = 1 / 25 * $gapok;
              TransGaji::where('item_gaji_id', 7)->where('gaji_id', $cek->id)->update(['nominal' => $alfa, 'qty' => $absensi2]);

              $dataAlpha = [
                'gaji_id' => $cek->id,
                'nominal' => $alfa,
                'qty' => $absensi2,
              ];

              $logs2 = [
                'tanggal' => now(),
                'tabel' => 'TransGaji',
                'aksi' => 'Update',
                'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                'ip' => $request->ip(),
                'keterangan' => json_encode(['data' => $dataAlpha]),
                'serial' => url('fetchGaji'),
              ];


              $log->create($logs2);
            }




            // uang makan
            if (TransGaji::where('item_gaji_id', 2)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 2)->where('gaji_id', $cek->id)->first() != "") {

              $absensi3 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'H')->get());
              $konfig = DB::table('konfigs')->first();
              TransGaji::where('item_gaji_id', 2)->where('gaji_id', $cek->id)->update(['nominal' => $konfig->uang_makan, 'qty' => $absensi3]);

              $dataMakan = [
                'gaji_id' => $cek->id,
                'nominal' => $konfig->uang_makan,
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


            if (!empty($lupa_absen->id)) {

              $absensi4 = count(Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])->where('karyawan_id', $cek->karyawan_id)->where('status_absensi', 'T')->get());

              $konfig = DB::table('konfigs')->first();

              $nominal =  $uang_makan; // ga dapat uang makan

              TransGaji::where('item_gaji_id', 5)->where('gaji_id', $cek->id)->update(['nominal' => $nominal, 'qty' => $absensi4]);

              $dataLupa = [
                'gaji_id' => $cek->id,
                'nominal' => $nominal,
                'qty' => $absensi4,
              ];

              $logs4 = [
                'tanggal' => now(),
                'tabel' => 'TransGaji',
                'aksi' => 'Update',
                'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                'ip' => $request->ip(),
                'keterangan' => json_encode(['data' => $dataLupa]),
                'serial' =>  url('fetchGaji'),
              ];


              $log->create($logs4);
            }

            //bpjs

            if (TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)->first() != null || TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)->first() != "") {
              TransGaji::where('item_gaji_id', 10)->where('gaji_id', $cek->id)->update(['nominal' => 40000, 'qty' => 1]);
            }

            $transTelat = TransGaji::where('item_gaji_id', 4)
              ->where('gaji_id', $cek->id)
              ->first();
            // Telat ot- in / ot -out
            if ($transTelat) {

              $absensiT = Absensi::whereBetween('tanggal', [$periode->mulai, $periode->selesai])
                ->where('karyawan_id', $cek->karyawan_id)->get();


              foreach ($absensiT as $itemAbsensi) {

                $totalPotongan = 0;
                $jumlahKejadian = 0;

                /*
                |--------------------------------------------------------------------------
                | Hitung keterlambatan masuk
                |--------------------------------------------------------------------------
                */
                $hasilMasuk = $this->hitungPotonganKeterlambatan(
                  ot: $itemAbsensi->ot_in,
                  konfigId: $konfig->id,
                  uangMakan: $uang_makan
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
                  uangMakan: $uang_makan
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
        ->select('b.id')
        ->get();

      foreach ($item as $a) {
        // code...
        $nominal_gaji;
        $qty_trans;
        if ($a->id == 1) {

          $nom = DB::table('tb_jabatan')->where('id', $jabatan_id)->first();

          $nominal_gaji = !empty($nom->gaji_pokok) ? $nom->gaji_pokok : 0;
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

      $data = [
        'mulai' => strftime('%d %B %Y', strtotime($z->periodeGaji->mulai)),
        'selesai' => strftime('%d %B %Y', strtotime($z->periodeGaji->selesai)),
        'nama' => $z->karyawan->nama_lengkap,
        'absensi' => $z->absensi,
        'gapok' => DB::table('departemen_item_gaji')->where('departemen_id', $z->karyawan->departement_id)->where('item_gaji_id', 1)->value('nominal'),
        'konfig' => DB::table('konfigs')->first(),
      ];

      $pdf = Pdf::loadView('admin.cetakPdfAbsensiMultiple', $data);
      $filename = "employee_{$z->karyawan->nama_lengkap}.pdf";
      $pdf->save($storagePath . $filename);
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
    $per =  Gaji::where('karyawan_id', $kary->id)->orderBy('id', 'desc')->first();
    $gaji = Gaji::with(['karyawan'])->where('periode_gaji_id', $per->periode_gaji_id)->get();
    $data['periode'] = PeriodeGaji::where('id', $per->periode_gaji_id)->first();
    // code...



    $pdfFiles = [];
    $storagePath = storage_path('app/public/pengguna/');

    if (!file_exists($storagePath)) {
      mkdir($storagePath, 0777, true);
    }

    // Generate multiple PDF files
    foreach ($gaji as $z) {
      $deptId = $z->karyawan->departement->id;

      $data = [
        'try' =>  DB::table('kategori_items as a')
          ->leftJoin('item_gajies as b', 'b.kategori_item_id', '=', 'a.id')
          ->rightJoin('departemen_item_gaji as c', 'c.item_gaji_id', '=', 'b.id')
          ->where('c.departemen_id', $deptId)
          ->orderBy('a.id')
          ->selectRaw('a.id')
          ->get(),
        'kategori' => KategoriItem::get(),
        'transGaji' => $z->transGaji,
        'nama_lengkap' => $z->karyawan->nama_lengkap,
        'nama_jabatan' => $z->karyawan?->jabatan?->nama_jabatan,
        'bulan' => date('m', strtotime($z->periodeGaji->mulai)),
        'tahun' => date('Y', strtotime($z->periodeGaji->mulai)),
        'periode' => PeriodeGaji::where('id', $per->periode_gaji_id)->first(),

      ];

      $pdf = Pdf::loadView('admin.cetakGajiMultiple', $data);
      $filename = "employee_{$z->karyawan->nama_lengkap}.pdf";
      $pdf->save($storagePath . $filename);
      $pdfFiles[] = $storagePath . $filename;
    }

    //  return view('admin.cetakGajiMultiple', $data);

    // Buat ZIP
    $zipFileName = storage_path('app/public/multiple_gaji.zip');
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

  private function hitungPotonganKeterlambatan(
    $ot,
    int $konfigId,
    float $uangMakan
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

    $aturan = AturanKeterlambatan::where(
      'konfig_id',
      $konfigId
    )
      ->where('is_active', true)
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

    $persenPotongan = (float) $aturan->persen_potongan;

    $potongan = (
      $persenPotongan / 100
    ) * $uangMakan;

    return [
      'potongan' => round($potongan),
      'qty'      => 1,
    ];
  }
}
