<?php

namespace App\Http\Controllers\admin;

use App\Exports\AbsenExport;
use App\Exports\DetailAbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanAbsen;
use App\Models\Log;
use App\Models\PeriodeGaji;
use App\Models\TransGaji;
use App\Models\JadwalKaryawan;

use App\Models\Gaji;
use App\Models\ItemGaji;
use App\Models\KategoriItem;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\PenentuanJamKerja;
use Auth;

use PDF;

class MasterAbensiController extends Controller
{

  protected $penentuanJamKerja;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function __construct(PenentuanJamKerja $penentuanJamKerja)
  {
    $this->penentuanJamKerja = $penentuanJamKerja;
  }

  public function index(Karyawan $karyawan, Request $request, PeriodeGaji $periodeGaji, Jabatan $jabatan)
  {
    $data = $karyawan->get();
    // return $data[0]->absensi;

    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($data) {
          if (!empty($data->absensi)) {
            if ($data->absensi != '[]') {
              return '
								<a class="btn btn-secondary text-light" href="' . route('karywan-absen', $data->id) . '"><i class="fas fa-regular fa-eye"></i></a>
								';
            }
          }
        })
        ->editColumn('departement_id', function ($data) {
          return $data->departement?->nama_departement;
        })
        ->editColumn('jabatan_id', function ($data) {
          return $data->jabatan?->nama_jabatan;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.dashboard-absensi', compact('data', 'periodeGaji', 'jabatan'))->with(['cekNav' => 'absen']);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request, Absensi $absensi)
  {
    $data = $absensi->latest();
    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('karyawan_id', function ($data) {
          return $data->karyawan->nama_lengkap;
        })
        ->filter(function ($instance) use ($request) {
          if ($request->get('karyawan_id') != null) {
            $instance->where('karyawan_id', $request->get('karyawan_id'))->get();
          }

          if ($request->get('tanggal') != null) {
            $instance->whereDate('tanggal', $request->get('tanggal'))->get();
          }


          if (!empty($request->get('search'))) {
            $instance
              ->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'LIKE', ["%{$request->get('search')}%"]);
              })
              ->get();
          }
        })
        ->rawColumns([])
        ->make(true);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */


  public function store(Request $request, Absensi $absensi, Log $log, KaryawanAbsen $karyawanAbsen)
  {
    try {
      $cek = '';
      $tanggal = date('Y-m-d', strtotime($request->waktu));

      $waktu = date('Y-m-d H:i:s', strtotime($request->waktu));

      $tanggal_baru = date('Y-m-d', strtotime('-1 days', strtotime($tanggal)));

      $get_date_before = DB::table('tb_absensi')->where('karyawan_id', $request->karyawan_id)->whereTime('jam_masuk', '>', '16:00:00')->where('tanggal', $tanggal_baru)->first();


      $data = $absensi->where('karyawan_id', $request->karyawan_id)->where('tanggal', date('Y-m-d', strtotime($request->tanggal)))
        ->first();
      $data2 = $karyawanAbsen->where('karyawan_id', $request->karyawan_id)->get();
      // return $data->count();
      $request['jam_kerja_id'] = 0;

      $request['status_absensi'] = 'A';

      if ($request->kt == 'in') {
        $request['jam_masuk'] = date('Y-m-d H:i:s', strtotime($request->waktu));

        for ($i = 0; $i < $data2->count(); $i++) {
          if ($request->tanggal >= $data2[$i]->tanggal_mulai && $request->tanggal <= $data2[$i]->tanggal_selesai) {
            if ($data2[$i]->status == 'DISETUJUI') {
              if ($data2[$i]->jenis_absen == 'I') {
                $request['status_absensi'] = 'I';
              } else if ($data2[$i]->jenis_absen == 'S') {
                $request['status_absensi'] = 'S';
              }
            }
          }
        }



        if ($data != [] || $data != null) {

          
          $work_time = $this->penentuanJamKerja->penentuan_jam_kerja($request->karyawan_id, 1, $waktu);


          $get_id = DB::table('tb_absensi')->where('karyawan_id', $request->karyawan_id)->whereNull('jam_masuk')->where('tanggal', $request->tanggal)->first();

          $dataCreate;


          // lupa absen masuk

              if (empty($get_date_before->id)) {
                if ($get_id->jam_pulang != null) {

                  $dataCreate = [
                    'karyawan_id' => $request->karyawan_id,
                    'jam_masuk' => $waktu,
                    //  'jam_pulang' => "Belum Absen Pulang",
                    // 'tanggal' => date('Y-m-d'),
                    //  'jam_kerja_id' => $work_time['jam_kerja_id'],
                    'ot_in' => 0,
                    'status_absensi' => 'H',
                    'updated_at' => now(),
                  ];
                } else {
                  $dataCreate = [
                    'karyawan_id' => $request->karyawan_id,
                    'jam_masuk' => $waktu,
                    'jam_pulang' => "Belum Absen Pulang",
                    // 'tanggal' => date('Y-m-d'),
                    'jam_kerja_id' => $work_time['jam_kerja_id'],
                    'ot_in' => $work_time['ot_in'],
                    'status_absensi' => 'T',
                    'updated_at' => now(),

                  ];
                }

                $cek = DB::table('tb_absensi')->where('id', $get_id->id)->update($dataCreate);

                //$cek = $data->update($request->all());
                $logs = [
                  'tanggal' => now(),
                  'tabel' => 'tb_absensi',
                  'aksi' => 'Update',
                  'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                  'ip' => $request->ip(),
                  'keterangan' => json_encode($dataCreate),
                  'serial' => route('absensi.store'),
                ];
              }else{ // if (empty($get_date_before->id)) { jika tanggal kemarin jam masuk nya lebih dari 16:00:00 maka masuk fungsi ini 
                    $this->jam_absen_masuk(
                      
                        $request->karyawan_id,
                        $waktu,
                        '',
                        $request->jarak,
                        $data->id,
                        auth()->guard('karyawan')->user()->id,
                        $tanggal
                    );
              }  
        } // if ($data != [] || $data != null) {
      } else { // if ($request->kt == 'in') {  ini fungsi absen pulang

        if (empty($get_date_before->id)) { // ini jika kemarin dia absen masuk lebih jam 16.00  dan data nya kosong maka ini jalankan ini biasnya untuk satpam
// ini berarti dia absen pulang normal 

                $work_time = $this->penentuanJamKerja->penentuan_jam_kerja($request->karyawan_id, 2, $waktu);

                $get_id_keluar = DB::table('tb_absensi')->where('karyawan_id', $request->karyawan_id)->whereNotNull('jam_masuk')->where('tanggal', $request->tanggal)->first();

                $ot_in = !empty($work_time['ot_in']) ? $work_time['ot_in'] : $get_id_keluar->ot_in;


                $dataUpdate = [
                  'karyawan_id' => $request->karyawan_id,
                  'jam_pulang' => $waktu,
                  'jam_kerja_id' => $work_time['jam_kerja_id'],
                  //'keterangan' => $work_time['word'],
                  'ot_out' => $work_time['ot_out'],
                  'ot_in' => $ot_in,
                  'status_absensi' => 'H',
                  'updated_at' => now(),

                ];



                $cek = DB::table('tb_absensi')->where('id', $get_id_keluar->id)->update($dataUpdate);

                $logs = [
                  'tanggal' => now(),
                  'tabel' => 'tb_absensi',
                  'aksi' => 'Update',
                  'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                  'ip' => $request->ip(),
                  'keterangan' => json_encode($dataUpdate),
                  'serial' => route('absensi.store'),
                ];

                 
          //  print_r('$jam_saja '.$jam_saja);
        }else {  // if (empty($get_date_before->id)) { // ini jika kemarin dia absen masuk lebih jam 16.00  dan data nya ada maka ini jalankan ini biasnya untuk satpam

                  $jam_saja = date('H',strtotime($waktu));
                  if ($jam_saja < 8) { // ini di jalankan ketika dia  absen pulang jam 5 an untuk satpam

                        $work_time = $this->penentuanJamKerja->penentuan_jam_kerja($request->karyawan_id, 2, $waktu);
                        $get_id_keluar = DB::table('tb_absensi')->where('karyawan_id', $request->karyawan_id)->whereNotNull('jam_masuk')->where('tanggal', $request->tanggal)->first();
                      // $date = date('Y-m-d H:i:s');

                        $dataSecurity = [
                          'karyawan_id' => $request->karyawan_id,
                          'jam_pulang' => $waktu,
                          'pulang_via' => 'Mobile',
                          'posisi_pulang' => 'kantor',
                          'url_keluar' => '',
                          'jarak_pulang' => 0,
                          'jam_kerja_id' => $work_time['jam_kerja_id'],
                          'keterangan' => '',
                          'ot_out' => $work_time['ot_out'],
                          //'ot_in' => $ot_in,
                          'status_absensi' => 'H',
                          'updated_at' => now()
                        ];

                      $cek = DB::table('tb_absensi')->where('id', $get_date_before->id)->update($dataSecurity);

                      $logs = [
                        'tanggal' => now(),
                        'tabel' => 'tb_absensi',
                        'aksi' => 'Update',
                        'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                        'ip' => $request->ip(),
                        'keterangan' => json_encode($dataSecurity),
                        'serial' => route('absensi.store'),
                      ];

                      $log->create($logs);

                          
                    
                  } else { // ini di jalankan ketika dia  absen pulang jam 5 an untuk satpam dan absen lagi untuk jam masuk di contoh absen jam 8 atau jam 12.30 atau absen masuk jam 9 malam

                         $this->jam_absen_pulang(                      
                              $request->karyawan_id,
                              $waktu,
                              '',
                              $request->jarak,
                              $data->id,
                              auth()->guard('karyawan')->user()->id,
                              $tanggal
                          );

                       
                }

         } // if (empty($get_date_before->id)) {     
    } // end of // if ($request->kt == 'in') {  

     

      if ($cek) {
        if ($data != [] || $data != null) {
          //return back()->with('success', 'Absensi Diubah');
        } else {
         // return back()->with('success', 'Absensi Ditambah');
        }
      } else {
       // return back()->with('error', 'Gagal Menambah Absensi');
      }
    } catch (\Throwable $th) {

    print_r($th);
      //return back()->with('error', $th->getMessage());
    }
  }

  public function jam_absen_masuk($id, $date, $posisi, $jarak, $get_id, $user_id, $tanggal)
  {
    // code...

    
    $work_time = $this->penentuanJamKerja->penentuan_jam_kerja($id, 1,$date);

    $data1 = [
      'karyawan_id' => $id,
      'jam_masuk' => $date,
      'jam_pulang' => "Belum Absen Pulang",
      'tanggal' => $tanggal,
      'keterangan' => '',
      'url_masuk' => '',
      'masuk_via' => 'Mobile',
      'posisi_masuk' => $posisi,
      'jarak_masuk' => $jarak,
      'jam_kerja_id' => $work_time['jam_kerja_id'],
      'ot_in' => $work_time['ot_in'],
      'status_absensi' => 'T',
    ];

    $simpan = DB::table('tb_absensi')->where('id', $get_id)->update($data1);
    echo json_encode($simpan);
    //khusus Pegawai Umum end================================================

    $logs = [
      'tanggal' => now(),
      'tabel' => 'tb_absensi',
      'aksi' => 'create',
      'user' => $user_id,
      // 'ip' => $request->ip(),
      'keterangan' => json_encode(['data' => $data1]),
      'serial' => url('save_konfirmation_pagi'),
    ];
    Log::create($logs);
  }

  public function jam_absen_pulang($karyawan_id, $date, $posisi, $jarak, $get_id, $user_id, $tanggal)
  {
    // code...

    
    $work_time = $this->penentuanJamKerja->penentuan_jam_kerja($karyawan_id, 2,$date);
    $get_id_keluar = DB::table('tb_absensi')->where('karyawan_id', $karyawan_id)->whereNotNull('jam_masuk')->where('tanggal', $tanggal)->first();
    $ot_in = !empty($work_time['ot_in']) ? $work_time['ot_in'] : $get_id_keluar->ot_in;

    $dataUpdate = [
            'karyawan_id' => $karyawan_id,
            'jam_pulang' => $date,
            'jam_kerja_id' => $work_time['jam_kerja_id'],
            //'keterangan' => $work_time['word'],
            'ot_out' => $work_time['ot_out'],
            'ot_in' => $ot_in,
            'status_absensi' => 'H',
            'updated_at' => now(),

          ];



    $simpan = DB::table('tb_absensi')->where('id', $get_id)->update($dataUpdate);

  //  $simpan = DB::table('tb_absensi')->where('id', $get_id)->update($data1);
   // echo json_encode($simpan);
    //khusus Pegawai Umum end================================================

    $logs = [
      'tanggal' => now(),
      'tabel' => 'tb_absensi',
      'aksi' => 'create',
      'user' => $user_id,
      // 'ip' => $request->ip(),
      'keterangan' => json_encode(['data' => $dataUpdate]),
      'serial' => url('save_konfirmation_pagi'),
    ];
    Log::create($logs);
  }



  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Absensi $absensi)
  {
    return view('admin.dashboard-lokasi', compact('absensi'))->with(['cekNav' => 'absen']);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    return $request->all();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }






  public function absensi(Karyawan $karyawan, Request $request, Absensi $absensi, PeriodeGaji $periodeGaji)
  {


    $data = $absensi->where('karyawan_id', $karyawan->id)->orderBy('tanggal', 'asc')->latest();
    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('url_masuk', function ($data) {
          return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#masuk-' . $data->id . '"><i class="fas fa-regular fa-eye"></i>
					</button>';
        })
        ->editColumn('url_keluar', function ($data) {
          return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pulang-' . $data->id . '"><i class="fas fa-regular fa-eye"></i>
					</button>';
        })
        ->editColumn('lokasi-d', function ($data) {
          return '<a href="' . route('absensi.show', $data->id) . '" class="btn btn-primary text-light"><i class="fas fa-globe"></i>
					</a>';
        })
        ->filter(function ($instance) use ($request) {
          if ($request->get('tahun') != null) {
            $instance->whereYear('tanggal', 'LIKE', ["%{$request->get('tahun')}%"])->get();
          }
          if ($request->get('bulan') != null) {
            $instance->whereMonth('tanggal', '=', $request->get('bulan'))->get();
          }

          if (!empty($request->get('search'))) {
            $instance->where('tanggal', 'LIKE', ["%{$request->get('search')}%"])->get();
          }
        })
        ->rawColumns(['url_masuk', 'url_keluar', 'lokasi-d', 'lokasi-p'])
        ->make(true);
    }

    return view('admin.detail-absensi', compact('karyawan', 'periodeGaji'))->with(['cekNav' => 'absen']);
  }

  public function exelAbsen(Request $request)
  {
    return Excel::download(new AbsenExport, 'Absen.xlsx');
  }

  public function exelDetailAbsen(Karyawan $karyawan, Request $request)
  {
    return $karyawan->where('id', $request->karyawan_id)->first();
    return Excel::download((new DetailAbsensiExport)->karyawan($karyawan->id), 'DetailAbsen.xlsx');
  }

  public function cekAbsensi(Absensi $absensi, Request $request)
  {
    $id_karyawan = $request->id_karyawan;
    $tanggal = $request->tanggal;
  }


  public function fetchKaryawan(Request $request)
  {
    $data =  $this->penentuanJamKerja->penentuan_jam_kerja(7, 2, '');
    return response()->json($data);
  }

  public function penentuanLibur(Request $request)
  {
    $data = HariLibur::orderBy('id', 'desc')->get();
    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($data) {
          return '
              <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#ubah-' . $data->id . '"><i class="fas fa-pen"></i></button>
              <a onclick="confirm_delete( \'' . url('destroyLibur', $data->id) . '\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-trash "></i></a>
              ';
        })
        ->editColumn('tipe', function ($data) {

          if ($data->tipe == 1) {
            return "Public";
          } else {
            return "Perusahaan";
          }
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.dashboard-libur', compact('data'))->with(['cekNav2' => 'libur']);
  }

  public function penentuanLiburStore(Request $request, Log $log)
  {

    try {

      $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai)->toDateString(); // hasil: "2025-04-22"
      $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->toDateString(); // hasil: "2025-04-23"

      $rangeTanggal = [$tanggalMulai, $tanggalAkhir];




      HariLibur::create([
        'tanggal' => json_encode($rangeTanggal),
        'nama_libur' => $request->nama_libur,
        'tipe' => $request->tipe,
      ]);

      Absensi::whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir])->update(['status_absensi' => 'L']);

      JadwalKaryawan::whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir])->update(['jam_kerja_id' => 4]);



      $logs5 = [
        'tanggal' => now(),
        'tabel' => 'tb_hari_libur',
        'aksi' => 'create',
        'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
        'ip' => $request->ip(),
        'keterangan' => json_encode(['data' => $request->all()]),
        'serial' => url('penentuanLiburStore'),
      ];


      $log->create($logs5);

      return back()->with('success', 'Data Baru Berhasil Ditambah');
    } catch (\Throwable $th) {
      return back()->with('error', $th->getMessage());
    }
  }

  public function penentuanLiburUpdate(Request $request, $id, Log $log)
  {

    try {

      $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai)->toDateString(); // hasil: "2025-04-22"
      $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->toDateString(); // hasil: "2025-04-23"

      $rangeTanggal = [$tanggalMulai, $tanggalAkhir];


      $libur = HariLibur::where('id', $id)->first();

      $tanggalRange = json_decode($libur->tanggal);

      $tanggalMulai = \Carbon\Carbon::parse($tanggalRange[0])->format('Y-m-d');
      $tanggalAkhir = \Carbon\Carbon::parse($tanggalRange[1])->format('Y-m-d');


      Absensi::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->update(['status_absensi' => 'A']);



      JadwalKaryawan::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->update(['jam_kerja_id' => 4]);


      HariLibur::where('id', $id)->update([
        'tanggal' => json_encode($rangeTanggal),
        'nama_libur' => $request->nama_libur,
        'tipe' => $request->tipe,
      ]);

      Absensi::whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir])->update(['status_absensi' => 'L']);

      $logs5 = [
        'tanggal' => now(),
        'tabel' => 'tb_hari_libur',
        'aksi' => 'update',
        'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
        'ip' => $request->ip(),
        'keterangan' => json_encode(['data' => $request->all()]),
        'serial' => url('penentuanLiburUpdate'),
      ];


      $log->create($logs5);

      return back()->with('success', 'Data Baru Behasil diupdate');
    } catch (\Throwable $th) {
      return back()->with('error', $th->getMessage());
    }
  }


  public function destroyLibur($id, Log $log)
  {
    try {
      $data = HariLibur::where('id', $id)->delete();

      $libur = HariLibur::where('id', $id)->first();

      $tanggalRange = json_decode($libur->tanggal);

      $tanggalMulai = \Carbon\Carbon::parse($tanggalRange[0])->format('Y-m-d');
      $tanggalAkhir = \Carbon\Carbon::parse($tanggalRange[1])->format('Y-m-d');


      Absensi::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->update(['status_absensi' => 'A']);

      if ($data) {
        return response()->json(['success' => 'Success']);
      } else {
        return response()->json(['error' => 'data delete failed.']);
      }

      $logs2 = [
        'tanggal' => now(),
        'tabel' => 'tb_hari_libur',
        'aksi' => 'Delete',
        'user' => auth()->guard('karyawan')->user()->id,
        'ip' => $request->ip(),
        'keterangan' => json_encode(['data' => $data]),
        'serial' => url('destroyLibur'),
      ];

      $log->create($logs2);
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage()]);
    }
  }
}
