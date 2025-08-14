@extends('layout.menu')
@section('konten')
    <h1 class="text-center my-3">Daftar Jadwal Mata Kuliah</h1>

    <!-- Tombol -->
    <div class="mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#tambahJadwal">Tambah Data</button>
        <button class="btn btn-success">Export to Excel</button>
    </div>

    <!-- Tabel -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Hari</th>
                <th>Tanggal</th>
                <th>Pukul</th>
                <th>Mata Kuliah</th>
                <th>SKS</th>
                <th>Ruang</th>
                <th>Dosen Pengampu</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($jadwalGrouped as $hari => $jamGroups)
                @php $hariRowspan = $jamGroups->count(); $firstHariRow = true; @endphp
                @foreach ($jamGroups as $jam => $items)
                    @php
                        $tanggalList = $items->pluck('tanggal')->unique()->sort();
                        $firstItem = $items->first();
                    @endphp
                    <tr>
                        @if ($firstHariRow)
                            <td rowspan="{{ $hariRowspan }}">{{ $no++ }}</td>
                            <td rowspan="{{ $hariRowspan }}">{{ $hari }}</td>
                            @php $firstHariRow = false; @endphp
                        @endif
                        <td>
                            @foreach ($tanggalList as $tgl)
                                {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}<br>
                            @endforeach
                        </td>
                        <td>{{ $firstItem->jam_mulai }} - {{ $firstItem->jam_selesai }}</td>
                        <td>{{ $firstItem->matkul->nama_matkul ?? '-' }}</td>
                        <td>{{ $firstItem->matkul->sks ?? '-' }}</td>
                        <td>{{ $firstItem->ruangan->nama_ruangan ?? '-' }}</td>
                        <td>{{ $firstItem->dosen->nama_dosen ?? '-' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <!-- Modal Tambah Jadwal -->
    <div class="modal fade" id="tambahJadwal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah Jadwal Berkala</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('jadwal_2.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Tanggal Pertama -->
                        <div class="form-group">
                            <label>Tanggal Pertama:</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                        </div>

                        <!-- Hari -->
                        <div class="form-group">
                            <label>Hari:</label>
                            <input type="text" name="hari" id="hari" class="form-control" readonly required>
                        </div>

                        <!-- Ruangan -->
                        <div class="form-group">
                            <label>Ruangan:</label>
                            <select name="ruangan_id" class="form-control" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($ruangan as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Prodi -->
                        <div class="form-group">
                            <label>Prodi:</label>
                            <select name="prodi_id" class="form-control" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach ($prodi as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>
                        <h6>Daftar Mata Kuliah Hari Ini</h6>
                        <div id="mataKuliahList">
                            <div class="mataKuliahItem border p-3 mb-3">
                                <!-- Mata Kuliah -->
                                <div class="form-group">
                                    <label>Mata Kuliah:</label>
                                    <select name="kode_matkul[]" class="form-control" required>
                                        <option value="">-- Pilih Mata Kuliah --</option>
                                        @foreach ($matkul as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_matkul }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Kelas -->
                                <div class="form-group">
                                    <label>Kelas:</label>
                                    <input type="text" name="kelas[]" class="form-control">
                                </div>

                                <!-- Jam Mulai -->
                                <div class="form-group">
                                    <label>Jam Mulai:</label>
                                    <input type="time" name="jam_mulai[]" class="form-control" required>
                                </div>

                                <!-- Jam Selesai -->
                                <div class="form-group">
                                    <label>Jam Selesai:</label>
                                    <input type="time" name="jam_selesai[]" class="form-control" required>
                                </div>

                                <!-- Dosen -->
                                <div class="form-group">
                                    <label>Dosen:</label>
                                    <select name="dosen_id[]" class="form-control" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dosen }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="button" class="btn btn-danger btn-sm removeItem">Hapus</button>
                            </div>
                        </div>

                        <button type="button" id="addMataKuliah" class="btn btn-success btn-sm">+ Tambah Mata Kuliah</button>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Buat jadwal otomatis untuk 8 tanggal?')">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('addMataKuliah').addEventListener('click', function() {
            let container = document.querySelector('#mataKuliahList');
            let newItem = container.firstElementChild.cloneNode(true);
            newItem.querySelectorAll('input, select').forEach(el => el.value = '');
            container.appendChild(newItem);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeItem')) {
                let container = document.querySelector('#mataKuliahList');
                if (container.children.length > 1) {
                    e.target.closest('.mataKuliahItem').remove();
                }
            }
        });

        document.getElementById('tanggal_mulai').addEventListener('change', function() {
            let tanggal = new Date(this.value);
            let hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            document.getElementById('hari').value = hariList[tanggal.getDay()];
        });
    </script>
@endsection
