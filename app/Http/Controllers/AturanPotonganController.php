<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AturanPotongan;
use App\Models\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AturanPotonganController extends Controller
{
    public function __construct()
    {
        return $this->middleware('isAuth');
    }


    public function index(
        AturanPotongan $aturanPotongan,
        Request $request
    ) {
        $data = $aturanPotongan
            ->orderBy('jenis_potongan')
            ->orderBy('qty_mulai')
            ->get();

        if ($request->ajax()) {

            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('status', function ($data) {

                    if ($data->is_active) {

                        return '
                            <span class="badge badge-success">
                                Aktif
                            </span>
                        ';
                    }

                    return '
                        <span class="badge badge-danger">
                            Tidak Aktif
                        </span>
                    ';
                })

                ->addColumn('nilai', function ($data) {

                    if ($data->tipe_nilai == 'persen') {

                        return number_format(
                            $data->nilai_potongan,
                            2,
                            ',',
                            '.'
                        ) . '%';
                    }

                    return 'Rp ' . number_format(
                        $data->nilai_potongan,
                        0,
                        ',',
                        '.'
                    );
                })

                ->addColumn('action', function ($data) {

                    return '

                        <button
                            type="button"
                            class="btn btn-warning"
                            data-toggle="modal"
                            data-target="#ubah-'.$data->id.'">

                            <i class="fas fa-pen"></i>

                        </button>


                        <a
                            onclick="
                                confirm_delete(
                                    \''.route(
                                        'aturan-potongan.destroy',
                                        $data->id
                                    ).'\',
                                    \'Are you sure want to delete data ?\'
                                )
                            "
                            class="btn btn-danger text-light">

                            <i class="fas fa-trash"></i>

                        </a>

                    ';
                })

                ->rawColumns([
                    'status',
                    'nilai',
                    'action'
                ])

                ->make(true);
        }

        return view(
            'admin.dashboard-aturan-potongan',
            compact('data')
        )->with([
            'cekNav2' => 'aturan-potongan'
        ]);
    }


    public function create()
    {
        //
    }


    public function store(
        Request $request,
        AturanPotongan $aturanPotongan,
        Log $log
    ) {
        $request->validate([

            'konfig_id' => [
                'required',
                'integer',
            ],

            'jenis_potongan' => [
                'required',
                'string',
                'max:100',
            ],

            'nama_aturan' => [
                'required',
                'string',
                'max:255',
            ],

            'menit_mulai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'menit_selesai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'qty_mulai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'qty_selesai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'tipe_nilai' => [
                'required',
                'in:nominal,persen',
            ],

            'nilai_potongan' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sumber_potongan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        try {

            $data = $aturanPotongan->create([

                'konfig_id' =>
                    $request->konfig_id,

                'jenis_potongan' =>
                    $request->jenis_potongan,

                'nama_aturan' =>
                    $request->nama_aturan,

                'menit_mulai' =>
                    $request->menit_mulai,

                'menit_selesai' =>
                    $request->menit_selesai,

                'qty_mulai' =>
                    $request->qty_mulai,

                'qty_selesai' =>
                    $request->qty_selesai,

                'tipe_nilai' =>
                    $request->tipe_nilai,

                'nilai_potongan' =>
                    $request->nilai_potongan,

                'sumber_potongan' =>
                    $request->sumber_potongan,

                'is_active' =>
                    $request->is_active,

            ]);


            $logs5 = [

                'tanggal' => now(),

                'tabel' => 'aturan_potongan',

                'aksi' => 'create',

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
                    'data' => $data
                ]),

                'serial' =>
                    route('aturan-potongan.store'),

            ];

            $log->create($logs5);


            return back()->with(
                'success',
                'Aturan Potongan berhasil ditambah'
            );

        } catch (\Throwable $th) {

            return back()->with(
                'error',
                $th->getMessage()
            );
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(
        Request $request,
        AturanPotongan $aturanPotongan,
        Log $log
    ) {
        $request->validate([

            'konfig_id' => [
                'required',
                'integer',
            ],

            'jenis_potongan' => [
                'required',
                'string',
                'max:100',
            ],

            'nama_aturan' => [
                'required',
                'string',
                'max:255',
            ],

            'menit_mulai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'menit_selesai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'qty_mulai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'qty_selesai' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'tipe_nilai' => [
                'required',
                'in:nominal,persen',
            ],

            'nilai_potongan' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sumber_potongan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        try {

            $aturanPotongan->update([

                'konfig_id' =>
                    $request->konfig_id,

                'jenis_potongan' =>
                    $request->jenis_potongan,

                'nama_aturan' =>
                    $request->nama_aturan,

                'menit_mulai' =>
                    $request->menit_mulai,

                'menit_selesai' =>
                    $request->menit_selesai,

                'qty_mulai' =>
                    $request->qty_mulai,

                'qty_selesai' =>
                    $request->qty_selesai,

                'tipe_nilai' =>
                    $request->tipe_nilai,

                'nilai_potongan' =>
                    $request->nilai_potongan,

                'sumber_potongan' =>
                    $request->sumber_potongan,

                'is_active' =>
                    $request->is_active,

            ]);


            $logs5 = [

                'tanggal' => now(),

                'tabel' => 'aturan_potongan',

                'aksi' => 'update',

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
                    'data' => $aturanPotongan
                ]),

                'serial' => route(
                    'aturan-potongan.update',
                    $aturanPotongan->id
                ),

            ];

            $log->create($logs5);


            return back()->with(
                'success',
                'Aturan Potongan berhasil diubah'
            );

        } catch (\Throwable $th) {

            return back()->with(
                'error',
                $th->getMessage()
            );
        }
    }


    public function destroy(
        Request $request,
        AturanPotongan $aturanPotongan,
        Log $log
    ) {
        try {

            $oldData = $aturanPotongan->toArray();

            $aturanPotongan->delete();


            $logs5 = [

                'tanggal' => now(),

                'tabel' => 'aturan_potongan',

                'aksi' => 'delete',

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
                    'data' => $oldData
                ]),

                'serial' => route(
                    'aturan-potongan.destroy',
                    $aturanPotongan->id
                ),

            ];

            $log->create($logs5);


            return response()->json([
                'success' =>
                    'Aturan Potongan berhasil dihapus.'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }
}