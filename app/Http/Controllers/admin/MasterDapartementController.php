<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Dapartement;
use App\Models\Log;
use Auth;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterDapartementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
		public function __construct()
    {
      return $this->middleware('isAuth');
    }

    public function index(Dapartement $dapartement, Request $request)
    {
        $data = $dapartement->get();
        if ($request->ajax()) {
            return DataTables::of($data)
              ->addIndexColumn()
              ->addColumn('action', function ($data){
                return '
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#ubah-'.$data->id.'"><i class="fas fa-pen"></i></button>
								<a onclick="confirm_delete( \''.route('dapartement.destroy',$data->id).'\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-trash "></i></a>
								';
              })
              ->rawColumns(['action'])
              ->make(true);
          }
        return view('admin.dashboard-dapartement', compact('data'))->with(['cekNav2' => 'dapartement']);
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
    public function store(Request $request, Dapartement $dapartement,Log $log)
    {
			try {
				$cek = $dapartement->create($request->all());

				$logs5 =[
					'tanggal'=>now(),
					'tabel'=> 'tb_departement',
					'aksi'=> 'create',
					'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
					'ip' => $request->ip(),
					'keterangan' => json_encode(['data' => $request->all()]),
					'serial' => route('dapartement.store'),
				];


				$log->create($logs5);
				if ($cek) {
					return back()->with('success','Dapartement Behasil ditambah');
				} else {
					return back()->with('error','Dapartement Gagal ditambah');
				}

			} catch (\Throwable $th) {
				return back()->with('error',$th->getMessage());
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
    public function update(Request $request, Dapartement $dapartement,Log $log)
    {
			try {
				$cek = $dapartement->update($request->all());

				$logs5 =[
					'tanggal'=>now(),
					'tabel'=> 'tb_departement',
					'aksi'=> 'update',
					'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
					'ip' => $request->ip(),
					'keterangan' => json_encode(['data' => $request->all()]),
					'serial' => route('dapartement.update'),
				];


				$log->create($logs5);
				if ($cek) {
					return back()->with('success','Dapartement Behasil diubah');
				} else {
					return back()->with('error','Dapartement Gagal diubah');
				}

			} catch (\Throwable $th) {
				return back()->with('error',$th->getMessage());
			}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dapartement $dapartement)
    {
			try {
				$data = $dapartement->delete();

				$logs5 =[
					'tanggal'=>now(),
					'tabel'=> 'tb_departement',
					'aksi'=> 'delete',
					'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
					'ip' => $request->ip(),
					'keterangan' => json_encode(['data' => $data]),
					'serial' => route('dapartement.destroy'),
				];


				$log->create($logs5);


				if ($data) {
					return response()->json(['success'=>'Success']);
				}
				else {
					return response()->json(['error'=>'Product Update failed.']);
				}
			} catch (\Throwable $th) {
				return response()->json(['error'=>$th->getMessage()]);
			}
    }
}
