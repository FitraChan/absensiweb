<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message; // Sesuaikan dengan model pesan
use Auth;

class InboxController extends Controller
{
  public function index()
 {
     $messages = Message::limit(50)->orderBy('id','desc')->get();
     return view('admin.inbox', compact('messages'));
 }

     public function markAsRead($id)
    {
        $message = Message::where('id', $id)
                          ->firstOrFail();

        // Update status menjadi sudah dibaca
        $message->update(['is_read' => true,'receiver_id' => auth()->guard('karyawan')->user()->id]);

        // Redirect ke halaman inbox atau detail pesan
        if($message->jenis_id == 1){
          return redirect()->route('karywanAbsen.index')->with('success', 'Pesan telah dibaca.');
        }else{
          return redirect()->route('lembur.index')->with('success', 'Pesan telah dibaca.');
        }
    }
}
