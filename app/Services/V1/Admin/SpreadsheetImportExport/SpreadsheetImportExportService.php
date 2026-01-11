<?php

namespace App\Services\V1\Admin\SpreadsheetImportExport;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SpreadsheetImportExportService
{
    public function exportExcel(object $exportable, string $filename)
    {
        return Excel::download($exportable, $filename.'.xlsx');
    }

    public function exportCsv(object $exportable, string $filename)
    {
        return Excel::download($exportable, $filename.'.csv');
    }

    public function import(object $importable, UploadedFile|string $file): string
    {
        try {
            $path = Storage::disk('local')->putFile('import', $file);

            Excel::queueImport($importable, Storage::disk('local')->path($path));

            Log::info('Import queued successfully', [
                'importable' => get_class($importable),
                'file_path' => $path,
            ]);

            return $path;

        } catch (\Throwable $e) {
            Log::error('Failed to queue import', [
                'importable' => get_class($importable),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up stored file if import queueing failed
            if (isset($path) && $file instanceof UploadedFile) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        }
    }
}
