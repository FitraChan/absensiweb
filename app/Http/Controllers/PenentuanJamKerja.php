<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanAbsen;
use App\Models\Log;
use App\Models\PeriodeGaji;
use App\Models\TransGaji;
use App\Models\Gaji;
use App\Models\ItemGaji;
use App\Models\KategoriItem;

use Illuminate\Support\Facades\DB;
use Auth;



class PenentuanJamKerja extends Controller
{
        public function penentuan_jam_kerja($karyawan_id,$waktu_kerja,$date){

          $group_jadwal = DB::table('tb_karyawan')->select('group_jadwal_id')->where('id',$karyawan_id)->first();

          $nowtime = date("Y-m-d H:i:s");
        //  $date = date('Y-m-d H:i:s');

        	 // $date = "2025-03-11 18:01:00";
        // penentuan masuk pagi apa siang========================

            $jam = date('H', strtotime($date));
            $menit = date('i', strtotime($date));

            $m = ltrim($menit, "0");
            $j = ltrim($jam, "0");

            $num = $j.".".$m;
            //$num = "4.1";
        //================================== waktu_mulai

        if($waktu_kerja == 1){

        $set_group_jadwal = DB::table('tb_jam_kerja')->select(DB::raw('HOUR(waktu_mulai) as waktu_mulai'))->whereIn('id', function($query) use ($group_jadwal){
        $id_kar = $group_jadwal->group_jadwal_id;
        $query->select('jam_kerja_id')->from('detail_group_jadwals')->where('group_jadwal_id',$id_kar); })->get();

          // cari jam yang terdekat

            $bungkus=[];
                foreach($set_group_jadwal as $a){

                    $bungkus[] = $a->waktu_mulai;
                }



            $smallest = [];

            foreach ($bungkus as $i) {
                $smallest[$i] = abs($i - $num);

            }

             asort($smallest);
            $hasil_jam = key($smallest);


             $penentuan_jam_kerja =  DB::table('tb_jam_kerja')->where(DB::raw('hour(waktu_mulai)'),$hasil_jam)->first();

             $data['jam_kerja_id'] = $penentuan_jam_kerja->id;


             $waktu_masuk_shift_masuk_to_time = date("H:i:s",strtotime($penentuan_jam_kerja->waktu_mulai));

            $date_to_time = date("H:i:s",strtotime($date));

                    $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_masuk_shift_masuk_to_time );
                    $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);


                    $jam_1 = date('H:i',strtotime($penentuan_jam_kerja->waktu_mulai));
                    $jam_2 = date('H:i',strtotime($date));




                    $difference = $shift_time->diff($time_work);
                    $m = $difference->i;
                    $h = $difference->h;


                    if($jam_2 > $jam_1){



                      $happy_hours = 60 * $h + $m;


                      $data['ot_in'] = '-'.$happy_hours;
                      $data['word'] = 'Terlambat '.$happy_hours.' Menit';


                    }else{

                       $data['word'] = "on Time";
                       $happy_hours = 60 * $h + $m;
                       $data['ot_in'] = $happy_hours;
                    }

        }else{

        // jam Pulang=====================================================

            $set_group_jadwal = DB::table('tb_jam_kerja')->select(DB::raw('HOUR(waktu_akhir) as waktu_akhir'))->whereIn('id', function($query) use ($group_jadwal){
              
            $id_kar = $group_jadwal->group_jadwal_id;
            $query->select('jam_kerja_id')->from('detail_group_jadwals')->where('group_jadwal_id',$id_kar); })->orderBy('waktu_mulai','asc')->get();

          // cari jam yang terdekat

            $bungkus=[];
                foreach($set_group_jadwal as $a){

                    $bungkus[] = $a->waktu_akhir;

                    //if()

                      //
                }





            $smallest = [];

            foreach ($bungkus as $i) {
                $smallest[$i] = abs($i - $num);

            }




             asort($smallest);
            $hasil_jam = key($smallest);

            $index_num = array_search($hasil_jam, $bungkus);





             $penentuan_jam_kerja = DB::table('tb_jam_kerja')->where(DB::raw('hour(waktu_akhir)'),$hasil_jam)->first();


             $penentuan_jam_kerja_waktu_akhir = date('H',strtotime($penentuan_jam_kerja->waktu_akhir));

          //   print_r( $bungkus);

             $index_waktu_jam_pulang = array_search($penentuan_jam_kerja_waktu_akhir, $bungkus);







             $data['jam_kerja_id'] = $penentuan_jam_kerja->id;

             $tanggal_now = date('Y-m-d',strtotime($date));


             $absen_morning = DB::table('tb_absensi')->select('id','jam_masuk','jam_kerja_id','keterangan')->where('tanggal', $tanggal_now)->where('karyawan_id',$karyawan_id)->orderBy('id','desc')->limit(1)->first();


          if(empty($absen_morning->id)){
             $cari_absen_terakhir = DB::table('tb_absensi')->select('jam_masuk','jam_kerja_id','keterangan')->where('karyawan_id',$karyawan_id)->where('jam_kerja_id',3)->orderBy('id','desc')->first();
          }


               $jam_kerja_morning = !empty($absen_morning->id) ? DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first() : DB::table('tb_jam_kerja')->where('id',$cari_absen_terakhir->jam_kerja_id)->first();

                 //$jam_kerja_morning = DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first() ;
               $index_morning = array_search(date('H',strtotime($jam_kerja_morning->waktu_akhir)), $bungkus);
               $jam_absen_masuk =  date('H',strtotime($absen_morning->jam_masuk));
               $jam_pulang_ditambah_satu = date('H', strtotime($date . ' + 1 hours'));


        //		print_r($jam);







        // bila index waktu jam pulang lebih kecil dari waktu akhir ketika dia absen masuk maka dan jika dia baru absen masuk trus ga lama
        // kemudian absen lagi maka...  dan baru absen masuk satu jam  kemudian absen pulang maka...
        if($index_waktu_jam_pulang  < $index_morning ||  $jam_absen_masuk == $jam ||  $jam_absen_masuk ==   $jam_pulang_ditambah_satu){


            $penentuan_jam_kerja_waktu_akhir_id_sama = date('H:i',strtotime($penentuan_jam_kerja->waktu_akhir));

          $home_time = date('H:i',strtotime($num));

        //	if($home_time < $penentuan_jam_kerja_waktu_akhir_id_sama)	{
                $data['penentuan_jam_kerja'] = $penentuan_jam_kerja_waktu_akhir_id_sama;
                $data['jam_kerja_id'] = $absen_morning->jam_kerja_id;


                 $waktu_akhir_shift_masuk =  DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first();

            $waktu_akhir_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_akhir));

            $date_to_time = date("H:i:s",strtotime($date));

            $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_akhir_shift_masuk_to_time);
            $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);
          //	$time_work = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "2022-05-05 12:01:00");



            $difference = $shift_time->diff($time_work);
            $m = $difference->i;
            $h = $difference->h;


            $happy_hours = 60 * $h + $m;



                  $data['word'] =  'Masuk '.$absen_morning->keterangan.', Pulangya - '.$happy_hours.' Menit';

                  //$data['ot_out'] = '-'.$h.'.'.$m;





                  $data['ot_out'] = '-'.$happy_hours;





              return $data;



        }




        // penentuan jam pulang id Apabila sama dengan id saat absen masuk maka

        if($penentuan_jam_kerja->id == $absen_morning->jam_kerja_id){


          $x = $index_num + 1;


        //print_r("expression");
        // jam pulang akan dinaikkan index array nya

              if(!empty($bungkus[$x])){

              //	print_r("masuk");

                $penentuan_kerja_lembur = DB::table('tb_jam_kerja')->where(DB::raw('hour(waktu_akhir)'),$bungkus[$x])->first();

                $penentuan_jam_kerja_waktu_akhir  = date('H',strtotime($penentuan_kerja_lembur->waktu_akhir));




                $index_waktu_akhir = array_search($penentuan_jam_kerja_waktu_akhir, $bungkus);




              }else{
          // jika indexnya over maka kembali ke 0

                //print_r("masuk");
                $penentuan_kerja_lembur = DB::table('tb_jam_kerja')->where(DB::raw('hour(waktu_akhir)'),$bungkus[0])->first();

                $penentuan_jam_kerja_waktu_akhir  = date('H',strtotime($penentuan_kerja_lembur->waktu_akhir));

                $index_waktu_akhir = array_search($penentuan_jam_kerja_waktu_akhir, $bungkus);

              }


        // jika waktunya kurang dari jam akhir
          $penentuan_jam_kerja_waktu_akhir_id_sama = date('H:i',strtotime($penentuan_jam_kerja->waktu_akhir));

          $home_time = date('H:i',strtotime($num));

        //	print_r($home_time);

          if($home_time < $penentuan_jam_kerja_waktu_akhir_id_sama)	{


            $waktu_akhir_shift_masuk = DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first();

            $waktu_akhir_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_akhir));

            $date_to_time = date("H:i:s",strtotime($date));

            $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_akhir_shift_masuk_to_time);
            $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);
          //	$time_work = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "2022-05-05 12:01:00");





            $difference = $shift_time->diff($time_work);
            $m = $difference->i;
            $h = $difference->h;

            $data['penentuan_jam_kerja'] = $penentuan_jam_kerja_waktu_akhir_id_sama;
                $data['jam_kerja_id'] = $absen_morning->jam_kerja_id;


                $happy_hours = 60 * $h + $m;
                  $data['word'] =  'Masuk '.$absen_morning->keterangan.',Pulangya - '.$happy_hours.' Menit';






                  $data['ot_out'] = '-'.$happy_hours;









              return $data;


          }


        }

        // index [13,18,5]
        // index waktu jam pulang lebih besar dari index jam akhir ketika dia absen masuk
        $index_waktu_akhir = 0;

      //  print_r($index_waktu_jam_pulang);

        if($index_waktu_jam_pulang > $index_morning){
        //print_r("masuk");

          $penentuan_jam_kerja_waktu_akhir = date('H',strtotime($penentuan_jam_kerja->waktu_akhir));


//

          $index_waktu_akhir = array_search($penentuan_jam_kerja_waktu_akhir, $bungkus);




        }



        // bila index saat absen masuk lebih kecil dari index jam pulang dannn nilai jam masuk lebih besar dari jam pulang shift yg sudah di tentukan
        // contoh bila dia masuk pagi jam 8.30 ternyata dia pulang nya jam 18.01 maka dia akan di rubah jam_kerja_id nya atau shiftnya dari pagi menjadi siang
        if(($index_morning  < $index_waktu_jam_pulang)  && ($num > $penentuan_jam_kerja_waktu_akhir)){




                    $waktu_akhir_shift_masuk = DB::table('tb_jam_kerja')->where('id',$penentuan_jam_kerja->id)->first();

                  $waktu_akhir_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_akhir));

                  $waktu_mulai_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_mulai));

                //  print_r($waktu_mulai_shift_masuk_to_time);

                  $date_to_time = date("H:i:s",strtotime($date));

                  $date_to_time_mulai = date("H:i:s",strtotime($absen_morning->jam_masuk));

                  $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_akhir_shift_masuk_to_time);

                  $shift_time_mulai = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_mulai_shift_masuk_to_time);

                  $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);

                  $time_work_mulai = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time_mulai);
                  //$time_work = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "2022-05-05 16:01:00");

                  $jam_1_mulai = date('H:i',strtotime($waktu_akhir_shift_masuk->waktu_mulai));
                  $jam_1 = date('H:i',strtotime($waktu_akhir_shift_masuk->waktu_akhir));
                  $jam_2 = date('H:i',strtotime($date));
                  //$jam_2 = date('H:i',strtotime("16:01:00"));



                  $difference = $shift_time->diff($time_work);
                  $m = $difference->i;
                  $h = $difference->h;



                  $difference_mulai = $shift_time_mulai->diff($time_work_mulai);
                  $m_mulai = $difference_mulai->i;
                  $h_mulai = $difference_mulai->h;






                    $happy_hours_out = 60 * $h + $m;
                    $happy_hours_in = 60 * $h_mulai + $m_mulai;

                  //  print_r("60".' * '.$h_mulai." + ".$m_mulai);

                  //  print_r($shift_time_mulai);


                    $data['ot_out'] = $happy_hours_out;
                    $data['ot_in'] = $happy_hours_in;





                return $data;


        }

        //	print_r($index_waktu_akhir);

        // index [13,18,5]
        // bila index waktu masuk pulang sama dengan index waktu akhir pada tb_jam_kerja di cek
        // contoh index num = 13  dan index waktu akhir = 13
        // cek jam pulangnya dia sama dengan shift kerluarnya di tb_jam_kerja
        //  contoh shift pagi jam 8 - 13, jika dia pulang lebih dari jam 13 maka masuk ke fungsi ini
        //  akan di hitung overtimenya
        if($index_num  == $index_waktu_akhir){

           //print_r("masuk");

           // echo"<pre>";
           // print_r($index_waktu_akhir);
           // echo"</pre>";

            $waktu_akhir_shift_masuk = DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first();

            $waktu_akhir_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_akhir));

            $date_to_time = date("H:i:s",strtotime($date));

            $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_akhir_shift_masuk_to_time);
            $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);
            //$time_work = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "2022-05-05 16:01:00");

            $jam_1 = date('H:i',strtotime($waktu_akhir_shift_masuk->waktu_akhir));
            $jam_2 = date('H:i',strtotime($date));
            //$jam_2 = date('H:i',strtotime("16:01:00"));



            $difference = $shift_time->diff($time_work);
            $m = $difference->i;
            $h = $difference->h;




            $data['jam_kerja_id'] = $absen_morning->jam_kerja_id;


            $happy_hours = 60 * $h + $m;
                  $data['word'] =  'Masuk '.$absen_morning->keterangan.', Pulang Overtime '.$happy_hours.' Menit';




                    $data['ot_out'] = $happy_hours;




        }



        // bila index waktu pulang lebih kecil dari index waktu akhir pada tb_jam_kerja,
        // misal dia shift pagi waktu akhir nya jam 13 sedangkan waktu pulangnta jam 13.01 maka akan dihitung overtime
        // contoh pada prosesnya saat jam pulang sama dengan waktu akhir shift pagi maka index_waktu_akhir yg semula 0, dinaikkan jadi 1
        // supaya bisa masuk proses over time

        if($index_num  < $index_waktu_akhir){
        //	print_r($index_num);
          //print_r("masuk");

            $waktu_akhir_shift_masuk = DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first();

            $waktu_akhir_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_akhir));

            $date_to_time = date("H:i:s",strtotime($date));

            $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_akhir_shift_masuk_to_time);
            $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);
            //$time_work = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "2022-05-05 17:01:00");

            $jam_1 = date('H:i',strtotime($waktu_akhir_shift_masuk->waktu_akhir));
            $jam_2 = date('H:i',strtotime($date));
            //$jam_2 = date('H:i',strtotime("17:01:00"));



            $difference = $shift_time->diff($time_work);
            $m = $difference->i;
            $h = $difference->h;




            $data['jam_kerja_id'] = $absen_morning->jam_kerja_id;

              $happy_hours = 60 * $h + $m;

                  $data['word'] =  'Masuk '.$absen_morning->keterangan.', Pulang Overtime '.$happy_hours.' Menit';



                  //if(!empty($h)){



                      $data['ot_out'] = $happy_hours;




            //$data['word'] = "Lembur";
        }

        // index [13,18,5]
        // ini terjadi ketika shift malam pulangny jam 5, kasusnya ketika di pulang lebih dari jam 5
        // misal di pulang jam 5.1 (index_num) = 2  dan  jam 5 waktu akhir index ke 2 ($index_waktu_akhir)
        // pada prosesnya dinaikkan indexnya, karena sudah mentok di index 5 maka prosesnya dikembalikan ke index 0
        // jadi index_num lebih dari index_waktu_akhir

        if($index_num  > $index_waktu_akhir){
          //print_r($index_waktu_akhir);

          if(empty($absen_morning->id)){
             $cari_absen_terakhir = DB::table('tb_absensi')->select('jam_masuk','jam_kerja_id','keterangan')->where('karyawan_id',$karyawan_id)->where('jam_kerja_id',3)->orderBy('id','desc')->first();
          }


                  //	 $jam_kerja_morning = !empty($absen_morning->id) ? DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first() : DB::table('tb_jam_kerja')->where('id',$cari_absen_terakhir->jam_kerja_id)->first();


            $waktu_akhir_shift_masuk = !empty($absen_morning->id) ? DB::table('tb_jam_kerja')->where('id',$absen_morning->jam_kerja_id)->first() : DB::table('tb_jam_kerja')->where('id',$cari_absen_terakhir->jam_kerja_id)->first() ;

            $waktu_akhir_shift_masuk_to_time = date("H:i:s",strtotime($waktu_akhir_shift_masuk->waktu_akhir));

            $date_to_time = date("H:i:s",strtotime($date));

            $shift_time = \Carbon\Carbon::createFromFormat('H:i:s', $waktu_akhir_shift_masuk_to_time);
            $time_work = \Carbon\Carbon::createFromFormat('H:i:s', $date_to_time);
            //$time_work = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "2022-05-05 17:01:00");

            $jam_1 = date('H:i',strtotime($waktu_akhir_shift_masuk->waktu_akhir));
            $jam_2 = date('H:i',strtotime($date));

            $difference = $shift_time->diff($time_work);
            $m = $difference->i;
            $h = $difference->h;

                  $data['jam_kerja_id'] = !empty($absen_morning->id) ? $absen_morning->jam_kerja_id : $cari_absen_terakhir->jam_kerja_id;
                  $happy_hours = 60 * $h + $m;
                  $data['word'] =  'Masuk '.$absen_morning->keterangan.', Pulang Overtime '.$happy_hours.' Menit';
                  $data['ot_out'] = $happy_hours;


        }




        }


        return $data;


        }
}
