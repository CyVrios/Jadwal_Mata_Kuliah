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
            <p class="m-0">Mata Kuliah: <span id="last-nama-matkul"></span></p>
            <p class="m-0">Hari: <span id="last-hari"></span></p>
            <p class="m-0">Jam: <span id="last-jam-mulai"></span> - <span id="last-jam-selesai"></span></p>
            <p class="m-0">Kode Mata Kuliah: <span id="last-kode-matkul"></span></p>
            <p class="m-0">Prodi: <span id="last-nama-prodi"></span></p>
            <p class="m-0">Semester: <span id="last-smt"></span></p>
            <p class="m-0">SKS: <span id="last-sks"></span></p>
            <p class="m-0">Dosen Pengampu: <span id="last-nama-dosen"></span></p>
            <p class="m-0">Kelas: <span id="last-kelas"></span></p>
            <p class="m-0">Ruangan: <span id="last-nama-ruangan"></span></p>
        </div>
    </div>

    <div class="mb-3">
        <form method="GET" action="{{ route('jadwal.index') }}" class="d-flex gap-2">
            <!-- Filter Semester -->
            <div>
                <label for="smt" class="form-label">Filter Semester:</label>
                <select name="smt" id="smt" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Semester</option>
                    @foreach ($semesters as $smt)
                        <option value="{{ $smt }}" {{ request('smt') == $smt ? 'selected' : '' }}>
                            {{ $smt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Prodi -->
            <div>
                <label for="prodi" class="form-label">Filter Prodi:</label>
                <select name="prodi" id="prodi" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Prodi</option>
                    @foreach ($prodiList as $id => $nama_prodi)
                        <option value="{{ $id }}" {{ request('prodi') == $id ? 'selected' : '' }}>
                            {{ $nama_prodi }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Tombol Hapus -->
    <div class="mb-2">
        <button id="delete-selected" class="btn btn-danger btn-sm">Hapus Data Terpilih</button>
        <button id="delete-all" class="btn btn-warning btn-sm">Hapus Semua Data</button>
    </div>

    <div class="table-responsive-lg">
        <table id="table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th class="text-center">NO</th>
                    <th class="text-center">Hari</th>
                    <th class="text-center">Jam</th>
                    <th class="text-center">Prodi</th>
                    <th class="text-center">Kode Mata Kuliah</th>
                    <th class="text-center">Mata Kuliah</th>
                    <th class="text-center">Semester</th>
                    <th class="text-center">SKS</th>
                    <th class="text-center">Dosen Pengampu</th>
                    <th class="text-center">Kelas</th>
                    <th class="text-center">Ruangan</th>
                    {{-- <th class="text-center">Mode</th> --}}
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jadwal as $d)
                    <tr>
                        <td class="text-center"><input type="checkbox" name="selected[]" value="{{ $d->id ?? '-' }}"></td>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $d->hari }}</td>
                        <td class="text-center">{{ $d->jam_mulai }} - {{ $d->jam_selesai }}</td>
                        <td class="text-center">{{ $d->prodi->nama_prodi ?? '-' }}</td>
                        <td class="text-center">{{ $d->matkul->kode_matkul ?? '-' }}</td>
                        <td class="text-center">{{ $d->matkul->nama_matkul ?? '-' }}</td>
                        <td class="text-center">{{ $d->matkul->smt ?? '-' }}</td>
                        <td class="text-center">{{ $d->matkul->sks ?? '-' }}</td>
                        <td class="text-center">{{ $d->dosen->nama_dosen ?? '-' }}</td>
                        <td class="text-center">{{ $d->kelas ?? '-' }}</td>
                        <td class="text-center">{{ $d->ruangan->nama_ruangan ?? '-' }}</td>
                        {{-- <td class="text-center">{{ ucfirst($d->mode_pembelajaran) }}</td> --}}
                        <td class="text-center">
                            <!-- Tombol Edit -->
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#editModal-{{ $d->id }}"><i class="fa fa-edit"></i></a>

                            {{-- <!-- Tombol Delete -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $d->id }}">
                            <i class="fa fa-trash"></i>
                        </button>
    
                        <!-- Form Delete -->
                        <form id="delete-form-{{ $d->id }}" action="{{ route('jadwal.destroy', $d->id) }}"
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>



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
                            <label for="ruangan_id">Mata Kuliah:</label>
                            <select name="kode_matkul" id="kode_matkul" class="form-control" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach ($matkul as $matkuls)
                                    <option value="{{ $matkuls->id }}" data-sks="{{ $matkuls->sks }}">
                                        {{ $matkuls->kode_matkul }} - {{ $matkuls->nama_matkul }} - {{ $matkuls->sks }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hari">Hari:</label>
                            <select name="hari" id="hari" class="form-control" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>
                                        {{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jam_mulai">Jam Mulai:</label>
                            <input type="time" name="jam_mulai" id="jam_mulai" class="form-control"
                                value="{{ old('jam_mulai') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="jam_selesai">Jam Selesai:</label>
                            <input type="time" name="jam_selesai" id="jam_selesai" class="form-control"
                                value="{{ old('jam_selesai') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="availability">Ketersediaan Ruangan:</label>
                            <div class="d-flex align-items-center">
                                <span id="availability-status" class="ml-3 text-success" style="display: none;">Ruangan
                                    tersedia.</span>
                                <span id="no-availability-status" class="ml-3 text-danger" style="display: none;">Tidak
                                    ada ruangan kosong.</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ruangan_id">Ruangan:</label>
                            <select name="ruangan_id" id="ruangan_id-add" class="form-control" required>
                                <option value="">-- Pilih Ruangan --</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="prodi_id">Prodi:</label>
                            <select name="prodi_id" id="prodi_id" class="form-control" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach ($prodi as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('prodi_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="form-group">
                            SKS:
                            <input type="text" name="sks" class="form-control" value="{{ old('sks') }}"
                                required>
                        </div> --}}

                        <p id="dosen-alert" style="display:none;"></p>

                        <div class="form-group">
                            Dosen:
                            <select name="dosen_id" class="form-control">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach ($dosen as $dosens)
                                    <option value="{{ $dosens->id }}"
                                        {{ old('dosen_id') == $dosens->id ? 'selected' : '' }}>
                                        {{ $dosens->nama_dosen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            Kelas:
                            <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}">
                        </div>

                        {{-- <div class="form-group">
                            Mode Pembelajaran:
                            <select name="mode_pembelajaran" class="form-control" required>
                                <option value="">-- Pilih Mode --</option>
                                <option value="luring" {{ old('mode_pembelajaran') == 'luring' ? 'selected' : '' }}>Luring
                                </option>
                                <option value="daring" {{ old('mode_pembelajaran') == 'daring' ? 'selected' : '' }}>Daring
                                </option>
                                <option value="luring/daring"
                                    {{ old('mode_pembelajaran') == 'luring/daring' ? 'selected' : '' }}>Luring/Daring
                                </option>
                            </select>
                        </div> --}}

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>


                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    @foreach ($jadwal as $d)
        <div class="modal fade" id="editModal-{{ $d->id }}" tabindex="-1" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Mata Kuliah</h5>
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
                                Mata Kuliah:
                                <select name="kode_matkul" class="form-control">
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach ($matkul as $matkuls)
                                        <option value="{{ $matkuls->id }}"
                                            {{ $d->kode_matkul == $matkuls->id ? 'selected' : '' }}>
                                            {{ $matkuls->kode_matkul }} - {{ $matkuls->nama_matkul }} -
                                            {{ $matkuls->sks }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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


                            {{-- <div class="form-group">
                            <label for="availability">Ketersediaan Ruangan:</label>
                            <div class="d-flex align-items-center">
                                <span id="availability-status" class="ml-3 text-success" style="display: none;">Ruangan tersedia.</span>
                                <span id="no-availability-status" class="ml-3 text-danger" style="display: none;">Tidak ada ruangan kosong.</span>
                            </div>
                        </div> --}}
                            <div class="form-group">
                                <label for="ruangan_id">Ruangan:</label>
                                <select name="ruangan_id" id="ruangan_id-add" class="form-control" required>
                                    @foreach ($ruangan as $ruang)
                                        <option value="{{ $ruang->id }}"
                                            {{ $d->ruangan_id == $ruang->id ? 'selected' : '' }}>
                                            {{ $ruang->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Form Edit (Manual) -->
                            {{-- <div class="form-group">
                            <button type="button" id="check-room-btn-edit" class="btn btn-secondary mt-2">Cek Ketersediaan Ruangan</button>
                            <label for="availability">Ketersediaan Ruangan:</label>
                            <div class="d-flex align-items-center">
                                <span id="availability-status-edit" class="ml-3 text-success" style="display: none;">Ruangan tersedia.</span>
                                <span id="no-availability-status-edit" class="ml-3 text-danger" style="display: none;">Tidak ada ruangan kosong.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ruangan_id">Ruangan:</label>
                            <select name="ruangan_id" id="ruangan_id-edit" class="form-control" required>
                                <option value="">-- Pilih Ruangan --</option>
                            </select>
                        </div> --}}

                            <div class="form-group">
                                <label for="prodi_id">Prodi:</label>
                                <select name="prodi_id" id="prodi_id" class="form-control" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach ($prodi as $prodis)
                                        <option value="{{ $prodis->id }}"
                                            {{ $d->prodi_id == $prodis->id ? 'selected' : '' }}>
                                            {{ $prodis->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                Dosen:
                                <select name="dosen_id" class="form-control">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosen as $dosens)
                                        <option value="{{ $dosens->id }}"
                                            {{ $d->dosen_id == $dosens->id ? 'selected' : '' }}>
                                            {{ $dosens->nama_dosen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                Kelas:
                                <input type="text" name="kelas" class="form-control" value="{{ $d->kelas }}">

                            </div>

                            {{-- <div class="form-group">
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
                    </div> --}}
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
                form.action = "{{ route('jadwal.bulkDelete') }}";
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
                form.action = "{{ route('jadwal.bulkDelete') }}";
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


    {{-- script  check dosen
    <!-- Tambahkan validasi ketersediaan dosen -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let elements = ["jam_mulai", "jam_selesai", "hari", "dosen_id"];
            elements.forEach(id => {
                document.getElementById(id).addEventListener("change", checkDosenAvailability);
            });

            function checkDosenAvailability() {
                let hari = document.getElementById("hari").value;
                let jamMulai = document.getElementById("jam_mulai").value;
                let jamSelesai = document.getElementById("jam_selesai").value;
                let dosenId = document.getElementById("dosen_id").value;

                if (!hari || !jamMulai || !jamSelesai || !dosenId) {
                    return;
                }

                fetch("{{ route('jadwal.checkDosen') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            hari: hari,
                            jam_mulai: jamMulai,
                            jam_selesai: jamSelesai,
                            dosen_id: dosenId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        let alertBox = document.getElementById("dosen-alert");
                        alertBox.style.display = "block";
                        if (data.available) {
                            alertBox.style.color = "green";
                            alertBox.innerText = "✅ Dosen tersedia.";
                        } else {
                            alertBox.style.color = "red";
                            alertBox.innerText = "❌ Dosen sudah memiliki jadwal pada waktu tersebut.";
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        });
    </script> --}}

    {{-- check dosen --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("jam_mulai").addEventListener("change", checkDosenAvailability);
            document.getElementById("jam_selesai").addEventListener("change", checkDosenAvailability);
            document.getElementById("hari").addEventListener("change", checkDosenAvailability);
            document.getElementById("dosen_id").addEventListener("change", checkDosenAvailability);

            function checkDosenAvailability() {
                let hari = document.getElementById("hari").value;
                let jamMulai = document.getElementById("jam_mulai").value;
                let jamSelesai = document.getElementById("jam_selesai").value;
                let dosenId = document.getElementById("dosen_id").value;

                if (!hari || !jamMulai || !jamSelesai || !dosenId) {
                    return;
                }

                fetch("{{ route('jadwal.checkDosen') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            hari: hari,
                            jam_mulai: jamMulai,
                            jam_selesai: jamSelesai,
                            dosen_id: dosenId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        let alertBox = document.getElementById("dosen-alert");
                        if (data.available) {
                            alertBox.style.color = "green";
                            alertBox.innerText = "Dosen tersedia.";
                        } else {
                            alertBox.style.color = "red";
                            alertBox.innerText = "Dosen sudah memiliki jadwal pada waktu tersebut.";
                        }
                    });
            }
        });
    </script>

    {{-- script check ruangan --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const matkulSelect = document.getElementById("kode_matkul");
            const jamMulaiInput = document.getElementById("jam_mulai");
            const jamSelesaiInput = document.getElementById("jam_selesai");

            const hariSelect = document.querySelector('select[name="hari"]');
            const roomSelect = document.getElementById('ruangan_id-add');
            const availabilityStatus = document.getElementById('availability-status');
            const noAvailabilityStatus = document.getElementById('no-availability-status');

            function checkAvailability() {
                const hari = hariSelect.value;
                const jamMulai = jamMulaiInput.value;
                const jamSelesai = jamSelesaiInput.value;

                if (!hari || !jamMulai || !jamSelesai) {
                    availabilityStatus.style.display = 'none';
                    noAvailabilityStatus.style.display = 'none';
                    roomSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                    return;
                }

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
                            availabilityStatus.textContent = `Ruangan tersedia: ${roomNames.join(', ')}`;
                            availabilityStatus.style.display = 'block';
                        } else {
                            roomSelect.innerHTML = '<option value="">-- Tidak Ada Ruangan Kosong --</option>';
                            noAvailabilityStatus.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memeriksa ketersediaan ruangan.');
                    });
            }

            matkulSelect.addEventListener("change", function() {
                if (matkulSelect.value) {
                    jamMulaiInput.removeAttribute("disabled");
                } else {
                    jamMulaiInput.setAttribute("disabled", "true");
                    jamSelesaiInput.value = "";
                }
            });

            jamMulaiInput.addEventListener("change", function() {
                const selectedOption = matkulSelect.options[matkulSelect.selectedIndex];
                const sks = parseInt(selectedOption.getAttribute("data-sks"));

                if (!sks || !jamMulaiInput.value) return;

                const menitTambahan = sks * 45;

                const [jam, menit] = jamMulaiInput.value.split(":").map(Number);
                let totalMenit = jam * 60 + menit + menitTambahan;

                let jamSelesai = Math.floor(totalMenit / 60);
                let menitSelesai = totalMenit % 60;

                jamSelesaiInput.value =
                    (jamSelesai < 10 ? "0" : "") + jamSelesai + ":" + (menitSelesai < 10 ? "0" : "") +
                    menitSelesai;

                // Panggil checkAvailability setelah jam selesai diupdate otomatis
                checkAvailability();
            });

            [hariSelect, jamMulaiInput, jamSelesaiInput].forEach(element => {
                element.addEventListener('input', checkAvailability);
            });
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
            };

            localStorage.setItem('lastAddedJadwal', JSON.stringify(lastJadwal));
        @endif
    </script>


@endsection
