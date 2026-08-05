<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Log;
use Auth;



class MasterAuthController extends Controller
{
  public function cekLogin(Request $request, Karyawan $karyawan)
	{
		if (auth()->guard('karyawan')->attempt($request->only('email', 'password'),false)) {
			$cek = $karyawan->where('email',$request->email)->first();
			//if ($cek->hak_akses == "1") {
				return redirect()->route('profile',1);
			// } else {
			// 	return back()->with('warning','Hanya Admin Yang Boleh Login');
			// }
		}else{
			return redirect()->route('home');
		}
	}

	public function logout()
	{
		auth()->guard('karyawan')->logout();
		return redirect()->route('home');
	}

	public function cekEmail(Request $request, Karyawan $karyawan, PasswordReset $passwordReset)
	{
		try {
			$token = Str::random(15);
			$request['token'] = $token;
			$request['created_at'] = Carbon::now();

			// return $request->all();

			$cek_email = $karyawan->where('email',$request->email)->first();
			if ($cek_email) {
        Mail::send('auth.email', ['token' => $token], function($message) use($request){
					$message->to($request->email);
					$message->subject('Email Verification Mail');
			});
			$passwordReset->insert(request()->except(['_token']));
				return back()->with('success','Silahkan Cek Email Anda');
			} else {
				return back()->with('warning','Email Tidak Terdaftar');
			}

		} catch (\Throwable $th) {
			return back()->with('error',$th->getMessage());
		}
	}

	public function verifyPass($token, PasswordReset $passwordReset)
	{
		try {
			$cek = $passwordReset->find($token);
			if ($cek) {
				return view('auth.password-change', compact('cek', 'token'));
			} else {
				return redirect()->route('home')->with('error', 'Maaf Permintaan Anda Sudah Kadaluwarsa');
			}
		} catch (\Throwable $th) {
			return back()->with('error',$th->getMessage());
		}
	}

	public function resetPass(Request $request, Karyawan $karyawan, PasswordReset $passwordReset,Log $log)
	{
		// return $request->all();
		try {
			$cek = $karyawan->where(['email'=>$request->email])->update(['password'=>bcrypt($request->password)]);
			$passwordReset->delete();

			if ($cek) {
				return redirect()->route('home')->with('success', 'Password Anda Sudah Diganti Silahkan Login');
			} else {
				return redirect()-back()->with('error', 'Gagal Memperbarui Password');
			}

      $logs2 =[
        'tanggal'=>now(),
        'tabel'=> 'tb_karyawan',
        'aksi'=> 'Delete',
        'user' => auth()->guard('karyawan')->user()->id,
        'ip' => $request->ip(),
        'keterangan' => json_encode([ 'data' => $data]),
        'serial' => url('resetPass'),
      ];

        $log->create($logs2);


		} catch (\Throwable $th) {
			return back()->with('error',$th->getMessage());
		}

	}
}
