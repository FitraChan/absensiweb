<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\KaryawanAbsen;
use App\Models\Cuti;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Message;
use App\Models\Karyawan;


use App\Models\Log;
use Yajra\DataTables\DataTables;
use Auth;


class MasterKaryawanAbsenController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request, KaryawanAbsen $karyawanAbsen)
  {
    $data = $karyawanAbsen::with('cuti')->latest();

     $karyawan = Karyawan::query()
        ->orderBy('nama_lengkap','asc')
        ->get();

    // return $data->karyawan;
    if ($request->ajax()) {
      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($data) {

          return '
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#data-' . $data->id . '"><i class="fas fa-regular fa-eye"></i></button>
						';
        })
        ->editColumn('karyawan_id', function ($data) {
          return $data->karyawan->nama_lengkap;
        })
        ->filter(function ($instance) use ($request) {
          if ($request->get('tahun') != null) {
            $instance->whereYear('tanggal_mulai', 'LIKE', ["%{$request->get('tahun')}%"])->get();
          }

          if ($request->get('bulan') != null) {
            $instance->whereMonth('tanggal_mulai', '=', $request->get('bulan'))->get();
          }

          if (!empty($request->get('search'))) {
            $instance
              ->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'LIKE', ["%{$request->get('search')}%"]);
              })
              ->get();
          }
        })
        ->rawColumns(['action'])
        ->make(true);
    }


    return view('admin.dashboard-karyawan_absen', compact('data', 'karyawan'))->with(['cekNav' => 'kabsen']);
  }

  public function updateRange(Request $request)
{
    $request->validate([
        'karyawan_id' => [
            'required',
            'integer',
        ],

        'tanggal_mulai' => [
            'required',
            'date',
        ],

        'tanggal_selesai' => [
            'required',
            'date',
            'after_or_equal:tanggal_mulai',
        ],

        'status_absensi' => [
            'required',
            'in:I,S,A,C',
        ],

        'keterangan' => [
            'nullable',
            'string',
            'max:1000',
        ],
    ]);

    $jumlahUpdate = Absensi::query()
        ->where(
            'karyawan_id',
            $request->karyawan_id
        )
        ->whereBetween('tanggal', [
           date('Y-m-d', strtotime($request->tanggal_mulai)),
           date('Y-m-d', strtotime($request->tanggal_selesai)),
        ])
        ->update([
            'status_absensi' => $request->status_absensi,
            'keterangan'     => $request->keterangan,
            'jam_masuk'      => null,
            'jam_pulang'     => null,
            'ot_in'          => null,
            'ot_out'         => null,
            'updated_at'     => now(),
        ]);

    if ($jumlahUpdate === 0) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Data absensi pada rentang tanggal tersebut tidak ditemukan.'
            );
    }

    return back()->with(
        'success',
        "{$jumlahUpdate} data absensi berhasil diperbarui."
    );
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
  public function store(Request $request)
  {
    //
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
  public function update(Request $request, KaryawanAbsen $karywanAbsen, Log $log)
  {
    try {
      $data = $karywanAbsen->update($request->all());

      $logs = [
        'tanggal' => now(),
        'tabel' => 'tb_karyawan_absen',
        'aksi' => 'Update',
        'user' => auth()->guard('karyawan')->user()->id,
        'ip' => $request->ip(),
        'keterangan' => json_encode(['data' => $request->all()]),
        'serial' => route('karywanAbsen.store'),
      ];


      $log->create($logs);

      $kary = $karywanAbsen->where('id', $request->id)->first();
      $year = date('Y');

      $cuti =   Cuti::where('karyawan_id', $kary->karyawan_id)->where('tahun', $year)->first();

      $jatah = $cuti->jatah_days - $kary->durasi;

      $total_hari = $cuti->total_hari + $kary->durasi;

      if ($jatah > 0) {

        if ($request->status == 'DISETUJUI') {


          //  $abs = Absensi::whereBetween('tanggal',[$kary->tanggal_mulai,$kary->tanggal_selesai])->get();
          $jns = '';
          if ($kary->jenis_absen == 'Izin') {
            $jns = 'I';
          } elseif ($kary->jenis_absen == 'Sakit') {
            $jns = 'S';
          } elseif ($kary->jenis_absen == 'Cuti') {
            $jns = 'C';
          } else {
            $jns = null;
          }

          $dates = json_decode($kary->tanggal, true);

          for ($i = 0; $i < $kary->durasi; $i++) {


            $update =  Absensi::where('tanggal', $dates[$i])->where('karyawan_id', $kary->karyawan_id)->update(['status_absensi' => $jns]);




            $dataUpdate = [
              'tanggal' => $dates[$i],
              'karyawan_id' => $kary->karyawan_id,
              'status_absensi' => $jns,
            ];

            $logs2 = [
              'tanggal' => now(),
              'tabel' => 'tb_absensi',
              'aksi' => 'Update',
              'user' => auth()->guard('karyawan')->user()->id,
              'ip' => $request->ip(),
              'keterangan' => json_encode(['data' => $dataUpdate]),
              'serial' => route('karywanAbsen.store'),
            ];

            $log->create($logs2);

            // echo "<pre>";
            //   print_r($dates[$i]);
            // echo "</pre>";

          } //  end of for ($i=0; $i < $kary->durasi ; $i++) {




          Cuti::where('karyawan_id', $kary->karyawan_id)->where('tahun', $year)->update(['jatah_days' => $jatah, 'total_hari' => $total_hari]);
        } // end of if($jatah > 0){
      } //  end of if($request->status == 'DISETUJUI'){

      $pesan_akhir = Message::where('user_id', $kary->karyawan_id)->orderBy('id', 'desc')->first();
      $last = $pesan_akhir->message . ' Status ' . $request->status;
      Message::where('id', $pesan_akhir->id)->update(['message' => $last]);

      if ($data) {
        return back()->with('success', 'Persetujuan Absen Karyawan ' . $request->status);
      }
    } catch (\Throwable $th) {
      return back()->with('error', $th->getMessage());
    }
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
}
