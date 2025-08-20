<?php

namespace App\Exports;

use App\Models\Mjadwal_2;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Jadwal2Export implements WithHeadings, WithEvents, ShouldAutoSize
{
    use Exportable;

    protected $smt;
    protected $prodi;

    public function __construct($smt = null, $prodi = null)
    {
        $this->smt = $smt;
        $this->prodi = $prodi;
    }

    public function headings(): array
    {
        return ['No', 'Hari', 'Tanggal', 'Jam', 'Prodi', 'Mata Kuliah', 'Kelas', 'SKS', 'Ruang', 'Dosen'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // 1) Ambil data tanpa duplikat
                $rows = Mjadwal_2::with(['matkul', 'dosen', 'ruangan', 'prodi'])
                    ->when($this->smt, fn($q) => $q->whereHas('matkul', fn($qq) => $qq->where('smt', $this->smt)))
                    ->when($this->prodi, fn($q) => $q->where('prodi_id', $this->prodi))
                    ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
                    ->orderBy('jam_mulai')
                    ->orderBy('tanggal')
                    ->get()
                    ->unique(
                        fn($r) => ($r->hari ?? '') . '|' .
                            ($r->tanggal ?? '') . '|' .
                            ($r->prodi_id ?? '') . '|' .
                            ($r->jam_mulai ?? '') . '|' .
                            ($r->jam_selesai ?? '') . '|' .
                            ($r->matkul_id ?? '') . '|' .
                            ($r->dosen_id ?? '') . '|' .
                            ($r->ruangan_id ?? '')
                    );

                $byHari = $rows->groupBy('hari');

                $row = 2;
                $no  = 1;

                foreach ($byHari as $hari => $hariGroup) {

                    $tanggalList = $hariGroup->pluck('tanggal')->unique()->sort()->values();
                    $tanggalText = $tanggalList
                        ->map(fn($tgl) => Carbon::parse($tgl)->translatedFormat('d F Y'))
                        ->implode("\n");

                    $matkuls = $hariGroup->groupBy(function ($r) {
                        return ($r->jam_mulai ?? '') . '-' . ($r->jam_selesai ?? '') . '|' .
                            ($r->matkul_id ?? '') . '|' .
                            ($r->dosen_id ?? '') . '|' .
                            ($r->ruangan_id ?? '');
                    })->map->first()
                        ->sortBy('jam_mulai')
                        ->values();

                    $startHariRow = $row;
                    $rowsCount    = $matkuls->count();

                    foreach ($matkuls as $mk) {
                        $jam = ($mk->jam_mulai ?? '') . '-' . ($mk->jam_selesai ?? '');

                        $sheet->setCellValue("D{$row}", $jam);
                        $sheet->setCellValue("E{$row}", $mk->prodi->nama_prodi ?? '-'); // ✅ ambil dari relasi
                        $sheet->setCellValue("F{$row}", $mk->matkul->nama_matkul ?? '-');
                        $sheet->setCellValue("G{$row}", $mk->kelas ?? '-');
                        $sheet->setCellValue("H{$row}", $mk->matkul->sks ?? '-');
                        $sheet->setCellValue("I{$row}", $mk->ruangan->nama_ruangan ?? '-');
                        $sheet->setCellValue("J{$row}", $mk->dosen->nama_dosen ?? '-');

                        $sheet->getStyle("D{$row}:J{$row}")
                            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                        $row++;
                    }


                    $endHariRow = $row - 1;

                    foreach (['A', 'B', 'C'] as $col) {
                        if ($endHariRow > $startHariRow) {
                            $sheet->mergeCells("{$col}{$startHariRow}:{$col}{$endHariRow}");
                        }
                    }

                    $sheet->setCellValue("A{$startHariRow}", $no++);
                    $sheet->setCellValue("B{$startHariRow}", $hari);
                    $sheet->setCellValue("C{$startHariRow}", $tanggalText);

                    $sheet->getStyle("C{$startHariRow}")
                        ->getAlignment()->setWrapText(true)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle("A{$startHariRow}:B{$startHariRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    $totalLines  = max(1, $tanggalList->count());
                    $totalHeight = 15 * $totalLines;
                    $perRow      = $rowsCount > 0 ? $totalHeight / $rowsCount : 15;

                    for ($r = $startHariRow; $r <= $endHariRow; $r++) {
                        $sheet->getDelegate()->getRowDimension($r)->setRowHeight($perRow);
                    }
                }

                // ==== Styling Tambahan ====
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Bold header + center
                $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E1F2'] // biru muda
                    ]
                ]);

                // Border semua sel
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                // Autofilter di header
                $sheet->setAutoFilter("A1:{$highestCol}1");
            }
        ];
    }
}
