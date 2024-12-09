<?php
use App\Exports\JadwalExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Cdosen;
use App\Http\Controllers\Cjadwal;
use App\Http\Controllers\Ckelas;
use App\Http\Controllers\Cmatkul;
use App\Http\Controllers\Cruangan;
use App\Http\Controllers\Cprodi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jadwal/export', [Cjadwal::class, 'export'])->name('jadwal.export');
Route::get('/check-available-rooms', [Cjadwal::class, 'checkAvailableRooms']);


Route::resource('dosen', Cdosen::class);
Route::resource('kelas', Ckelas::class);
Route::resource('ruangan', Cruangan::class);
Route::resource('matkul', Cmatkul::class);
Route::resource('jadwal', Cjadwal::class);
Route::resource('prodi', Cprodi::class);

