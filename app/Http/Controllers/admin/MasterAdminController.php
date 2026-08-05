<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Dapartement;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Log;
use Auth;

class MasterAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    //  return $this->middleware('isAuth');
    }

    public function index(Karyawan $karyawan, Dapartement $dapartement, Jabatan $jabatan)
    {
      $data = $karyawan->where('sts_karyawan','!=','SUPERADMIN')
      ->where('sts_karyawan','!=','ADMIN')->get();

      return view('admin.dashboard-admin', compact('data','dapartement', 'jabatan'))->with(['cekNav' => 'dashboard']);
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
    public function edit(Profile $profile)
    {

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
        //
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

		public function profile(Profile $profile)
		{
			$data = $profile->first();
      return view('admin.dashboard-profile', compact('data'))->with(['cekNav' => 'setting']);
		}

		public function updateProfile(Profile $profile, Request $request,Log $log)
		{
			try {


				$cek = $profile->update($request->all());
				if ($cek) {
					return back()->with('success','Profile Perusahaan Berhasil Diubah');
				} else {
					return back()->with('error','Profile Perusahaan Gagal Diubah');
				}

        $logs2 =[
          'tanggal'=>now(),
          'tabel'=> 'tb_profile',
          'aksi'=> 'Update',
          'user' => auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode([ 'data' => $request->all()]),
          'serial' => route('karywanAbsen.store'),
        ];

          $log->create($logs2);

			} catch (\Throwable $th) {
				return back()->with('error',$th->getMessage());
			}
		}
}
