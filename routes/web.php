<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemGajiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

        Route::get('/', function () {
            return view('auth.login-admin');
        })->name('home');

        Route::get('/forgot-email', function () {
            return view('auth.forgot_pass-admin');
        })->name('cek-email');
        Route::get('forgot-password/{token}', 'admin\MasterAuthController@verifyPass')->name('password-update');
        Route::put('forgot-password/{passwordReset}', 'admin\MasterAuthController@resetPass')->name('resetPass');

        Route::get('logout', 'admin\MasterAuthController@logout')->name('logout');
        Route::post('login', 'admin\MasterAuthController@cekLogin')->name('login');
        Route::post('email', 'admin\MasterAuthController@cekEmail')->name('email-verfy');
  Route::middleware(['isAuth'])->group(function(){
        // Route::get('admin/setting/{profile}', 'admin\MasterAdminController@profile')->name('profile');
        // Route::put('admin/setting/{profile}', 'admin\MasterAdminController@updateProfile')->name('update-profile');


       // ==============================
        // PROFILE
        // ==============================

        // Form tambah
        Route::get(
            'admin/setting/create',
            'admin\MasterAdminController@createProfile'
        )->name('profile.create');

        // Simpan
        Route::post(
                'storeProfile',
                'admin\MasterAdminController@storeProfile'
            )->name('profile.store');

        // Edit
        Route::get(
            'admin/setting/{profile}/edit',
            'admin\MasterAdminController@editProfile'
        )->name('profile.edit');

        // Update
        Route::put(
            'admin/setting/{profile}',
            'admin\MasterAdminController@updateProfile'
        )->name('update-profile');

        // Hapus
        Route::delete(
            'admin/setting/{profile}',
            'admin\MasterAdminController@destroyProfile'
        )->name('profile.destroy');

        // Daftar/detail profile
        Route::get(
            'admin/setting/{profile}',
            'admin\MasterAdminController@profile'
        )->name('profile');


        Route::get('absensi/karyawan/{karyawan}', 'admin\MasterAbensiController@absensi')->name('karywan-absen');
        Route::post('dowload/absen', 'admin\MasterAbensiController@exelAbsen')->name('download-absen');
        Route::post('dowload/detail', 'admin\MasterAbensiController@exelDetailAbsen')->name('download-detail-absen');
        Route::put('group/karyawan/{karyawan}', 'admin\MasterGroupController@updateKaryawan')->name('group-karyawan');
        Route::get('fetchKaryawan', 'admin\MasterAbensiController@fetchKaryawan')->name('fetchKaryawan');

        Route::resource('admin', 'admin\MasterAdminController',['names'=>'admin']);
        Route::resource('jabatan', 'admin\MasterJabatanController',['names'=>'jabatan']);
        Route::resource('dapartement', 'admin\MasterDapartementController',['names'=>'dapartement']);
        Route::resource('jamKerja', 'admin\MasterJamKerjaController',['names'=>'jamKerja']);
        Route::resource('karyawan', 'admin\MasterKaryawanController',['names'=>'karyawan']);
        Route::resource('pendidikan', 'admin\MasterPendidikanController',['names'=>'pendidikan']);
        Route::resource('penggajian', 'PenggajianController',['names'=>'penggajian']);
        Route::resource('agama', 'admin\MasterAgamaController',['names'=>'agama']);
        Route::resource('periodeGaji', 'admin\MasterGajiController',['names'=>'gaji']);

      


        Route::resource('absensi', 'admin\MasterAbensiController',['names'=>'absensi']);
        Route::resource('karywanAbsen', 'admin\MasterKaryawanAbsenController',['names'=>'karywanAbsen']);
        Route::resource('karyawans', 'admin\MasterKaryawanAdminController',['names'=>'adminKaryawan']);
        Route::resource('groupJadwal', 'admin\MasterGroupController',['names'=>'group']);
        Route::resource('aturan-potongan','AturanPotonganController',['names'=>'aturan-potongan']);

        Route::resource('payrollSetting', 'PayrollSetting',['names'=>'payrollSetting']);


        Route::resource('detailGroupJadwal', 'admin\MasterDetailGroupController',['names'=>'detailGroup']);
        //Route::resource('item-gaji', ItemGajiController::class);

        Route::resource('item-gaji', 'ItemGajiController',['names'=>'item-gaji']);



        Route::get('findSundays/{id}/{periodeId}', 'admin\MasterAbensiController@findSundays')->name('findSundays');
        Route::get('cetakPdf2', 'PenggajianController@cetakPdf2')->name('cetakPdf2');
        Route::get('gaji/{id}', 'PenggajianController@gaji')->name('gaji');
        Route::get('fetchGaji', 'PenggajianController@fetchGaji')->name('fetchGaji');
        Route::post('/find-sundays-karyawan','admin\MasterGajiController@findSundaysToKaryawan')->name('findSundaysToKaryawan');


        Route::resource('inbox', 'InboxController',['names'=>'inbox']);

        Route::get('/messages/{id}/read', 'InboxController@markAsRead')->name('messages.read');
        // Route::get('cetakPdf', 'PenggajianController@cetakPdf')->name('cetakPdf');
        // Route::get('cetakAbsenPdf', 'PenggajianController@cetakAbsenPdf')->name('cetakAbsenPdf');
        Route::get('cuti', 'PenggajianController@cuti')->name('cuti');

        Route::get('downloadMultipleAbsen', 'PenggajianController@downloadMultipleAbsen')->name('downloadMultipleAbsen');
        Route::get('downloadMultipleGaji', 'PenggajianController@downloadMultipleGaji')->name('downloadMultipleGaji');
        Route::get('coba', 'admin\MasterGajiController@coba')->name('coba');

        Route::resource('lembur', 'LemburController',['names'=>'lembur']);
        Route::resource('jadwalKerja', 'JadwalKerjaController',['names'=>'jadwalKerja']);
        Route::get('groupKerja', 'JadwalKerjaController@groupKerja')->name('groupKerja');
        Route::post('simpanGroupKerja', 'JadwalKerjaController@simpanGroupKerja')->name('simpanGroupKerja');

        Route::get('pencarianJadwal', 'JadwalKerjaController@pencarianJadwal')->name('pencarianJadwal');

        Route::get('dataJadwalKaryawan', 'JadwalKerjaController@dataJadwalKaryawan')->name('dataJadwalKaryawan');
        Route::get('penentuanLibur', 'admin\MasterAbensiController@penentuanLibur')->name('penentuanLibur');
        Route::post('penentuanLiburStore', 'admin\MasterAbensiController@penentuanLiburStore')->name('penentuanLiburStore');
        Route::put('penentuanLiburUpdate/{id}', 'admin\MasterAbensiController@penentuanLiburUpdate')->name('penentuanLiburUpdate');
        Route::delete('destroyLibur/{id}', 'admin\MasterAbensiController@destroyLibur')->name('destroyLibur');
        Route::post('/absensi/status-range','admin\MasterKaryawanAbsenController@updateRange')->name('absensi-status.update-range');












        //Route::post('updateCell', 'admin\MasterDapartementController@updateCell')->name('updateCell');







   });
