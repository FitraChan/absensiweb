<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsenExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Absensi::join('tb_karyawan', 'tb_karyawan.id', '=', 'tb_absensi.karyawan_id')
        ->join('tb_jam_kerja', 'tb_jam_kerja.id', '=', 'tb_absensi.jam_kerja_id')
        ->select('tb_jam_kerja.nama_shift','tb_karyawan.nama_lengkap','tb_karyawan.email','tanggal','jam_masuk','jam_pulang','status_absensi')
        ->get();
    }

    public function headings():array
    {
        return [
            'Nama Shift',
            'Nama Karyawan',
            'Email',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status Absensi',
        ];
    }
}
