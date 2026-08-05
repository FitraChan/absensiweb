<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SetPayroll;
use App\Models\Dapartement;
use App\Models\ItemGaji;



use Illuminate\Http\Request;

use App\Models\Log;
use Yajra\DataTables\DataTables;
use Auth;

class PayrollSetting extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SetPayroll $setPayroll, Request $request)
    {
        $data = $setPayroll->orderBy('departemen_id','asc')->orderBy('item_gaji_id','asc')->latest();
        $departement = Dapartement::get();
        $itemGaji = ItemGaji::get();

        if ($request->ajax()) {
          return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data){
              return '
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#data-'.$data->id.'"><i class="fas fa-regular fa-eye"></i></button>
              <a onclick="confirm_delete( \''.route('payrollSetting.destroy',$data->id).'\', \'Are you sure want to delete data ?\')" class="btn btn-danger text-light"><i class="fas fa-trash "></i></a>

              ';
            })
            ->filter(function ($instance) use ($request) {

              if ($request->get('departement_id') != null) {
                  $instance->where('departemen_id', $request->get('departement_id'))->get();
              }

            })

            ->editColumn('departemen_id',function ($row)
            {
              return $row->departement->nama_departement;
            })
            ->editColumn('item_gaji_id',function ($row)
            {
              return $row->itemGaji->nama_item_gaji;
            })

            ->editColumn('nominal',function ($row)
            {
              return number_format($row->nominal, 0, ',', '.');
            })

            ->rawColumns(['action'])
            ->make(true);
       }

       return view('admin.dashboard-payroll-setting', compact('data','departement','itemGaji'))->with(['cekNav2' => 'setPayroll']);


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
    public function store(Request $request, SetPayroll $setPayroll,Log $log)
    {

      try {
        $cek = $setPayroll->create($request->all());
        if ($cek) {
          return back()->with('success','Data Baru Behasil ditambah');
        } else {
          return back()->with('error','Data Baru Gagal ditambah');
        }

        $logs5 =[
          'tanggal'=>now(),
          'tabel'=> 'departemen_item_gaji',
          'aksi'=> 'create',
          'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode(['data' => $request->all()]),
          'serial' => route('payrollSetting.store'),
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
    public function update(Request $request, SetPayroll $setPayroll,Log $log)
    {

        //  dd(SetPayroll);

      //  $try = $setPayroll->where('nomina',$request->nominal)->first();


      try {
        $cek = $setPayroll->where('id',$request->id)->update(['nominal' => $request->nominal, 'departemen_id' => $request->departemen_id, 'item_gaji_id' => $request->item_gaji_id]);


        if ($cek) {
          return back()->with('success','Data Behasil diubah');
        } else {
          return back()->with('error','Data Gagal diubah');
        }

        $logs5 =[
          'tanggal'=>now(),
          'tabel'=> 'departemen_item_gaji',
          'aksi'=> 'update',
          'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode(['data' => $request->all()]),
          'serial' => route('payrollSetting.update'),
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
    public function destroy(Request $request, $id ,SetPayroll $setPayroll,Log $log)
    {
      try {
      //  $data = $setPayroll->delete();

      $data = $setPayroll->where('id',$id)->delete();
        if ($data) {
        //  return response()->json(['success'=>'Success']);
        }
        else {
        //  return response()->json(['error'=>'Data Update failed.']);
        }

        $logs5 =[
          'tanggal'=>now(),
          'tabel'=> 'departemen_item_gaji',
          'aksi'=> 'delete',
          'user' => auth()->guard('karyawan')->user()->hak_akses.'-'.auth()->guard('karyawan')->user()->id,
          'ip' => $request->ip(),
          'keterangan' => json_encode(['data' => $data]),
          'serial' => route('payrollSetting.destroy'),
        ];


        $log->create($logs5);
      } catch (\Throwable $th) {
        return response()->json(['error'=>$th->getMessage()]);
      }
    }
}
