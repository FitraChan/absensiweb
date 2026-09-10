<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Agama;
use App\Models\Dapartement;
use App\Models\Jabatan;
use App\Models\GroupJadwal;

use App\Models\Cuti;
use App\Models\Log;
use App\Models\Karyawan;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Auth;
use Svg\Tag\Group;

class MasterKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Karyawan $karyawan, Request $request, Jabatan $jabatan, Dapartement $dapartement, Agama $agama, Pendidikan $pendidikan,GroupJadwal $groupJadwal)
    {
			$data = $karyawan->get();
			if ($request->ajax()) {
				return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function ($data){
						return '
						<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#ubah-'.$data->id.'"><i class="fas fa-pen"></i></button>
						<a onclick="confirm_delete( \''.route('karyawan.destroy',$data->id).'\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-trash "></i></a>
						 <form
                        action="'.route('findSundaysToKaryawan').'"
                        method="POST"
                        style="display:inline-block;"
                    >
                        '.csrf_field().'

                        <input
                            type="hidden"
                            name="karyawan_id"
                            value="'.$data->id.'"
                        >

                        <button
                            type="submit"
                            class="btn btn-success"
                            title="Generate Absensi"
                            onclick="return confirm(
                                \'Generate absensi untuk karyawan ini?\'
                            )"
                        >
                            <i class="fas fa-calendar-check"></i>
                        </button>
                    </form>
						';
					})
					->editColumn('departement_id',function ($row)
					{
						return $row->departement?->nama_departement;
					})
					->editColumn('jabatan_id',function ($row)
					{
						return $row->jabatan?->nama_jabatan;
					})
					->rawColumns(['action'])
					->make(true);
			}
			return view('admin.dashboard-karyawan', compact('data','jabatan','dapartement', 'agama', 'pendidikan','groupJadwal'))->with(['cekNav2' => 'karyawan']);
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
    public function store(Request $request, Karyawan $karyawan)
    {
			DB::beginTransaction();
      try {
				$image = $request->file('image');

				if ($request->file('image') != null) {
					$imageName = 'profile_pengguna_'.Str::random(5).'.'.$image->extension();

					$image->storeAs('/public/pengguna/',$imageName);

					$request['foto'] = $imageName;
				}
				$request['password'] = bcrypt($request->password);

				$cek = $karyawan->create($request->all());

        $tahun = date('Y');

        Cuti::create([
            'karyawan_id' => $cek->id,
            'tahun' => $tahun,
            'total_hari' => 0,
            'jatah_days' => 12,
        ]);


				if ($cek) {

          $logs =[
            'tanggal'=>now(),
            'tabel'=> 'tb_karyawan',
            'aksi'=> 'Create',
            'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
            'ip' => $request->ip(),
            'keterangan' => json_encode([ 'data' => $request->all()]),
            'serial' => route('karyawan.store'),
          ];
          Log::create($logs);
					DB::commit();


					return back()->with('success','Data Pengguna Berhasil Ditambah');
				} else {
					DB::rollBack();
					return back()->with('error','Data Pengguna Gagal Ditambah');
				}

			} catch (\Throwable $th) {
				DB::rollBack();
				return back()->with('error', $th->getMessage());
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
    public function update(Request $request,Karyawan $karyawan)
    {
			DB::beginTransaction();
      try {
				$image = $request->file('image');

				if ($request->file('image') != null) {
					$imageName = 'profile_pengguna_'.Str::random(5).'.'.$image->extension();

					$destination = storage_path('app/public/pengguna/');
					if ($karyawan->foto != null) {
						try {
							unlink($destination.$karyawan->foto);
						} catch (\Throwable $th) {}
					}
					$image->storeAs('/public/pengguna/',$imageName);

					$request['foto'] = $imageName;
				}
				$request['password'] = bcrypt($request->password);

				$cek = $karyawan->update($request->all());
				if ($cek) {
					DB::commit();
					return back()->with('success','Data Pengguna Berhasil Ditambah');
				} else {
					DB::rollBack();
					return back()->with('error','Data Pengguna Gagal Ditambah');
				}

			} catch (\Throwable $th) {
				DB::rollBack();
				return back()->with('error', $th->getMessage());
			}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Karyawan $karyawan)
    {
			DB::beginTransaction();
			try {
				$destination = storage_path('app/public/pengguna/');
				if ($karyawan->foto != null) {
					try {
						unlink($destination.$karyawan->foto);
					} catch (\Throwable $th) {}
				}

				$cek = $karyawan->delete();
				if ($cek) {
					DB::commit();
					return response()->json(['success'=>'Success']);
				} else {
					DB::rollBack();
					return response()->json(['error'=>'Product Update failed.']);
				}
			} catch (\Throwable $th) {
				DB::rollBack();
				return back()->with('error', $th->getMessage());
			}
    }
}
