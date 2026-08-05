<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Log;
use Auth;

class MasterJabatanController extends Controller
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

    public function index(Jabatan $jabatan, Request $request)
    {
			$data = $jabatan->get();
			if ($request->ajax()) {
					return DataTables::of($data)
						->addIndexColumn()
						->addColumn('action', function ($data){
							return '
							<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#ubah-'.$data->id.'"><i class="fas fa-pen"></i></button>
							<a onclick="confirm_delete( \''.route('jabatan.destroy',$data->id).'\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-trash "></i></a>
							';
						})
						->rawColumns(['action'])
						->make(true);
				}
			return view('admin.dashboard-jabatan', compact('data'))->with(['cekNav2' => 'jabatan']);
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
    public function store(Request $request, Jabatan $jabatan,Log $log)
    {
			try {
				$cek = $jabatan->create($request->all());
				if ($cek) {
					return back()->with('success','Jabatan Baru Behasil ditambah');
				} else {
					return back()->with('error','Jabatan Baru Gagal ditambah');
				}

				$logs5 =[
          'tanggal'=>now(),
          'tabel'=> 'tb_jabatan',
          'aksi'=> 'create',
          'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode(['data' => $request->all()]),
          'serial' => route('jabatan.store'),
        ];


        $log->create($logs5);

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
    public function update(Request $request, Jabatan $jabatan,Log $log)
    {

		
			try {
				$cek = $jabatan->update($request->all());
				if ($cek) {
			//		return back()->with('success','Jabatan Behasil diubah');
				} else {
					//return back()->with('error','Jabatan Gagal diubah');
				}

				$logs5 =[
          'tanggal'=>now(),
          'tabel'=> 'tb_jabatan',
          'aksi'=> 'update',
          'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode(['data' => $request->all()]),
          'serial' => route('jabatan.update'),
        ];


        $log->create($logs5);

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


    public function destroy(Jabatan $jabatan,Log $log)
    {
			try {
				$data = $jabatan->delete();
				if ($data) {
					return response()->json(['success'=>'Success']);
				}
				else {
					return response()->json(['error'=>'Product Update failed.']);
				}

				$logs5 =[
					'tanggal'=>now(),
					'tabel'=> 'tb_jabatan',
					'aksi'=> 'delete',
					'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
					'ip' => $request->ip(),
					'keterangan' => json_encode(['data' => $data]),
					'serial' => route('jabatan.update'),
				];


				$log->create($logs5);
			} catch (\Throwable $th) {
				return response()->json(['error'=>$th->getMessage()]);
			}
    }
}
