@extends('layout.menu')
@section('konten')
    <h1 class="text-center my-3">Jadwal Mata Kuliah 2</h1>

    {{-- Tombol --}}
    <div class="mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#tambahJadwal">Tambah Data</button>
        <button id="delete-all" class="btn btn-outline-danger">Hapus Semua</button>
        <a class="btn btn-success"
           href="{{ route('jadwal_2.export', ['smt' => request('smt'), 'prodi' => request('prodi')]) }}">
            Export to Excel
        </a>
    </div>

    {{-- Filter --}}
    {{-- <div class="">
        <form class="form-inline mb-3" method="GET" action="{{ route('jadwal_2.index') }}">
            <select name="smt" class="form-control mr-2">
                <option value="">-- Semua SMT --</option>
                @foreach ($semesters ?? [] as $s)
                    <option value="{{ $s }}" {{ isset($smtFilter) && $smtFilter == $s ? 'selected' : '' }}>
                        {{ $s }}</option>
                @endforeach
            </select>

            <select name="prodi" class="form-control mr-2">
                <option value="">-- Semua Prodi --</option>
                @foreach ($prodiList ?? [] as $id => $nama)
                    <option value="{{ $id }}" {{ isset($prodiFilter) && $prodiFilter == $id ? 'selected' : '' }}>
                        {{ $nama }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary mr-2" type="submit">Terapkan</button>
        </form>
    </div> --}}

    {{-- Tabel --}}
    <table id="table" class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Hari</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Pukul</th>
            <th class="text-center">Prodi</th>
            <th class="text-center">Mata Kuliah</th>
            <th class="text-center">Kelas</th>
            <th class="text-center">SKS</th>
            <th class="text-center">Ruang</th>
            <th class="text-center">Dosen Pengampu</th>
        </tr>
        </thead>
        <tbody>
        @php $no = 1; @endphp
        @foreach ($jadwalGrouped as $hari => $jamGroups)
            @php
                $hariRowspan = $jamGroups->count();
                $tanggalListHari = $jamGroups->flatten()->pluck('tanggal')->unique()->sort();
                $firstHariRow = true;
            @endphp
            @foreach ($jamGroups as $jam => $items)
                @php $firstItem = $items->first(); @endphp
                <tr>
                    @if ($firstHariRow)
                        <td class="text-center" rowspan="{{ $hariRowspan }}">{{ $no++ }}</td>
                        <td class="text-center" rowspan="{{ $hariRowspan }}">{{ $hari }}</td>
                        <td class="text-center" rowspan="{{ $hariRowspan }}">
                            @foreach ($tanggalListHari as $tgl)
                                {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}<br>
                            @endforeach
                        </td>
                        @php $firstHariRow = false; @endphp
                    @endif
                    <td class="text-center">{{ $firstItem->jam_mulai }} - {{ $firstItem->jam_selesai }}</td>
                    <td class="text-center">{{ $firstItem->prodi->nama_prodi ?? '-' }}</td>
                    <td class="text-center">{{ $firstItem->matkul->nama_matkul ?? '-' }}</td>
                    <td class="text-center">{{ $firstItem->kelas ?? '-' }}</td>
                    <td class="text-center">{{ $firstItem->matkul->sks ?? '-' }}</td>
                    <td class="text-center">{{ $firstItem->ruangan->nama_ruangan ?? '-' }}</td>
                    <td class="text-center">{{ $firstItem->dosen->nama_dosen ?? '-' }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>

    {{-- Modal Tambah Jadwal --}}
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
                        <div class="form-group">
                            <label>Tanggal Pertama:</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Hari:</label>
                            <input type="text" name="hari" id="hari" class="form-control" readonly required>
                        </div>

                        <div class="form-group">
                            <label>Ruangan:</label>
                            <select name="ruangan_id" id="ruangan_id-add" class="form-control" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($ruangan as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                            <small id="availability-status" class="form-text text-success" style="display:none;"></small>
                            <small id="no-availability-status" class="form-text text-danger" style="display:none;"></small>
                        </div>
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
                            <div class="mk-item border p-3 mb-3">
                                <div class="form-group">
                                    <label>Mata Kuliah:</label>
                                    <select name="kode_matkul[]" class="form-control select2-mk" required>
                                        <option value="">-- Pilih Mata Kuliah --</option>
                                        @foreach ($matkul as $m)
                                            <option value="{{ $m->id }}" data-sks="{{ $m->sks ?? 0 }}">
                                                {{ $m->kode_matkul }} - {{ $m->nama_matkul }} - Semester
                                                {{ $m->smt }} - sks {{ $m->sks }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Kelas:</label>
                                    <input type="text" name="kelas[]" class="form-control kelas-input">
                                </div>
                                <div class="form-group">
                                    <label>Jam Mulai:</label>
                                    <input type="time" name="jam_mulai[]" class="form-control jm">
                                </div>
                                <div class="form-group">
                                    <label>Jam Selesai:</label>
                                    <input type="time" name="jam_selesai[]" class="form-control js">
                                </div>
                                <div class="form-group">
                                    <label>Dosen:</label>
                                    <select name="dosen_id[]" class="form-control select2-dosen" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dosen }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted dosen-alert"></small>
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

    {{-- Script --}}
    <script>
        // isi otomatis hari
        document.getElementById('tanggal_mulai').addEventListener('change', function() {
            let tanggal = new Date(this.value);
            let hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            document.getElementById('hari').value = hariList[tanggal.getDay()];
        });

        // clone mk-item
        document.getElementById('addMataKuliah').addEventListener('click', function() {
            let container = document.querySelector('#mataKuliahList');
            let template = document.querySelector('.mk-item').outerHTML;
            container.insertAdjacentHTML('beforeend', template);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeItem')) {
                e.target.closest('.mk-item').remove();
            }
        });

        // auto jam selesai berdasarkan SKS
        document.addEventListener('change', function(e) {
            if (e.target.matches('.select2-mk, .jm')) {
                const item = e.target.closest('.mk-item');
                const mk = item.querySelector('.select2-mk');
                const jm = item.querySelector('.jm');
                const js = item.querySelector('.js');
                if (!mk || !jm || !js) return;
                const opt = mk.options[mk.selectedIndex];
                const sks = parseInt(opt.getAttribute('data-sks') || '0');
                if (sks && jm.value) {
                    const [H, M] = jm.value.split(':').map(Number);
                    let total = H * 60 + M + (sks * 45);
                    let HH = Math.floor(total / 60), MM = total % 60;
                    js.value = (HH < 10 ? '0' : '') + HH + ':' + (MM < 10 ? '0' : '') + MM;
                }
            }
        });

        // validasi dosen bentrok
        document.addEventListener('change', function(e) {
            if (e.target.matches('.select2-dosen, .jm, .js')) {
                const item = e.target.closest('.mk-item');
                if (!item) return;
                const hari = document.getElementById('hari').value;
                const tanggal = document.getElementById('tanggal_mulai').value;
                const jm = item.querySelector('.jm')?.value || '';
                const js = item.querySelector('.js')?.value || '';
                const dosen = item.querySelector('.select2-dosen')?.value || '';
                if (hari && tanggal && jm && js && dosen) {
                    fetch(`{{ route('jadwal_2.checkDosen') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ hari, tanggal, jam_mulai: jm, jam_selesai: js, dosen_id: dosen })
                    }).then(r => r.json()).then(d => {
                        const el = item.querySelector('.dosen-alert');
                        if (!el) return;
                    });
                }
            }
        });

        // delete semua
        document.getElementById('delete-all').addEventListener('click', function() {
            if (confirm('Hapus SEMUA data jadwal 2?')) {
                const f = document.createElement('form');
                f.method = 'POST';
                f.action = '{{ route('jadwal_2.bulkDelete') }}';
                f.innerHTML = `@csrf @method('DELETE') <input type="hidden" name="delete_all" value="1">`;
                document.body.appendChild(f);
                f.submit();
            }
        });
    </script>
@endsection
