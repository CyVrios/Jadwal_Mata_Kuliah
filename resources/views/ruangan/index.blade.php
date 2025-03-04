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
    <h1>Halaman utama data ruangan</h1>
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
        <p class="m-0">Nama Ruangan: <span id="last-nama-ruangan"></span></p>
    </div>

     <!-- Tombol Hapus -->
     <div class="mb-2">
        <button id="delete-selected" class="btn btn-danger btn-sm">Hapus Data Terpilih</button>
        <button id="delete-all" class="btn btn-warning btn-sm">Hapus Semua Data</button>
    </div>

    <table id="table" class="table table-bordered table-hover display">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th class="tengah">
                    NO
                </th>
                {{-- <th class="tengah">
                    Id Ruangan
                </th> --}}
                <th class="tengah">
                    Nama ruangan
                </th>
                <th class="tengah">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ruangan as $d)
                <tr>
                    <td class="text-center"><input type="checkbox" name="selected[]" value="{{ $d->id ?? '-' }}"></td>
                    <td class="tengah">{{ $loop->iteration }}</td>
                    {{-- <td class="tengah">{{ $d->id_ruangan }}</td> --}}
                    <td class="tengah">{{ $d->nama_ruangan }}</td>
                    <td class="tengah">
                        <!-- Tombol Edit -->
                        <a href="javascript:void(0)" class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#editModal-{{ $d->id }}"><i class="fa fa-edit"></i></a>
{{-- 
                        <!-- Tombol Delete dengan SweetAlert -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $d->id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                        <!-- Form Delete -->
                        <form id="delete-form-{{ $d->id }}" action="{{ route('ruangan.destroy', $d->id) }}"
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form> --}}
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="tengah">Tidak ada data ruangan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>



    <!-- Modal Tambah -->
    <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data ruangan</h5>
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

                    <form action="{{ route('ruangan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="form-group">
                            Id Ruangan:
                            <input type="text" name="id_ruangan" id="" class="form-control" required>
                        </div> --}}
                        <div class="form-group">
                            Nama ruangan:
                            <input type="text" name="nama_ruangan" id="" class="form-control" value="{{ old('nama_ruangan') }}" required>
                        </div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($ruangan as $d)
        <!-- Modal Edit -->
        <div class="modal fade" id="editModal-{{ $d->id }}" tabindex="-1" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data ruangan</h5>
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
                        <form action="{{ route('ruangan.update', $d->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            {{-- <div class="form-group">
                                Id Ruangan:
                                <input type="text" name="id_ruangan" class="form-control" value="{{ $d->id_ruangan }}"
                                    required>
                            </div> --}}
                            <div class="form-group">
                                Nama ruangan:
                                <input type="text" name="nama_ruangan" class="form-control"
                                    value="{{ $d->nama_ruangan }}" required>
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

    {{-- script untuk delete semua/pilih --}}
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('input[name="selected[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        document.getElementById('delete-selected').addEventListener('click', function() {
            let selectedIds = Array.from(document.querySelectorAll('input[name="selected[]"]:checked'))
                .map(checkbox => checkbox.value);

            if (selectedIds.length === 0) {
                alert("Pilih setidaknya satu data untuk dihapus.");
                return;
            }

            if (confirm("Apakah Anda yakin ingin menghapus data yang dipilih?")) {
                let form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route('ruangan.bulkDelete') }}";
                form.innerHTML = `
            @csrf
            @method('DELETE')
            <input type="hidden" name="selected" value="${selectedIds.join(',')}">
        `;
                document.body.appendChild(form);
                form.submit();
            }
        });

        document.getElementById('delete-all').addEventListener('click', function() {
            if (confirm("Apakah Anda yakin ingin menghapus SEMUA data?")) {
                let form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route('ruangan.bulkDelete') }}";
                form.innerHTML = `
            @csrf
            @method('DELETE')
            <input type="hidden" name="delete_all" value="1">
        `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>

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
            localStorage.removeItem('lastAddedRuangan'); // Hapus data dari localStorage
        });


        // Menampilkan Data Terakhir di Halaman
        document.addEventListener('DOMContentLoaded', function() {
            const lastData = JSON.parse(localStorage.getItem('lastAddedRuangan'));

            if (lastData) {
                // Tampilkan elemen HTML
                const lastDataContainer = document.getElementById('last-data-container');
                const lastNamaRuangan = document.getElementById('last-nama-ruangan');

                lastNamaRuangan.textContent = lastData.nama_ruangan;

                lastDataContainer.style.display = 'block';
            }
        });


        // Menyimpan Data Terakhir ke localStorage
        @if (session('status') && session()->has('last_data'))
            const lastData = {
                nama_ruangan: "{{ session('last_data')['nama_ruangan'] }}"
            };
            localStorage.setItem('lastAddedRuangan', JSON.stringify(lastData));
        @endif
    </script>
@endsection
