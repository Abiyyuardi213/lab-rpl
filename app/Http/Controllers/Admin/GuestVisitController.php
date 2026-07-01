<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuestVisitController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.guest-visits.index', $this->indexPayload($request));
    }

    public function downloadTemplate()
    {
        $path = $this->createXlsxTemplateFile();

        return response()
            ->download(
                $path,
                'template-rekap-tamu-lab-rpl.xlsx',
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            )
            ->deleteFileAfterSend(true);
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        [$uploadedRows, $readErrors] = $this->readUploadedRows($request->file('file_excel'));

        [$previewRows, $previewErrors] = $this->buildPreviewRows($uploadedRows);
        $previewErrors = [...$readErrors, ...$previewErrors];

        session([
            'guest_visit_import_rows' => collect($previewRows)
                ->where('is_valid', true)
                ->pluck('data')
                ->values()
                ->all(),
        ]);

        return view('admin.guest-visits.index', [
            ...$this->indexPayload($request),
            'previewRows' => $previewRows,
            'previewErrors' => $previewErrors,
        ]);
    }

    public function confirmImport()
    {
        $rows = session('guest_visit_import_rows', []);

        if (empty($rows)) {
            return redirect()
                ->route('admin.guest-visits.index')
                ->with('error', 'Tidak ada data valid untuk diimport. Silakan upload dan review file terlebih dahulu.');
        }

        foreach ($rows as $row) {
            GuestVisit::create($row);
        }

        session()->forget('guest_visit_import_rows');

        return redirect()
            ->route('admin.guest-visits.index')
            ->with('import_success', count($rows) . ' data tamu berhasil diimport.');
    }

    public function cancelImport()
    {
        session()->forget('guest_visit_import_rows');

        return redirect()
            ->route('admin.guest-visits.index')
            ->with('info', 'Review import dibatalkan.');
    }

    private function indexPayload(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = GuestVisit::query();

        if (! empty($validated['start_date'])) {
            $query->whereDate('visit_date', '>=', $validated['start_date']);
        }

        if (! empty($validated['end_date'])) {
            $query->whereDate('visit_date', '<=', $validated['end_date']);
        }

        if (! empty($validated['q'])) {
            $search = trim($validated['q']);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('activity_purpose', 'like', "%{$search}%")
                    ->orWhere('lab_condition', 'like', "%{$search}%");
            });
        }

        $baseSummaryQuery = clone $query;

        $guestVisits = $query
            ->orderByDesc('visit_date')
            ->orderByDesc('started_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'records' => (clone $baseSummaryQuery)->count(),
            'guests' => (clone $baseSummaryQuery)->sum('guest_count'),
            'active' => (clone $baseSummaryQuery)->whereNull('ended_at')->count(),
            'completed' => (clone $baseSummaryQuery)->whereNotNull('ended_at')->count(),
        ];

        return compact('guestVisits', 'summary');
    }

    private function buildPreviewRows($rows): array
    {
        $rows = collect($rows)->filter(function ($row) {
            return collect($row)->filter(fn ($value) => filled($value))->isNotEmpty();
        })->values();

        if ($rows->isEmpty()) {
            return [[], ['File kosong atau tidak memiliki data.']];
        }

        $headers = collect($rows->first())
            ->map(fn ($header) => $this->normalizeHeader($header))
            ->values()
            ->all();

        $requiredHeaders = [
            'tanggal',
            'jam_mulai',
            'tujuan_aktivitas',
            'nama_tamu',
            'jumlah_tamu',
            'kondisi_lab',
        ];

        $missingHeaders = array_values(array_diff($requiredHeaders, $headers));

        if (! empty($missingHeaders)) {
            return [[], ['Kolom wajib tidak ditemukan: ' . implode(', ', $missingHeaders) . '. Pastikan memakai template terbaru.']];
        }

        $previewRows = [];
        $previewErrors = [];

        foreach ($rows->slice(1)->values() as $index => $row) {
            $rowNumber = $index + 2;
            $mapped = [];

            foreach ($headers as $columnIndex => $header) {
                $mapped[$header] = $row[$columnIndex] ?? null;
            }

            [$data, $errors] = $this->parseImportRow($mapped, $rowNumber);

            if (! empty($errors)) {
                $previewErrors[] = 'Baris ' . $rowNumber . ': ' . implode(' ', $errors);
            }

            $previewRows[] = [
                'row_number' => $rowNumber,
                'is_valid' => empty($errors),
                'errors' => $errors,
                'raw' => $mapped,
                'data' => $data,
            ];
        }

        if (empty($previewRows)) {
            $previewErrors[] = 'Tidak ada baris data setelah header.';
        }

        return [$previewRows, $previewErrors];
    }

    private function parseImportRow(array $row, int $rowNumber): array
    {
        $errors = [];

        $visitDate = $this->parseDateValue($row['tanggal'] ?? null);
        $startTime = $this->parseTimeValue($row['jam_mulai'] ?? null);
        $endTime = $this->parseTimeValue($row['jam_keluar'] ?? null);
        $guestName = trim((string) ($row['nama_tamu'] ?? ''));
        $activityPurpose = trim((string) ($row['tujuan_aktivitas'] ?? ''));
        $labCondition = trim((string) ($row['kondisi_lab'] ?? ''));
        $additionalNote = trim((string) ($row['keterangan_tambahan'] ?? ''));
        $guestCount = (int) ($row['jumlah_tamu'] ?? 0);

        if (! $visitDate) {
            $errors[] = 'Tanggal wajib format YYYY-MM-DD.';
        }

        if (! $startTime) {
            $errors[] = 'Jam mulai wajib format HH:MM.';
        }

        if ($guestName === '') {
            $errors[] = 'Nama tamu wajib diisi.';
        }

        if ($activityPurpose === '') {
            $errors[] = 'Tujuan aktivitas wajib diisi.';
        }

        if ($guestCount < 1 || $guestCount > 500) {
            $errors[] = 'Jumlah tamu harus 1-500.';
        }

        if ($labCondition === '') {
            $errors[] = 'Kondisi lab wajib diisi.';
        }

        $startedAt = $visitDate && $startTime
            ? Carbon::parse($visitDate->format('Y-m-d') . ' ' . $startTime)
            : null;

        $endedAt = $visitDate && $endTime
            ? Carbon::parse($visitDate->format('Y-m-d') . ' ' . $endTime)
            : null;

        if ($startedAt && $endedAt && $endedAt->lt($startedAt)) {
            $errors[] = 'Jam keluar tidak boleh lebih awal dari jam mulai.';
        }

        return [
            [
                'visit_date' => $visitDate?->toDateString(),
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'activity_purpose' => $activityPurpose,
                'guest_name' => $guestName,
                'guest_count' => $guestCount,
                'lab_condition' => $labCondition,
                'additional_note' => $additionalNote !== '' ? $additionalNote : null,
            ],
            $errors,
        ];
    }

    private function normalizeHeader($header): string
    {
        return str((string) $header)
            ->lower()
            ->trim()
            ->replace([' ', '-', '/', '.'], '_')
            ->replaceMatches('/_+/', '_')
            ->toString();
    }

    private function parseDateValue($value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        if (is_numeric($value)) {
            try {
                return Carbon::create(1899, 12, 30)->addDays((int) floor((float) $value))->startOfDay();
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date) {
                    return $date->startOfDay();
                }
            } catch (\Throwable) {
                //
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseTimeValue($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('H:i');
        }

        if (is_numeric($value)) {
            try {
                $seconds = (int) round((((float) $value) - floor((float) $value)) * 86400);
                return Carbon::createFromTime(0, 0, 0)->addSeconds($seconds)->format('H:i');
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['H:i', 'H:i:s', 'g:i A', 'g:i a'] as $format) {
            try {
                $time = Carbon::createFromFormat($format, $value);
                if ($time) {
                    return $time->format('H:i');
                }
            } catch (\Throwable) {
                //
            }
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable) {
            return null;
        }
    }

    private function readUploadedRows($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        return match ($extension) {
            'csv' => [$this->readCsvRows($path), []],
            'xlsx' => $this->readXlsxRows($path),
            'xls' => $this->readHtmlExcelRows($path),
            default => [[], ['Format file tidak didukung. Gunakan .xls, .xlsx, atau .csv.']],
        };
    }

    private function readCsvRows(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if (! $handle) {
            return $rows;
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function readHtmlExcelRows(string $path): array
    {
        $content = file_get_contents($path);

        if ($content === false || stripos($content, '<table') === false) {
            return [[], ['File .xls harus berasal dari template yang diunduh atau disimpan sebagai HTML Excel. Untuk file Excel modern, gunakan .xlsx.']];
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($content);
        libxml_clear_errors();

        $rows = [];
        foreach ($dom->getElementsByTagName('tr') as $tr) {
            $row = [];
            foreach ($tr->childNodes as $cell) {
                if (in_array(strtolower($cell->nodeName), ['td', 'th'], true)) {
                    $row[] = trim($cell->textContent);
                }
            }

            if (! empty($row)) {
                $rows[] = $row;
            }
        }

        return [$rows, []];
    }

    private function readXlsxRows(string $path): array
    {
        if (! class_exists(\ZipArchive::class)) {
            return [[], ['Ekstensi PHP ZipArchive belum aktif, sehingga .xlsx tidak bisa dibaca. Gunakan .csv atau .xls template.']];
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [[], ['File .xlsx tidak bisa dibuka.']];
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            return [[], ['Sheet pertama tidak ditemukan pada file .xlsx.']];
        }

        $xml = simplexml_load_string($sheetXml);
        if (! $xml) {
            return [[], ['Sheet .xlsx tidak bisa dibaca.']];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $rowNode) {
            $row = [];
            foreach ($rowNode->c as $cell) {
                $reference = (string) $cell['r'];
                $columnIndex = $this->excelColumnIndex(preg_replace('/\d+/', '', $reference));
                $type = (string) $cell['t'];
                $value = isset($cell->v) ? (string) $cell->v : '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                }

                $row[$columnIndex] = $value;
            }

            if (! empty($row)) {
                ksort($row);
                $rows[] = array_values($row);
            }
        }

        return [$rows, []];
    }

    private function readSharedStrings(\ZipArchive $zip): array
    {
        $content = $zip->getFromName('xl/sharedStrings.xml');
        if ($content === false) {
            return [];
        }

        $xml = simplexml_load_string($content);
        if (! $xml) {
            return [];
        }

        $strings = [];
        foreach ($xml->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;
                continue;
            }

            $text = '';
            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function excelColumnIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    private function createXlsxTemplateFile(): string
    {
        $tempDirectory = storage_path('app/temp');
        if (! is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0775, true);
        }

        $path = $tempDirectory . DIRECTORY_SEPARATOR . 'template-rekap-tamu-lab-rpl-' . uniqid() . '.xlsx';

        $headers = [
            'tanggal',
            'jam_mulai',
            'jam_keluar',
            'tujuan_aktivitas',
            'nama_tamu',
            'jumlah_tamu',
            'kondisi_lab',
            'keterangan_tambahan',
        ];

        $example = [
            now()->format('Y-m-d'),
            '08:00',
            '10:30',
            'Kunjungan laboratorium',
            'Nama Perwakilan Tamu',
            '5',
            'Baik',
            'Contoh baris, silakan hapus atau ganti sebelum import.',
        ];

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->xlsxStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxSheetXml($headers, $example));
        $zip->addFromString('docProps/core.xml', $this->xlsxCoreXml());
        $zip->addFromString('docProps/app.xml', $this->xlsxAppXml());
        $zip->close();

        return $path;
    }

    private function xlsxSheetXml(array $headers, array $example): string
    {
        $rows = [
            $this->xlsxRowXml(1, $headers, 1),
            $this->xlsxRowXml(2, $example, 0),
        ];

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<dimension ref="A1:H2"/>'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<cols><col min="1" max="3" width="16" customWidth="1"/><col min="4" max="5" width="30" customWidth="1"/><col min="6" max="7" width="16" customWidth="1"/><col min="8" max="8" width="46" customWidth="1"/></cols>'
            . '<sheetData>' . implode('', $rows) . '</sheetData>'
            . '</worksheet>';
    }

    private function xlsxRowXml(int $rowNumber, array $values, int $style): string
    {
        $cells = [];

        foreach (array_values($values) as $index => $value) {
            $cell = $this->xlsxColumnName($index + 1) . $rowNumber;
            $cells[] = '<c r="' . $cell . '" t="inlineStr" s="' . $style . '"><is><t>' . e((string) $value) . '</t></is></c>';
        }

        return '<row r="' . $rowNumber . '">' . implode('', $cells) . '</row>';
    }

    private function xlsxColumnName(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function xlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';
    }

    private function xlsxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function xlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Rekap Tamu" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function xlsxWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function xlsxStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1A4FA0"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function xlsxCoreXml(): string
    {
        $now = now()->toIso8601String();

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>Template Rekap Tamu Lab RPL</dc:title><dc:creator>Lab RPL ITATS</dc:creator>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '</cp:coreProperties>';
    }

    private function xlsxAppXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Lab RPL</Application></Properties>';
    }
}
