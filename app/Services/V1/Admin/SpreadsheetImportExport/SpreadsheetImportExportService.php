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

     public function import(object $importable, UploadedFile|string $file): array
    {
        $path = Storage::disk('local')->putFile('imports', $file);

        Storage::disk('local')->makeDirectory('imports/errors');

        try {
            Excel::import(
                $importable,
                Storage::disk('local')->path($path)
            );


            $stats = method_exists($importable, 'getStats') 
                ? $importable->getStats() 
                : [];

            Log::info('Import completed successfully', [
                'importable' => get_class($importable),
                'file_path' => $path,
                'stats' => $stats,
            ]);

   

            return [
                'success' => true,
                'path' => $path,
                'stats' => $stats,
            ];

        } catch (\Throwable $e) {
            Log::error('Failed to import spreadsheet', [
                'importable' => get_class($importable),
                'file_path' => $path,
                'error' => $e->getMessage(),
                'trace' => app()->isLocal() ? $e->getTraceAsString() : null,
            ]);

        
            $errorPath = 'imports/errors/' . basename($path);
            Storage::disk('local')->move($path, $errorPath);

            throw $e;
        }
    }

    // public function import(object $importable, UploadedFile|string $file): string
    // {
    //     $path = Storage::disk('local')->putFile('import', $file);

    //     try {
    //         Excel::import(
    //             $importable,
    //             Storage::disk('local')->path($path)
    //         );

    //         Log::info('Import completed successfully (sync)', [
    //             'importable' => get_class($importable),
    //             'file_path' => $path,
    //         ]);

    //         return $path;

    //     } catch (\Throwable $e) {
    //         Log::error('Failed to import spreadsheet', [
    //             'importable' => get_class($importable),
    //             'error' => $e->getMessage(),
    //             'trace' => app()->isLocal() ? $e->getTraceAsString() : null,
    //         ]);

    //         Storage::disk('local')->delete($path);

    //         throw $e;
    //     }
    // }

}
