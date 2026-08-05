<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\JadwalKaryawan;
use App\Models\JamKerja;
use App\Models\Karyawan;
use App\Models\GroupJadwal;
use DatePeriod;
use DateInterval;
use App\Models\Log;
use Auth;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class JadwalKerjaController extends Controller
{
    //

    public function __construct()
    {
      return $this->middleware('isAuth');
    }


  public function index(JadwalKaryawan $jadwalKaryawan,JamKerja $jamKerja,Karyawan $karyawan, Request $request)
  {
    // code...
        $tanggalMulai =  $request->input('start_date');

         $jamKerja = JamKerja::get();
        $employees = $karyawan->with(['JadwalKaryawan'])->get();


          $dates = [];

        $startDate = $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai) : \Carbon\Carbon::now()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        if(!empty($tanggalMulai)){

            $dates = [
                'senin' => \Carbon\Carbon::parse($tanggalMulai)->format('Y-m-d'),
                'selasa' => \Carbon\Carbon::parse($tanggalMulai)->addDays(1)->format('Y-m-d'),
                'rabu' => \Carbon\Carbon::parse($tanggalMulai)->addDays(2)->format('Y-m-d'),
                'kamis' => \Carbon\Carbon::parse($tanggalMulai)->addDays(3)->format('Y-m-d'),
                'jumat' => \Carbon\Carbon::parse($tanggalMulai)->addDays(4)->format('Y-m-d'),
            ];

        }else{

            $dates = [
                'senin' => \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d'),
                'selasa' => \Carbon\Carbon::now()->startOfWeek()->addDays(1)->format('Y-m-d'),
                'rabu' => \Carbon\Carbon::now()->startOfWeek()->addDays(2)->format('Y-m-d'),
                'kamis' => \Carbon\Carbon::now()->startOfWeek()->addDays(3)->format('Y-m-d'),
                'jumat' => \Carbon\Carbon::now()->startOfWeek()->addDays(4)->format('Y-m-d'),
            ];
       }


        // Format data untuk DataTables
        $data = $employees->map(function ($employee) use ($dates,  $tanggalMulai) {
            $row = [
                'nama_lengkap' => $employee->nama_lengkap,
            ];

            // Loop hari kerja (Senin - Jumat)

            foreach ($dates as $key => $date) {

                $shift = $employee->JadwalKaryawan->where('tanggal', $date)->first();

                if ($shift) {
       // Menampilkan jam kerja dalam tag <span> dengan data-employee
                       $row[$key] = '<span class="shift-info" data-employee="'.$employee->id.'" data-date="'.$date.'">'
                                   . date('H:i:s', strtotime($shift->JamKerja->waktu_mulai))
                                   . ' - '
                                   . date('H:i:s', strtotime($shift->JamKerja->waktu_akhir))
                                   . '</span>';
                   } else {
                       // Jika shift tidak ada, tampilkan tombol +
                       $row[$key] = '<button class="btn btn-primary add-shift" data-date="'.$date.'" data-employee="'.$employee->id.'">+</button>';
                   }
            }


            // echo"<pre>";
            // print_r($dates);
            // echo"</pre>";

            return $row;
        });
        if ($request->ajax()) {
          // Ambil data karyawan
            return DataTables::of($data)->rawColumns(['senin', 'selasa', 'rabu', 'kamis', 'jumat'])->make(true);
       }

        return view('admin.employee_shifts', compact('jamKerja','dates'))->with(['cekNav2' => 'shifts']);
  }

  public function pencarianJadwal(Request $request)
  {
    // code...
       $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : \Carbon\Carbon::now()->startOfWeek();
       $endDate = $startDate->copy()->endOfWeek();

       $schedules = Schedule::whereBetween('work_date', [$startDate, $endDate])
           ->orderBy('work_date', 'asc')
           ->get();

       return response()->json($schedules);
  }


  public function groupKerja(JamKerja $jamKerja,Karyawan $karyawan, Request $request)
  {
    // code...

    $groupJadwal = GroupJadwal::with('karyawan.JadwalKaryawan')->get();
    $jamKerja = $jamKerja->get();
    $dates = [
        'senin' => \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d'),
        'selasa' => \Carbon\Carbon::now()->startOfWeek()->addDays(1)->format('Y-m-d'),
        'rabu' => \Carbon\Carbon::now()->startOfWeek()->addDays(2)->format('Y-m-d'),
        'kamis' => \Carbon\Carbon::now()->startOfWeek()->addDays(3)->format('Y-m-d'),
        'jumat' => \Carbon\Carbon::now()->startOfWeek()->addDays(4)->format('Y-m-d'),
    ];

    // Format data untuk DataTables
    $data = $groupJadwal->map(function ($grup) use ($dates) {
        $row = [
            'nama_grup' => $grup->nama_grup,
        ];

        foreach ($dates as $key => $date) {

              $shift = [];
          foreach ($grup->karyawan as $karyawan) {
              foreach ($karyawan->JadwalKaryawan as $jadwal) {
                  //echo $jadwal->tanggal . '<br>';

                  $get =    $jadwal->where('tanggal', $date)->where('karyawan_id', $jadwal->karyawan_id)->first();
                  if(!empty($get->id)){
                    $shift[]=   [
                                  'waktu_mulai' => $get->JamKerja->waktu_mulai,
                                  'waktu_akhir' => $get->JamKerja->waktu_akhir,
                                ];
                                break;
                  }else{
                    $shift = '';
                  }


              }
          }

          $hai = !empty($shift[0]) ? $shift[0] : 0;

          if($hai != 0) {
               $row[$key] = '<span class="shift-info" data-grup="'.$grup->id.'" data-date="'.$date.'">'
                           . date('H:i:s', strtotime($hai['waktu_mulai']))
                           . ' - '
                           . date('H:i:s', strtotime($hai['waktu_akhir']))
                           . '</span>';
           } else {
               $row[$key] = '<button class="btn btn-primary add-shift" data-date="'.$date.'" data-grup="'.$grup->id.'">+</button>';
           }


        }

        return $row;
    });


    if ($request->ajax()) {
      // Ambil data karyawan

          return DataTables::of($data)->rawColumns(['senin', 'selasa', 'rabu', 'kamis', 'jumat'])->make(true);
   }

      return view('admin.group_shifts', compact('data','dates','jamKerja'))->with(['cekNav2' => 'grup']);
  }

  public function simpanGroupKerja(Request $request, JadwalKaryawan $jadwalKaryawan,Log $log)
  {
    // code...
    $tanggal_akhir = $request->tanggal_akhir;

    $grup_id = $request->grup_id;

    $tanggal = $request->tanggal;

    $startDate = \Carbon\Carbon::parse($tanggal);
    $endDate = \Carbon\Carbon::parse($tanggal_akhir)->addDay(); // Tambahkan 1 hari agar tanggal akhir masuk

    $interval = new DateInterval('P1D'); // Interval 1 hari
    $period = new DatePeriod($startDate, $interval, $endDate);


        try {

          $kary = Karyawan::where('group_jadwal_id',$grup_id)->get();

          \Carbon\Carbon::setLocale('id');

          $day = \Carbon\Carbon::parse($tanggal)->translatedFormat('l');

          foreach ($kary as $key) {

            foreach ($period as $date) {

                    $hari = \Carbon\Carbon::parse($date)->translatedFormat('l');

                  if($hari == $day){

                    JadwalKaryawan::create([
                                             'tanggal' => $date->format('Y-m-d'),
                                             'karyawan_id' => $key->id,
                                             'jam_kerja_id' => $request->jam_kerja_id
                                           ]);
                  }
            }

        }
          return back()->with('success','Behasil ditambah');


        } catch (\Throwable $th) {
          return back()->with('error',$th->getMessage());
        }
  }

  public function store(Request $request, JadwalKaryawan $jadwalKaryawan,Log $log)
  {
    // code...

    $look = $jadwalKaryawan->where('tanggal', $request->tanggal)->where('karyawan_id', $request->karyawan_id)->first();
    // echo"<pre>";
    // print_r($request->all());
    // echo"</pre>";
        try {
          $cek;
          if(!empty($look->id)){
            $cek = $jadwalKaryawan->where('id',$look->id)->update(['jam_kerja_id' => $request->jam_kerja_id ]);

            $logs5 =[
              'tanggal'=>now(),
              'tabel'=> 'tb_',
              'aksi'=> 'update',
              'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
              'ip' => $request->ip(),
              'keterangan' => json_encode(['data' => $request->all()]),
              'serial' => route('jadwalKerja.store'),
            ];


            $log->create($logs5);
          }else{
              $cek = $jadwalKaryawan->create($request->all());

              $logs5 =[
                'tanggal'=>now(),
                'tabel'=> 'tb_',
                'aksi'=> 'create',
                'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
                'ip' => $request->ip(),
                'keterangan' => json_encode(['data' => $request->all()]),
                'serial' => route('jadwalKerja.store'),
              ];


              $log->create($logs5);
          } //  }else{


          if ($cek) {
            return back()->with('success','Behasil ditambah');
          } else {
            return back()->with('error','Gagal ditambah');
          }

        } catch (\Throwable $th) {
          return back()->with('error',$th->getMessage());
        }
  }
}
