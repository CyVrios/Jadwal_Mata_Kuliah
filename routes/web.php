<?php

use App\Http\Controllers\Cdosen;
use App\Http\Controllers\Cjadwal;
use App\Http\Controllers\Cjadwal_2;
use App\Http\Controllers\Cmatkul;
use App\Http\Controllers\Cruangan;
use App\Http\Controllers\Cprodi;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cimport;

Route::get('/', function () {
    return view('welcome');
});

// route untuk jadwal 1
Route::post('/matkul/import', [Cimport::class, 'import'])->name('matkul.import');
Route::get('/jadwal/export', [Cjadwal::class, 'export'])->name('jadwal.export');
Route::get('/check-available-rooms', [Cjadwal::class, 'checkAvailableRooms']);
Route::post('/jadwal/checkDosen', [Cjadwal::class, 'checkDosen'])->name('jadwal.checkDosen');
Route::post('/jadwal/check-kelas-semester', [Cjadwal::class, 'checkKelasSemester'])->name('jadwal.checkKelasSemester');
Route::post('/jadwal/cek-slot', [Cjadwal::class, 'cekSlotKosong'])->name('jadwal.cekSlotKosong');
Route::post('/jadwal/cek-slot-kosong', [Cjadwal::class, 'cekSlotKosong'])->name('jadwal.cekSlotKosong');

//route untuk jadwal 2
Route::prefix('jadwal-2')->name('jadwal_2.')->group(function () {
    Route::get('/', [Cjadwal_2::class, 'index'])->name('index');
    Route::post('/store', [Cjadwal_2::class, 'store'])->name('store');

    Route::get('/export', [Cjadwal_2::class, 'export'])->name('export');

    Route::get('/check-available-rooms', [Cjadwal_2::class, 'checkAvailableRooms'])->name('checkAvailableRooms');
    Route::post('/check-dosen', [Cjadwal_2::class, 'checkDosen'])->name('checkDosen');
    Route::post('/check-kelas-smt', [Cjadwal_2::class, 'checkKelasSemester'])->name('checkKelasSemester');
    Route::post('/cek-slot-kosong', [Cjadwal_2::class, 'cekSlotKosong'])->name('cekSlotKosong');
    Route::delete('/bulk-delete', [Cjadwal_2::class, 'bulkDelete'])->name('bulkDelete');
    Route::get('/{id}/edit', [Cjadwal_2::class, 'edit'])->name('edit');
    Route::put('/{id}', [Cjadwal_2::class, 'update'])->name('update');
});



Route::delete('/jadwal/bulk-delete', [Cjadwal::class, 'bulkDelete'])->name('jadwal.bulkDelete');
Route::delete('/matkul/bulk-delete', [Cmatkul::class, 'bulkDelete'])->name('matkul.bulkDelete');
Route::delete('/prodi/bulk-delete', [Cprodi::class, 'bulkDelete'])->name('prodi.bulkDelete');
Route::delete('/ruangan/bulk-delete', [Cruangan::class, 'bulkDelete'])->name('ruangan.bulkDelete');
Route::delete('/dosen/bulk-delete', [Cdosen::class, 'bulkDelete'])->name('dosen.bulkDelete');


Route::resource('dosen', Cdosen::class);
Route::resource('ruangan', Cruangan::class);
Route::resource('matkul', Cmatkul::class);
Route::resource('jadwal', Cjadwal::class);
Route::resource('jadwal_2', Cjadwal_2::class);
Route::resource('prodi', Cprodi::class);
