@extends('layout.menu')
	@section('konten')
{{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> --}}
<div>
<h1>Halaman tambah data</h1>
<form action="{{ route('matkul.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
    Nama Matkul: 
    <input type="text" name="nis" id="" class="form-control" required>
</div>
<div class="form-group">
    Nama Siswa:
    <input type="text" name="nama" id="" class="form-control" required>
</div>
<div class="form-group">
    Alamat Siswa:
    <textarea name="alamat" id="" cols="30" rows="10" class="form-control"></textarea>
</div>
<div class="from-group">
    Tanggal Lahir Siswa:
    <input type="date" name="tanggal_lahir" id="" class="form-control" required>
</div>
<div class="form-group">
    Jenis Kelamin Siswa:
    <select class="form-control form-select" name="jk" id="" required>
        <option selected="">~Pilih Jenis Kelamin~</option>
        <option value="Laki-laki">Laki-laki</option>
        <option value="Perempuan">Perempuan</option>
    </select>
</div>
<div class="form-group">
    Prodi Siswa:
    <select class="form-control form-select" aria-label="Default select example" name="prodi" id="" required>
        <option selected="">~Pilih Prodi~</option>
        <option value="TJAT">Teknik Jaringan Akses Telekomunikasi</option>
        <option value="TKJ">Teknik Komputer Jaringan</option>
        <option value="RPL">Rekayasa Perangkat Lunak</option>
        <option value="MM">Multimedia</option>
        <option value="Animasi">Animasi</option>
    </select>
</div>
    <button type="submit" class="btn btn-primary">Simpan Data</button>
    <a href="{{ route('siswa.index')}}" class="btn btn-warning">Kembali</a>
</form> 
</div>
@endsection
