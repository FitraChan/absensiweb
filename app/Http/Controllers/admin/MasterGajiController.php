<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeGaji;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Log;

use App\Models\Karyawan;
use App\Models\TransAbsen;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\TransGaji;
use App\Models\SetPayroll;

use App\Models\Gaji;
use Illuminate\Support\Facades\DB;

use App\Models\ItemGaji;
use App\Models\KategoriItem;





use Auth;


class MasterGajiController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(PeriodeGaji $periodeGaji, Request $request)
  {
    $data = $periodeGaji->orderBy('id', 'desc')->get();

    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($data) {
          return '
							
							<a onclick="confirm_delete( \'' . route('gaji.destroy', $data->id) . '\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-trash "></i></a>
							';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.dashboard-ped_gaji', compact('data'))->with(['cekNav2' => 'gaji']);;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(
    Request $request,
    PeriodeGaji $periodeGaji,
    Log $log
) {
    $request->validate([
        'mulai' => ['required', 'date'],
        'selesai' => ['required', 'date', 'after_or_equal:mulai'],
    ]);

    try {
        $periodeBentrok = PeriodeGaji::where(function ($query) use ($request) {
            $query
                ->whereBetween('mulai', [
                    $request->mulai,
                    $request->selesai,
                ])
                ->orWhereBetween('selesai', [
                    $request->mulai,
                    $request->selesai,
                ])
                ->orWhere(function ($query) use ($request) {
                    $query
                        ->where('mulai', '<=', $request->mulai)
                        ->where('selesai', '>=', $request->selesai);
                });
        })->first();

        if ($periodeBentrok) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Periode gaji bertabrakan dengan periode '
                    . $periodeBentrok->mulai
                    . ' sampai '
                    . $periodeBentrok->selesai
                    . '.'
                );
        }

        $cek = $periodeGaji->create(
            $request->only([
                'nama_periode',  
                'mulai',
                'selesai',
                // tambahkan kolom lain yang memang boleh disimpan
            ])
        );

        $this->findSundays(
            $cek->mulai,
            $cek->selesai,
            $cek->id
        );

        $logs5 = [
            'tanggal' => now(),
            'tabel' => 'periodeGaji',
            'aksi' => 'create',
            'user' =>
                auth()->guard('karyawan')->user()->hak_akses
                . '-'
                . auth()->guard('karyawan')->user()->id,
            'ip' => $request->ip(),
            'keterangan' => json_encode([
                'data' => $cek,
            ]),
            'serial' => route('gaji.store'),
        ];

        $log->create($logs5);

        return back()->with(
            'success',
            'Periode Gaji Karyawan Berhasil Ditambah'
        );
    } catch (\Throwable $th) {
        return back()
            ->withInput()
            ->with('error', $th->getMessage());
    }
}

  public function coba(Request $request)
  {
    // code...
    $item =  SetPayroll::with('itemGaji')->orderBy('itemGaji.kategori_item_id', 'asc')->get();
    return $item;
  }

  public function findSundays($newDateStringMulai, $newDateStringAkhir, $periode_id)
  {
    // Buat instance Carbon untuk tanggal awal dan akhir


    $karyawanAll = Karyawan::get();

    //  foreach ($karyawanAll as $key){

    try {

      $karyawanAll->chunk(60)->each(function ($chunk) use ($newDateStringMulai, $newDateStringAkhir, $periode_id) {



        foreach ($chunk as $key) {

          $dataPer = [
            'karyawan_id' => $key->id,
            'periode_gaji_id' =>  $periode_id,
          ];

          $getTransAbsen =  TransAbsen::create($dataPer);

          $getTransAbsensi = TransAbsen::where('karyawan_id', $key->id)->orderBy('id', 'desc')->first();



          //$per = PeriodeGaji::where('id', $periode_id)->first();
          $startDate = \Carbon\Carbon::parse($newDateStringMulai);
          $endDate = \Carbon\Carbon::parse($newDateStringAkhir);

          // Array untuk menyimpan hari Minggu
          $sundays = [];
          $days = [];

          // Loop dari tanggal awal sampai akhir
          while ($startDate->lte($endDate)) {
            // Cek apakah hari ini adalah hari Minggu


            $days[] = $startDate->toDateString();

            $data = [
              'karyawan_id' =>  $key->id,
              'tanggal' => date('Y-m-d', strtotime($startDate->toDateString())),
              'status_absensi' => 'A',
              'trans_absen_id' => $getTransAbsensi->id,
            ];
            $cek = Absensi::where('karyawan_id', $key->id)->where('tanggal', date('Y-m-d', strtotime($startDate->toDateString())))->first();

            if (empty($cek->id)) {
              Absensi::insert($data);


              //  InsertAbsensiBatchJob::dispatch($data);

              $logs5 = [
                'tanggal' => now(),
                'tabel' => 'tb_absensi',
                'aksi' => 'Create',
                'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
                'ip' => '127.0.0.1',
                'keterangan' => json_encode(['data' => $data]),
                'serial' => url('fetchGaji'),
              ];


              Log::create($logs5);
            }

            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $startDate->toDateString());

            if ($date->format('l') === 'Saturday') {

              $data_sat = [
                'status_absensi' => 'L',
              ];
              Absensi::where('tanggal', $startDate->toDateString())->where('karyawan_id', $key->id)->update($data_sat);
            }


            if ($startDate->isSunday()) {
              // Jika ya, tambahkan ke array
              $sundays[] = $startDate->toDateString();

              $data_sunday = [
                'status_absensi' => 'L',
              ];

              $cek2 = Absensi::where('karyawan_id', $key->id)->where('tanggal', $startDate->toDateString())->first();

              Absensi::where('tanggal', $startDate->toDateString())->where('karyawan_id', $key->id)->update($data_sunday);
            }

            // Tambah satu hari
            $startDate->addDay();
          }
        } // foreach ($chunk as $key) {

      }); // $karyawanAll->chunk(60)->each(function ($chunk) use ($startDate, $getTransAbsensi) {

      // Return array hari Minggu
      return response()->json($sundays);
    } catch (\Exception $e) {
      // Tangani kalau gagal
      \Log::error('Gagal insert chunk: ' . $e->getMessage());

      // Bisa juga tampilkan error, atau lanjutkan ke chunk berikutnya
    }
  }

 public function findSundaysToKaryawan(Request $request)
{
    $request->validate([
        'karyawan_id' => 'required|integer|exists:tb_karyawan,id',
    ]);

    $cekPeriode = PeriodeGaji::orderByDesc('id')->first();

    if (!$cekPeriode) {
        return response()->json([
            'status' => false,
            'message' => 'Periode gaji tidak ditemukan.',
        ], 404);
    }

    $karyawan = Karyawan::find($request->karyawan_id);

    if (!$karyawan) {
        return response()->json([
            'status' => false,
            'message' => 'Karyawan tidak ditemukan.',
        ], 404);
    }

    try {

        $transAbsen = TransAbsen::create([
            'karyawan_id' => $karyawan->id,
            'periode_gaji_id' => $cekPeriode->id,
        ]);

        $startDate = \Carbon\Carbon::parse(
            $cekPeriode->mulai
        );

        $endDate = \Carbon\Carbon::parse(
            $cekPeriode->selesai
        );

        $sundays = [];

        while ($startDate->lte($endDate)) {

            $tanggal = $startDate->toDateString();

            $cek = Absensi::where(
                'karyawan_id',
                $karyawan->id
            )
            ->where(
                'tanggal',
                $tanggal
            )
            ->first();

            /*
            |--------------------------------------------------------------------------
            | BUAT ABSENSI JIKA BELUM ADA
            |--------------------------------------------------------------------------
            */

            if (!$cek) {

                $status = 'A';

                // Sabtu / Minggu = Libur
                if (
                    $startDate->isSaturday() ||
                    $startDate->isSunday()
                ) {
                    $status = 'L';
                }

                $data = [
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $tanggal,
                    'status_absensi' => $status,
                    'trans_absen_id' => $transAbsen->id,
                ];

                Absensi::create($data);

                $tahun = date('Y');

                $jatah_cuti = DB::table('konfigs')->first();

                Cuti::create([
                        'karyawan_id' => $karyawan->id,
                        'tahun' => $tahun,
                        'total_hari' => 0,
                        'jatah_days' => $jatah_cuti->jatah_cuti,
                ]);

                $logs5 = [
                    'tanggal' => now(),
                    'tabel' => 'tb_absensi',
                    'aksi' => 'Create',
                    'user' =>
                        auth()
                            ->guard('karyawan')
                            ->user()
                            ->hak_akses
                        . '-'
                        . auth()
                            ->guard('karyawan')
                            ->user()
                            ->id,
                    'ip' => $request->ip(),
                    'keterangan' => json_encode([
                        'data' => $data,
                    ]),
                    'serial' => url('fetchGaji'),
                ];

                Log::create($logs5);

            } else {

                /*
                |--------------------------------------------------------------------------
                | KALAU SUDAH ADA DAN SABTU / MINGGU
                |--------------------------------------------------------------------------
                */

                if (
                    $startDate->isSaturday() ||
                    $startDate->isSunday()
                ) {
                    $cek->update([
                        'status_absensi' => 'L',
                    ]);
                }
            }

            if ($startDate->isSunday()) {
                $sundays[] = $tanggal;
            }

            $startDate->addDay();
        }

        return back()->with('success', 'Berhasil');

        // return response()->json([
        //     'status' => true,
        //     'message' => 'Absensi berhasil dibuat.',
        //     'karyawan' => [
        //         'id' => $karyawan->id,
        //         'nama' => $karyawan->nama_lengkap,
        //     ],
        //     'periode' => [
        //         'id' => $cekPeriode->id,
        //         'mulai' => $cekPeriode->mulai,
        //         'selesai' => $cekPeriode->selesai,
        //     ],
        //     'minggu' => $sundays,
        // ]);

    } catch (\Exception $e) {

        \Log::error(
            'Gagal membuat absensi karyawan: '
            . $e->getMessage()
        );

        return response()->json([
            'status' => false,
            'message' => 'Gagal membuat absensi.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
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
  // public function update(Request $request, PeriodeGaji $periodeGaji)
  // {
  //   try {
  //     $cek = $periodeGaji->update($request->all());
  //     if ($cek) {
  //       return back()->with('success', 'Periode Gaji Karyawan Berhasil Diubah');
  //     } else {
  //       return back()->with('error', 'Periode Gaji Karyawan Gagal Diubah');
  //     }

  //     $logs5 = [
  //       'tanggal' => now(),
  //       'tabel' => 'periodeGaji',
  //       'aksi' => 'update',
  //       'user' => auth()->guard('karyawan')->user()->hak_akses . '-' . auth()->guard('karyawan')->user()->id,
  //       'ip' => $request->ip(),
  //       'keterangan' => json_encode(['data' => $request->all()]),
  //       'serial' => route('periodeGaji.update'),
  //     ];


  //     $log->create($logs5);
  //   } catch (\Throwable $th) {
  //     return back()->with('error', $th->getMessage());
  //   }
  // }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(
    Request $request,
    PeriodeGaji $periodeGaji,
    Log $log
) {
    DB::beginTransaction();

    try {
        $periode = PeriodeGaji::find($periodeGaji->id);

        if (!$periode) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Periode gaji tidak ditemukan.',
            ], 404);
        }

        $gajiIds = Gaji::where(
            'periode_gaji_id',
            $periode->id
        )->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Hapus transaksi gaji
        |--------------------------------------------------------------------------
        */
        if ($gajiIds->isNotEmpty()) {
            TransGaji::whereIn(
                'gaji_id',
                $gajiIds
            )->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus transaksi absensi periode
        |--------------------------------------------------------------------------
        */
        TransAbsen::where(
            'periode_gaji_id',
            $periode->id
        )->delete();

        /*
        |--------------------------------------------------------------------------
        | Hapus data gaji
        |--------------------------------------------------------------------------
        */
        Gaji::where(
            'periode_gaji_id',
            $periode->id
        )->delete();

        /*
        |--------------------------------------------------------------------------
        | Hati-hati: ini menghapus semua absensi dalam rentang tanggal.
        | Aktifkan hanya jika memang absensi dibuat khusus untuk periode tersebut.
        |--------------------------------------------------------------------------
        */
        Absensi::whereBetween(
            'tanggal',
            [
                $periode->mulai,
                $periode->selesai,
            ]
        )->delete();

        $dataLog = [
            'id' => $periode->id,
            'mulai' => $periode->mulai,
            'selesai' => $periode->selesai,
        ];

        $periode->delete();

        $user = auth()
            ->guard('karyawan')
            ->user();

        $log->create([
            'tanggal' => now(),
            'tabel' => 'periodeGaji',
            'aksi' => 'delete',
            'user' => $user
                ? $user->hak_akses . '-' . $user->id
                : 'system',
            'ip' => $request->ip(),
            'keterangan' => json_encode([
                'data' => $dataLog,
            ]),
            'serial' => route(
                'gaji.destroy',
                $periodeGaji->id
            ),
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Periode gaji berhasil dihapus.',
        ]);
    } catch (\Throwable $th) {
        DB::rollBack();

        report($th);

        return response()->json([
            'status' => false,
            'message' => 'Periode gaji gagal dihapus.',
            'error' => config('app.debug')
                ? $th->getMessage()
                : null,
        ], 500);
    }
}
}
