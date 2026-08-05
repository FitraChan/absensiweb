<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use App\Models\Message;

use Yajra\DataTables\DataTables;
use App\Models\Log;


class LemburController extends Controller
{
    //
    public function index(Request $request, Lembur $lembur)
    {
			$data = $lembur->orderBy('id','desc')->get();

      foreach ($data as $key) {
        // code...
        $key->karyawan;
      }
			// return $data[0]->detailGroupJadwal;
			if ($request->ajax()) {
				return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function ($data){
						return '
						  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#data-'.$data->id.'"><i class="fas fa-regular fa-eye"></i></button>
					  	<a onclick="confirm_delete( \''.route('group.destroy',$data->id).'\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-calendar-minus"></i></a>
						';
					})
          ->editColumn('sts_pengajuan',function ($data){
            $sts;
            if($data->sts_pengajuan == 0){
              $sts = 'Menunggu Konfirmasi';
            }else if($data->sts_pengajuan == 1){
						  $sts = 'Diterima';
            }else{
              $sts = 'Ditolak';
            }

             return $sts;
					})
          ->editColumn('tgl_pengajuan',function ($data){
              return date('d-m-Y H:i:s', strtotime($data->tgl_pengajuan));
					})
					->rawColumns(['action'])
					->make(true);
			}
			return view('admin.dashboard-lembur', compact('data'))->with(['cekNav'=>'lembur']);

    //  return response($data);
    }

    public function update(Request $request, Lembur $lembur,Log $log)
    {
      try {
	         $cek = $lembur->update($request->all());
           $status;
           if($request->sts_pengajuan == 1){
              $status = 'Diterima';
           }else{
              $status = 'Ditolak';
           }

           $pesan_akhir = Message::where('user_id',$request->karyawan_id)->orderBy('id','desc')->first();
           $last = $pesan_akhir->message.' Status '.$status;
           Message::where('id',$pesan_akhir->id)->update(['message' => $last]);

           if ($cek) {
             return back()->with('success','Lembur Berhasil diubah');
           } else {
             return back()->with('error','Lembur Gagal diubah');
           }
      }catch(\Throwable $th){
        	return back()->with('error',$th->getMessage());
      }
    }
}
