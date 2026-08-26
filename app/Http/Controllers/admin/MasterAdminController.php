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
    $data = $karyawan->where('sts_karyawan', '!=', 'SUPERADMIN')
      ->where('sts_karyawan', '!=', 'ADMIN')->get();

    return view('admin.dashboard-admin', compact('data', 'dapartement', 'jabatan'))->with(['cekNav' => 'dashboard']);
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
  public function edit(Profile $profile) {}

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
    $data = $profile->get();

    return view('admin.dashboard-profile', compact('data'))
      ->with(['cekNav' => 'setting']);
  }


  public function createProfile()
  {
    return view('admin.dashboard-profile-create')
      ->with(['cekNav' => 'setting']);
  }


  


  public function editProfile(Profile $profile, $id)
  {
    $data = $profile->findOrFail($id);

    return view('admin.dashboard-profile-edit', compact('data'))
      ->with(['cekNav' => 'setting']);
  }

  public function storeProfile(Profile $profile,Request $request, Log $log)
  {
    try {

      $request->validate([
        'email' => 'nullable|email',
        'nama_perusahaan' => 'required',
        'telepon' => 'nullable',
        'website' => 'nullable',
        'alamat' => 'nullable',
        'lat' => 'nullable',
        'lang' => 'nullable',
      ]);

      $data = $request->only([
        'email',
        'nama_perusahaan',
        'telepon',
        'website',
        'alamat',
        'lat',
        'lang',
      ]);

      $cek = $profile->create($data);

      if ($cek) {

        $logs = [
          'tanggal' => now(),
          'tabel' => 'tb_profile',
          'aksi' => 'Create',
          'user' => auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode([
            'data' => $data
          ]),
          'serial' => url()->current(),
        ];

        $log->create($logs);

        return redirect()
          ->route('profile',1)
          ->with('success', 'Profile Perusahaan Berhasil Ditambahkan');
      }

      return back()->with('error', 'Profile Perusahaan Gagal Ditambahkan');
    } catch (\Throwable $th) {

      return back()
        ->withInput()
        ->with('error', $th->getMessage());
    }
  }


  public function updateProfile(Profile $profile, Request $request, Log $log, $id)
  {
    try {

      $data = $profile->findOrFail($id);

      $request->validate([
        'email' => 'nullable|email',
        'nama_perusahaan' => 'required',
        'telepon' => 'nullable',
        'website' => 'nullable',
        'alamat' => 'nullable',
        'lat' => 'nullable',
        'lang' => 'nullable',
      ]);

      $updateData = $request->only([
        'email',
        'nama_perusahaan',
        'telepon',
        'website',
        'alamat',
        'lat',
        'lang',
      ]);

      $cek = $data->update($updateData);

      if ($cek) {

        $logs = [
          'tanggal' => now(),
          'tabel' => 'tb_profile',
          'aksi' => 'Update',
          'user' => auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode([
            'data' => $updateData
          ]),
          'serial' => url()->current(),
        ];

        $log->create($logs);

        return redirect()
          ->route('profile')
          ->with('success', 'Profile Perusahaan Berhasil Diubah');
      }

      return back()->with('error', 'Profile Perusahaan Gagal Diubah');
    } catch (\Throwable $th) {

      return back()
        ->withInput()
        ->with('error', $th->getMessage());
    }
  }


  public function destroyProfile(Profile $profile, Log $log, Request $request, $id)
  {
    try {

      $data = $profile->findOrFail($id);

      $dataProfile = $data->toArray();

      $data->delete();

      $logs = [
        'tanggal' => now(),
        'tabel' => 'tb_profile',
        'aksi' => 'Delete',
        'user' => auth()->guard('karyawan')->user()->id,
        'ip' => $request->ip(),
        'keterangan' => json_encode([
          'data' => $dataProfile
        ]),
        'serial' => url()->current(),
      ];

      $log->create($logs);

      return back()->with(
        'success',
        'Profile Perusahaan Berhasil Dihapus'
      );
    } catch (\Throwable $th) {

      return back()->with(
        'error',
        $th->getMessage()
      );
    }
  }
}
