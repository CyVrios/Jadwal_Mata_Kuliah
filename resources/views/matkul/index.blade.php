{{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> --}}
@extends('layout.menu')
@section('konten')

    <style>
        .tengah {
            text-align: center;
        }

        table.dataTable {
            width: 100% !important;
            /* Atur lebar tabel secara eksplisit */
            overflow: visible !important;
            /* Pastikan tidak ada overflow */
        }

        /* Hilangkan scroll */
        .dataTables_wrapper {
            overflow: visible !important;
        }
    </style>

    <h1>Halaman utama data mata kuliah</h1>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah" style="margin-bottom: 20px">
        Tambah Data
    </button>

    <!-- Tempat Menampilkan Data Terakhir -->
    <div id="last-data-container" class="alert alert-info alert-dismissible fade show" style="display: none;">
        <button type="button" class="close" aria-label="Close" id="close-notification">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Data Terakhir Ditambahkan:</strong>
        <p class="m-0">Kode Mata Kuliah: <span id="last-kode-matkul"></span></p>
        <p class="m-0">Nama Mata Kuliah: <span id="last-nama-matkul"></span></p>
        <p class="m-0">Semester: <span id="last-smt"></span></p>
        {{-- <p class="m-0">Nama Prodi: <span id="last-nama-prodi"></span></p> --}}
    </div>

    <table id="table" class="table table-bordered table-hover display">
        <thead>
            <tr>
                <th class="tengah">
                    NO
                </th>
                <th class="tengah">
                    Kode Mata Kuliah
                </th>
                <th class="tengah">
                    Nama Mata Kuliah
                </th>
                <th class="tengah">
                    Semester
                </th>
                {{-- <th class="tengah">
                    Prodi
                </th> --}}
                <th class="tengah">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($matkul as $d)
                <tr>
                    <td class="tengah">{{ $loop->iteration }}</td>
                    <td class="tengah">{{ $d->kode_matkul }}</td>
                    <td class="tengah">{{ $d->nama_matkul }}</td>
                    <td class="tengah">{{ $d->smt }}</td>
                    {{-- <td class="tengah">{{ $d->prodi->nama_prodi ?? '-' }}</td> --}}
                    <td class="tengah">
                        <!-- Tombol Edit -->
                        <a href="javascript:void(0)" class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#editModal-{{ $d->id }}"><i class="fa fa-edit"></i></a>

                        <!-- Tombol Delete dengan SweetAlert -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $d->id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                        <!-- Form Delete -->
                        <form id="delete-form-{{ $d->id }}" action="{{ route('matkul.destroy', $d->id) }}"
                            method="POST" style="display: none;">
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

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data Mata Kuliah</h5>
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

                    <form action="{{ route('matkul.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            Kode Mata Kuliah:
                            <input type="text" name="kode_matkul" id="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            Nama Mata Kuliah:
                            <input type="text" name="nama_matkul" id="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            Semester:
                            <input type="text" name="smt" id="" class="form-control" required>
                        </div>
                        {{-- <div class="form-group">
                            Prodi:
                            <select name="id_prodi" class="form-control">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach ($prodi as $prodiItem)
                                    <option value="{{ $prodiItem->id }}">{{ $prodiItem->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($matkul as $d)
        <!-- Modal Edit -->
        <div class="modal fade" id="editModal-{{ $d->id }}" tabindex="-1" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Mata Kuliah</h5>
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
                        <form action="{{ route('matkul.update', $d->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                Kode Mata kuliah:
                                <input type="text" name="kode_matkul" class="form-control"
                                    value="{{ $d->kode_matkul }}" required>
                            </div>
                            <div class="form-group">
                                Nama matkul:
                                <input type="text" name="nama_matkul" class="form-control"
                                    value="{{ $d->nama_matkul }}" required>
                            </div>
                            <div class="form-group">
                                Semester:
                                <input type="text" name="smt" class="form-control"
                                    value="{{ $d->smt }}" required>
                            </div>
                            {{-- <div class="form-group">
                                Prodi:
                                <select name="id_prodi" class="form-control">
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach ($prodi as $prodis)
                                        <option value="{{ $prodis->id }}"
                                            {{ $d->id_prodi == $prodis->id ? 'selected' : '' }}>
                                            {{ $prodis->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

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
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
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

        // Menyembunyikan Pemberitahuan Saat Tombol "X" Ditekan
        const closeNotificationButton = document.getElementById('close-notification');
        closeNotificationButton.addEventListener('click', function() {
            const lastDataContainer = document.getElementById('last-data-container');
            lastDataContainer.style.display = 'none';
            localStorage.removeItem('lastAddedMatkul'); // Hapus data dari localStorage
        });


        // Menampilkan Data Terakhir di Halaman
        document.addEventListener('DOMContentLoaded', function() {
            const lastData = JSON.parse(localStorage.getItem('lastAddedMatkul'));

            if (lastData) {
                // Tampilkan elemen HTML
                const lastDataContainer = document.getElementById('last-data-container');
                const lastKodeMatkul = document.getElementById('last-kode-matkul');
                const lastNamaMatkul = document.getElementById('last-nama-matkul');
                const lastSmt = document.getElementById('last-smt');

                lastKodeMatkul.textContent = lastData.kode_matkul;
                lastNamaMatkul.textContent = lastData.nama_matkul;
                lastSmt.textContent = lastData.smt;

                lastDataContainer.style.display = 'block';
            }
        });


        // Menyimpan Data Terakhir ke localStorage
        @if (session('last_data'))
            const lastData = {
                kode_matkul: "{{ session('last_data')['kode_matkul'] }}",
                nama_matkul: "{{ session('last_data')['nama_matkul'] }}",
                smt: "{{ session('last_data')['smt'] }}"
            };
            localStorage.setItem('lastAddedMatkul', JSON.stringify(lastData));
        @endif
    </script>



@endsection