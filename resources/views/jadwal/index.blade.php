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

    <h1 class="text-center my-3">Daftar Jadwal Mata Kuliah</h1>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah" style="margin-bottom: 20px">
        Tambah Data
    </button>
    <a href="{{ route('jadwal.export', ['smt' => request('smt'), 'prodi' => request('prodi')]) }}"
        class="btn btn-success mb-3">
        Export to Excel
    </a>





    <!-- Tempat Menampilkan Data Terakhir -->
    <div id="last-data-container" class="alert alert-info alert-dismissible fade show" style="display: none;">
        <button type="button" class="close" aria-label="Close" id="close-notification">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="">
            <strong>Data Terakhir Ditambahkan:</strong>
            <p class="m-0">Hari: <span id="last-hari"></span></p>
            <p class="m-0">Jam: <span id="last-jam-mulai"></span> - <span id="last-jam-selesai"></span></p>
            <p class="m-0">Kode Mata Kuliah: <span id="last-kode-matkul"></span></p>
            <p class="m-0">Prodi: <span id="last-nama-prodi"></span></p>
            <p class="m-0">Mata Kuliah: <span id="last-nama-matkul"></span></p>
            <p class="m-0">Semester: <span id="last-smt"></span></p>
            <p class="m-0">SKS: <span id="last-sks"></span></p>
            <p class="m-0">Dosen Pengampu: <span id="last-nama-dosen"></span></p>
            <p class="m-0">Kelas: <span id="last-kelas"></span></p>
            <p class="m-0">Ruangan: <span id="last-nama-ruangan"></span></p>
            <p class="m-0">Mode: <span id="last-mode-pembelajaran"></span></p>
        </div>
    </div>

    <div class="mb-3">
        <form method="GET" action="{{ route('jadwal.index') }}">
            <!-- Filter Semester -->
            <label for="smt">Filter Semester:</label>
            <select name="smt" id="smt" onchange="this.form.submit()">
                <option value="">Semua Semester</option>
                @foreach ($semesters as $smt)
                    <option value="{{ $smt }}" {{ request('smt') == $smt ? 'selected' : '' }}>
                        {{ $smt }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Prodi -->
            <label for="prodi">Filter Prodi:</label>
            <select name="prodi" id="prodi" onchange="this.form.submit()">
                <option value="">Semua Prodi</option>
                @foreach ($prodiList as $id => $nama_prodi)
                    <option value="{{ $id }}" {{ request('prodi') == $id ? 'selected' : '' }}>
                        {{ $nama_prodi }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <table id="table" class="table table-bordered table-hover display">
        <thead>
            <tr>
                <th class="tengah">NO</th>
                <th class="tengah">Hari</th>
                <th class="tengah">Jam</th>
                <th class="tengah">Kode Mata Kuliah</th>
                <th class="tengah">Prodi</th>
                <th class="tengah">Mata Kuliah</th>
                <th class="tengah">Semester</th>
                <th class="tengah">SKS</th>
                <th class="tengah">Dosen Pengampu</th>
                <th class="tengah">Kelas</th>
                <th class="tengah">Ruangan</th>
                <th class="tengah">Mode</th>
                <th class="tengah">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jadwal as $d)
                <tr>
                    <td class="tengah">{{ $loop->iteration }}</td>
                    <td class="tengah">{{ $d->hari }}</td>
                    <td class="tengah">{{ $d->jam_mulai }} - {{ $d->jam_selesai }}</td>
                    <td class="tengah">{{ $d->matkul->kode_matkul ?? '-' }}</td>
                    <td class="tengah">{{ $d->prodi->nama_prodi ?? '-' }}</td>
                    <td class="tengah">{{ $d->matkul->nama_matkul ?? '-' }}</td>
                    <td class="tengah">{{ $d->matkul->smt ?? '-' }}</td>
                    <td class="tengah">{{ $d->sks ?? '-' }}</td>
                    <td class="tengah">{{ $d->dosen->nama_dosen ?? '-' }}</td>
                    <td class="tengah">{{ $d->kelas ?? '-' }}</td>
                    <td class="tengah">{{ $d->ruangan->nama_ruangan ?? '-' }}</td>
                    <td class="tengah">{{ ucfirst($d->mode_pembelajaran) }}</td>
                    <td class="tengah">
                        <!-- Tombol Edit -->
                        <a href="javascript:void(0)" class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#editModal-{{ $d->id }}"><i class="fa fa-edit"></i></a>

                        <!-- Tombol Delete -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $d->id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                        <!-- Form Delete -->
                        <form id="delete-form-{{ $d->id }}" action="{{ route('jadwal.destroy', $d->id) }}"
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="tengah">Tidak ada data jadwal.</td>
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

                    <form action="{{ route('jadwal.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="debug" value="1">
                        <div class="form-group">
                            <label for="hari">Hari:</label>
                            <select name="hari" id="hari" class="form-control" required>
                                <option value="">-- Pilih Hari --</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jam_mulai">Jam Mulai:</label>
                            <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="jam_selesai">Jam Selesai:</label>
                            <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="availability">Ketersediaan Ruangan:</label>
                            <div class="d-flex align-items-center">
                                <span id="availability-status" class="ml-3 text-success" style="display: none;">Ruangan tersedia.</span>
                                <span id="no-availability-status" class="ml-3 text-danger" style="display: none;">Tidak ada ruangan kosong.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_ruangan">Ruangan:</label>
                            <select name="id_ruangan" id="id_ruangan-add" class="form-control" required>
                                <option value="">-- Pilih Ruangan --</option>
                            </select>
                        </div>  

                        <div class="form-group">
                            Mata Kuliah:
                            <select name="kode_matkul" class="form-control">
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach ($matkul as $matkuls)
                                    <option value="{{ $matkuls->id }}">{{ $matkuls->kode_matkul }} -
                                        {{ $matkuls->nama_matkul }} </option>
                                    {{-- - {{ $matkuls->prodi->nama_prodi }} --}}
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_prodi">Prodi:</label>
                            <select name="id_prodi" id="id_prodi" class="form-control" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach ($prodi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="form-group">
                            Semester:
                            <input type="text" name="smt" class="form-control" required>
                        </div> --}}
                        <div class="form-group">
                            SKS:
                            <input type="text" name="sks" class="form-control" required>
                        </div>
                        <div class="form-group">
                            Dosen:
                            <select name="id_dosen" class="form-control">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach ($dosen as $dosens)
                                    <option value="{{ $dosens->id }}">{{ $dosens->nama_dosen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            Kelas:
                            <input type="text" name="kelas" class="form-control">
                            {{-- <select name="id_kelas" class="form-control"> --}}
                            {{-- <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $kelasItem)
                                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                                @endforeach
                            </select> --}}
                        </div>
                        <div class="form-group">
                            Mode Pembelajaran:
                            <select name="mode_pembelajaran" class="form-control" required>
                                <option value="">-- Pilih Mode --</option>
                                <option value="luring">Luring</option>
                                <option value="daring">Daring</option>
                                <option value="luring/daring">Luring/Daring</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @foreach ($jadwal as $d)
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
                        <form action="{{ route('jadwal.update', $d->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                Hari:
                                <select name="hari" class="form-control" required>
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin" {{ $d->hari == 'Senin' ? 'selected' : '' }}>Senin</option>
                                    <option value="Selasa" {{ $d->hari == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                    <option value="Rabu" {{ $d->hari == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                    <option value="Kamis" {{ $d->hari == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                    <option value="Jumat" {{ $d->hari == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                    <option value="Sabtu" {{ $d->hari == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                </select>
                            </div>
                            <div class="form-group">
                                Jam Mulai:
                                <input type="time" name="jam_mulai" class="form-control" value="{{ $d->jam_mulai }}"
                                    required>
                            </div>
                            <div class="form-group">
                                Jam Selesai:
                                <input type="time" name="jam_selesai" class="form-control"
                                    value="{{ $d->jam_selesai }}" required>
                            </div>

                            <!-- Form Edit (Manual) -->
                            <div class="form-group">
                                <button type="button" id="check-room-btn-edit" class="btn btn-secondary mt-2">Cek Ketersediaan Ruangan</button>
                                <label for="availability">Ketersediaan Ruangan:</label>
                                <div class="d-flex align-items-center">
                                    <span id="availability-status-edit" class="ml-3 text-success" style="display: none;">Ruangan tersedia.</span>
                                    <span id="no-availability-status-edit" class="ml-3 text-danger" style="display: none;">Tidak ada ruangan kosong.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="id_ruangan">Ruangan:</label>
                                <select name="id_ruangan" id="id_ruangan-edit" class="form-control" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                </select>
                            </div>

                            <div class="form-group">
                                Mata Kuliah:
                                <select name="kode_matkul" class="form-control">
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach ($matkul as $matkuls)
                                        <option value="{{ $matkuls->id }}"
                                            {{ $d->kode_matkul == $matkuls->id ? 'selected' : '' }}>
                                            {{ $matkuls->kode_matkul }} - {{ $matkuls->nama_matkul }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_prodi">Prodi:</label>
                                <select name="id_prodi" id="id_prodi" class="form-control" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach ($prodi as $prodis)
                                        <option value="{{ $prodis->id }}"
                                            {{ $d->id_prodi == $prodis->id ? 'selected' : '' }}>
                                            {{ $prodis->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                SKS:
                                <input type="text" name="sks" class="form-control" value="{{ $d->sks }}"
                                    required>
                            </div>
                            <div class="form-group">
                                Dosen:
                                <select name="id_dosen" class="form-control">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosen as $dosens)
                                        <option value="{{ $dosens->id }}"
                                            {{ $d->id_dosen == $dosens->id ? 'selected' : '' }}>
                                            {{ $dosens->nama_dosen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                Kelas:
                                <input type="text" name="kelas" class="form-control" value="{{ $d->kelas }}">

                            </div>

                            <div class="form-group">
                                Mode Pembelajaran:
                                <select name="mode_pembelajaran" class="form-control" required>
                                    <option value="">-- Pilih Mode --</option>
                                    <option value="luring" {{ $d->mode_pembelajaran == 'luring' ? 'selected' : '' }}>
                                        Luring</option>
                                    <option value="daring" {{ $d->mode_pembelajaran == 'daring' ? 'selected' : '' }}>
                                        Daring</option>
                                    <option value="luring/daring"
                                        {{ $d->mode_pembelajaran == 'luring/daring' ? 'selected' : '' }}>Luring/Daring
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
            // Form Tambah (otomatis) selectors
            const hariSelectAdd = document.querySelector('select[name="hari"]');
            const jamMulaiInputAdd = document.querySelector('input[name="jam_mulai"]');
            const jamSelesaiInputAdd = document.querySelector('input[name="jam_selesai"]');
            const roomSelectAdd = document.getElementById('id_ruangan-add');
            const availabilityStatusAdd = document.getElementById('availability-status');
            const noAvailabilityStatusAdd = document.getElementById('no-availability-status');

            // Form Edit (manual) selectors
            const modal = document.querySelector('.modal'); // Pastikan modal sudah dimuat
            const hariSelectEdit = modal.querySelector('select[name="hari"]');
            const jamMulaiInputEdit = modal.querySelector('input[name="jam_mulai"]');
            const jamSelesaiInputEdit = modal.querySelector('input[name="jam_selesai"]');
            const roomSelectEdit = modal.querySelector('#id_ruangan-edit');
            const availabilityStatusEdit = modal.querySelector('#availability-status-edit');
            const noAvailabilityStatusEdit = modal.querySelector('#no-availability-status-edit');
            const checkRoomBtnEdit = modal.querySelector('#check-room-btn-edit');

            // Fungsi untuk mengecek ketersediaan ruangan (untuk kedua form)
            const checkAvailability = (roomSelect, availabilityStatus, noAvailabilityStatus, hariSelect,
                jamMulaiInput, jamSelesaiInput) => {
                const hari = hariSelect.value;
                const jamMulai = jamMulaiInput.value;
                const jamSelesai = jamSelesaiInput.value;

                // Validasi input
                if (!hari || !jamMulai || !jamSelesai) {
                    availabilityStatus.style.display = 'none';
                    noAvailabilityStatus.style.display = 'none';
                    roomSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                    return;
                }

                // Fetch ketersediaan ruangan
                fetch(`/check-available-rooms?hari=${hari}&jam_mulai=${jamMulai}&jam_selesai=${jamSelesai}`)
                    .then(response => response.json())
                    .then(data => {
                        availabilityStatus.style.display = 'none';
                        noAvailabilityStatus.style.display = 'none';

                        if (data.length > 0) {
                            roomSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                            let roomNames = [];
                            data.forEach(room => {
                                const option = document.createElement('option');
                                option.value = room.id;
                                option.textContent = room.nama_ruangan;
                                roomSelect.appendChild(option);
                                roomNames.push(room.nama_ruangan);
                            });

                            // Tampilkan status ruang tersedia
                            availabilityStatus.textContent = `Ruangan tersedia: ${roomNames.join(', ')}`;
                            availabilityStatus.style.display = 'block';
                        } else {
                            // Tidak ada ruang tersedia
                            roomSelect.innerHTML =
                                '<option value="">-- Tidak Ada Ruangan Kosong --</option>';
                            noAvailabilityStatus.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memeriksa ketersediaan ruangan.');
                    });
            };

            // Event listener untuk form tambah
            hariSelectAdd.addEventListener('change', () => checkAvailability(roomSelectAdd, availabilityStatusAdd,
                noAvailabilityStatusAdd, hariSelectAdd, jamMulaiInputAdd, jamSelesaiInputAdd));
            jamMulaiInputAdd.addEventListener('input', () => checkAvailability(roomSelectAdd, availabilityStatusAdd,
                noAvailabilityStatusAdd, hariSelectAdd, jamMulaiInputAdd, jamSelesaiInputAdd));
            jamSelesaiInputAdd.addEventListener('input', () => checkAvailability(roomSelectAdd,
                availabilityStatusAdd, noAvailabilityStatusAdd, hariSelectAdd, jamMulaiInputAdd,
                jamSelesaiInputAdd));

            // Event listener untuk form edit (manual) dengan tombol
            checkRoomBtnEdit.addEventListener('click', () => checkAvailability(roomSelectEdit,
                availabilityStatusEdit, noAvailabilityStatusEdit, hariSelectEdit, jamMulaiInputEdit,
                jamSelesaiInputEdit));
        });




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

        // Menampilkan Data Terakhir di Halaman
        document.addEventListener('DOMContentLoaded', function() {
            const lastData = JSON.parse(localStorage.getItem('lastAddedJadwal'));

            if (lastData) {
                console.log('Data terakhir ditemukan:', lastData);

                const lastDataContainer = document.getElementById('last-data-container');
                const lastHari = document.getElementById('last-hari');
                const lastJamMulai = document.getElementById('last-jam-mulai');
                const lastJamSelesai = document.getElementById('last-jam-selesai');
                const lastKodeMatkul = document.getElementById('last-kode-matkul');
                const lastNamaProdi = document.getElementById('last-nama-prodi');
                const lastNamaMatkul = document.getElementById('last-nama-matkul');
                const lastSemester = document.getElementById('last-smt');
                const lastSks = document.getElementById('last-sks');
                const lastNamaDosen = document.getElementById('last-nama-dosen');
                const lastKelas = document.getElementById('last-kelas');
                const lastNamaRuangan = document.getElementById('last-nama-ruangan');
                const lastMode = document.getElementById('last-mode-pembelajaran');

                // Set data ke elemen HTML
                lastHari.textContent = lastData.hari;
                lastJamMulai.textContent = lastData.jam_mulai;
                lastJamSelesai.textContent = lastData.jam_selesai;
                lastKodeMatkul.textContent = lastData.kode_matkul;
                lastNamaProdi.textContent = lastData.nama_prodi;
                lastNamaMatkul.textContent = lastData.nama_matkul;
                lastSemester.textContent = lastData.smt;
                lastSks.textContent = lastData.sks;
                lastNamaDosen.textContent = lastData.nama_dosen;
                lastKelas.textContent = lastData.kelas;
                lastNamaRuangan.textContent = lastData.nama_ruangan;
                lastMode.textContent = lastData.mode_pembelajaran;

                lastDataContainer.style.display = 'block';
            } else {
                console.log('Data tidak ditemukan di localStorage.');
            }

            // Tombol "X" untuk menutup pemberitahuan
            const closeNotificationButton = document.getElementById('close-notification');
            if (closeNotificationButton) {
                closeNotificationButton.addEventListener('click', function() {
                    const lastDataContainer = document.getElementById('last-data-container');
                    lastDataContainer.style.display = 'none';
                    localStorage.removeItem('lastAddedJadwal');
                });
            }
        });



        // Menyimpan Data Terakhir ke localStorage
        // Simpan data jadwal ke localStorage setelah berhasil menambah atau memperbarui data
        @if (session('status') && session()->has('last_data'))
            const lastJadwal = {
                hari: "{{ session('last_data')['hari'] }}",
                jam_mulai: "{{ session('last_data')['jam_mulai'] }}",
                jam_selesai: "{{ session('last_data')['jam_selesai'] }}",
                kode_matkul: "{{ session('last_data')['kode_matkul'] }}",
                nama_prodi: "{{ session('last_data')['nama_prodi'] }}",
                nama_matkul: "{{ session('last_data')['nama_matkul'] }}",
                smt: "{{ session('last_data')['smt'] }}",
                sks: "{{ session('last_data')['sks'] }}",
                nama_dosen: "{{ session('last_data')['nama_dosen'] }}",
                kelas: "{{ session('last_data')['kelas'] }}",
                nama_ruangan: "{{ session('last_data')['nama_ruangan'] }}",
                mode_pembelajaran: "{{ session('last_data')['mode_pembelajaran'] }}"
            };

            localStorage.setItem('lastAddedJadwal', JSON.stringify(lastJadwal));
        @endif
    </script>


@endsection
