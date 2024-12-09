{{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> --}}
@extends('layout.menu')
@section('konten')
    <h1>Halaman utama data kelas</h1>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah" style="margin-bottom: 20px">
        Tambah Data
    </button>
    <table class="table table-bordered table-hover display">
        <thead>
            <tr>
                <th class="tengah">
                    NO
                </th>
                <th class="tengah">
                    Kode Kelas
                </th>
                <th class="tengah">
                    Nama Kelas
                </th>
                <th class="tengah">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelas as $d)
                <tr>
                    <td class="tengah">{{ $loop->iteration }}</td>
                    <td class="tengah">{{ $d->id_kelas }}</td>
                    <td class="tengah">{{ $d->nama_kelas }}</td>
                    <td class="tengah">
                        <!-- Tombol Edit -->
                        <a href="javascript:void(0)" class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#editModal-{{ $d->id }}"><i class="fa fa-edit"></i></a>

                        <!-- Tombol Delete dengan SweetAlert -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $d->id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                        <!-- Form Delete -->
                        <form id="delete-form-{{ $d->id }}" action="{{ route('kelas.destroy', $d->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>

                </tr>
                @empty
                <tr>
                    <td colspan="10" class="tengah">Tidak ada data jadwal.</td>
                </tr>
    
            @endforelse
        </tbody>
    </table>

    <style>
        .tengah {
            text-align: center;
        }
    </style>
    @if (session('status'))
        <script>
            Swal.fire({
                title: "{{ session('status')['judul'] }}",
                text: "{{ session('status')['pesan'] }}",
                icon: "{{ session('status')['icon'] }}"
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + id).submit();
                        }
                    });
                });
            });
        });
    </script>


    <!-- Modal Tambah -->
    <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('kelas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            Kode Kelas:
                            <input type="text" name="id_kelas" id="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            Nama Kelas:
                            <input type="text" name="nama_kelas" id="" class="form-control" required>
                        </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($kelas as $d)
        <!-- Modal Edit -->
        <div class="modal fade" id="editModal-{{ $d->id }}" tabindex="-1" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form Edit -->
                        <form action="{{ route('kelas.update', $d->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                Kode Kelas:
                                <input type="text" name="id_kelas" class="form-control" value="{{ $d->id_kelas }}"
                                    required>
                            </div>
                            <div class="form-group">
                                Nama Kelas:
                                <input type="text" name="nama_kelas" class="form-control" value="{{ $d->nama_kelas }}"
                                    required>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#editModal-{{ old('id') }}').modal('show');
            @endif
        });
    </script>



    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#tambah').modal('show');
            @endif
        });
    </script>
@endsection
