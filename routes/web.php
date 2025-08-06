<?php
use App\Http\Controllers\Cdosen;
use App\Http\Controllers\Cjadwal;
use App\Http\Controllers\Cmatkul;
use App\Http\Controllers\Cruangan;
use App\Http\Controllers\Cprodi;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cimport;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/matkul/import', [Cimport::class, 'import'])->name('matkul.import');
Route::get('/jadwal/export', [Cjadwal::class, 'export'])->name('jadwal.export');
Route::get('/check-available-rooms', [Cjadwal::class, 'checkAvailableRooms']);
Route::post('/jadwal/checkDosen', [Cjadwal::class, 'checkDosen'])->name('jadwal.checkDosen');
Route::post('/jadwal/check-kelas-semester', [Cjadwal::class, 'checkKelasSemester'])->name('jadwal.checkKelasSemester');



Route::delete('/jadwal/bulk-delete', [Cjadwal::class, 'bulkDelete'])->name('jadwal.bulkDelete');
Route::delete('/matkul/bulk-delete', [Cmatkul::class, 'bulkDelete'])->name('matkul.bulkDelete');
Route::delete('/prodi/bulk-delete', [Cprodi::class, 'bulkDelete'])->name('prodi.bulkDelete');
Route::delete('/ruangan/bulk-delete', [Cruangan::class, 'bulkDelete'])->name('ruangan.bulkDelete');
Route::delete('/dosen/bulk-delete', [Cdosen::class, 'bulkDelete'])->name('dosen.bulkDelete');

Route::resource('dosen', Cdosen::class);
Route::resource('ruangan', Cruangan::class);
Route::resource('matkul', Cmatkul::class);
Route::resource('jadwal', Cjadwal::class);
Route::resource('prodi', Cprodi::class);