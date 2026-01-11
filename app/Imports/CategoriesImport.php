<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Events\AfterImport;

class CategoriesImport implements
    ShouldQueue,
    ToModel,
    WithChunkReading,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithEvents
{
    use SkipsFailures;

    public string $queue = 'imports';
    public int $timeout = 900;

    private ?string $filePath;

    public function __construct(?string $filePath = null)
    {
        $this->filePath = $filePath;
    }

    /**
     * Insert or update category
     */
    public function model(array $row): ?Category
    {
        try {
            $parentId = null;

            if (!empty($row['parent_slug_en'])) {
                $parentId = Category::where(
                    'slug_en',
                    $row['parent_slug_en']
                )->value('id');
            }

            return Category::updateOrCreate(
                ['slug_en' => $row['slug_en']],
                [
                    'name_en'        => $row['name_en'],
                    'name_ar'        => $row['name_ar'],
                    'slug_ar'        => $row['slug_ar'],
                    'description_en' => $row['description_en'] ?? null,
                    'description_ar' => $row['description_ar'] ?? null,
                    'parent_id'      => $parentId,
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Category import failed', [
                'slug_en' => $row['slug_en'] ?? null,
                'error'   => $e->getMessage(),
                'trace'   => app()->isLocal() ? $e->getTraceAsString() : null,
            ]);

            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name_en'        => ['required', 'string', 'max:255'],
            'name_ar'        => ['required', 'string', 'max:255'],
            'slug_en'        => ['required', 'string', 'max:255'],
            'slug_ar'        => ['required', 'string', 'max:255'],
            'description_en'=> ['nullable', 'string', 'max:500'],
            'description_ar'=> ['nullable', 'string', 'max:500'],
            'parent_slug_en'=> ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'name_en.required' => 'English name is required',
            'name_ar.required' => 'Arabic name is required',
            'slug_en.required' => 'English slug is required',
            'slug_ar.required' => 'Arabic slug is required',
        ];
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            Log::warning('Category row skipped', [
                'row'     => $failure->row(),
                'errors'  => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }

    /**
     * Events
     */
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                if ($this->filePath) {
                    Storage::disk('local')->delete($this->filePath);
                }

                Log::info('Categories import completed', [
                    'failures' => count($this->failures()),
                ]);
            },
        ];
    }

    /**
     * Chunk size
     */
    public function chunkSize(): int
    {
        return 500;
    }
}
