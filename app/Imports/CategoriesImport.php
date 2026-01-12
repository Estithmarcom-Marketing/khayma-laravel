<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;

class CategoriesImport implements SkipsOnFailure, ToModel, WithChunkReading, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    private array $existingCategories = [];

    private array $slugCache = [];

    private int $processedRows = 0;

    private int $successCount = 0;

    private int $skippedCount = 0;

    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);

        $this->loadExistingCategories();
    }

    private function loadExistingCategories(): void
    {
        Category::select('id', 'slug_en', 'slug_ar', 'parent_id')
            ->get()
            ->each(function ($category) {
                $this->existingCategories[$category->id] = $category;
                $this->slugCache['en'][$category->slug_en] = $category->id;
                $this->slugCache['ar'][$category->slug_ar] = $category->id;
            });
    }

    public function model(array $row): ?Category
    {
        $this->processedRows++;

        try {
            $parentId = $this->resolveParentId($row);

            if ($parentId && $this->wouldCreateCircularReference($row['slug_en'], $parentId)) {
                Log::warning('Circular reference detected', [
                    'row' => $this->processedRows,
                    'slug_en' => $row['slug_en'],
                    'parent_id' => $parentId,
                ]);
                $this->skippedCount++;

                return null;
            }

            $category = DB::transaction(function () use ($row, $parentId) {
                return Category::updateOrCreate(
                    ['slug_en' => $row['slug_en']],
                    [
                        'name_en' => trim($row['name_en']),
                        'name_ar' => trim($row['name_ar']),
                        'slug_ar' => $row['slug_ar'],
                        'description_en' => ! empty($row['description_en']) ? trim($row['description_en']) : null,
                        'description_ar' => ! empty($row['description_ar']) ? trim($row['description_ar']) : null,
                        'parent_id' => $parentId,
                    ]
                );
            });

            $this->existingCategories[$category->id] = $category;
            $this->slugCache['en'][$category->slug_en] = $category->id;
            $this->slugCache['ar'][$category->slug_ar] = $category->id;

            $this->successCount++;

            if ($this->processedRows % 100 === 0) {
                Log::info("Import progress: {$this->processedRows} rows processed");
            }

            return $category;

        } catch (\Throwable $e) {
            Log::error('Category import row failed', [
                'row' => $this->processedRows,
                'slug_en' => $row['slug_en'] ?? 'N/A',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $this->skippedCount++;

            return null;
        }
    }

    private function resolveParentId(array $row): ?int
    {
        if (empty($row['parent_id'])) {
            return null;
        }

        if (is_numeric($row['parent_id'])) {
            $parentId = (int) $row['parent_id'];

            if (! isset($this->existingCategories[$parentId])) {
                Log::warning('Parent category ID not found', [
                    'row' => $this->processedRows,
                    'parent_id' => $parentId,
                    'child_slug' => $row['slug_en'],
                ]);

                return null;
            }

            return $parentId;
        }

        $parentSlug = trim($row['parent_id']);

        if (isset($this->slugCache['en'][$parentSlug])) {
            return $this->slugCache['en'][$parentSlug];
        }

        if (isset($this->slugCache['ar'][$parentSlug])) {
            return $this->slugCache['ar'][$parentSlug];
        }

        Log::warning('Parent category slug not found', [
            'row' => $this->processedRows,
            'parent_slug' => $parentSlug,
            'child_slug' => $row['slug_en'],
        ]);

        return null;
    }

    private function wouldCreateCircularReference(string $childSlug, int $potentialParentId): bool
    {

        $childId = $this->slugCache['en'][$childSlug] ?? null;

        if (! $childId) {
            return false;

        }

        if ($childId === $potentialParentId) {
            return true;
        }

        $currentId = $potentialParentId;
        $visited = [];

        while ($currentId !== null) {
            if ($currentId === $childId) {
                return true;
            }

            if (isset($visited[$currentId])) {
                break;
            }

            $visited[$currentId] = true;
            $currentId = $this->existingCategories[$currentId]->parent_id ?? null;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug_en' => [
                'required',
                'string',
                'max:255',
                'unique:categories,slug_en',
            ],
            'slug_ar' => [
                'required',
                'string',
                'max:255',
                'unique:categories,slug_ar',
            ],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name_en.required' => 'English name is required (column: name_en)',
            'name_ar.required' => 'Arabic name is required (column: name_ar)',
            'slug_en.required' => 'English slug is required (column: slug_en)',
            'slug_en.regex' => 'English slug must be lowercase with hyphens only (e.g., electronics-phones)',
            'slug_ar.required' => 'Arabic slug is required (column: slug_ar)',
            'description_en.max' => 'English description cannot exceed 1000 characters',
            'description_ar.max' => 'Arabic description cannot exceed 1000 characters',
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            Log::warning('Category validation failed', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);

            $this->skippedCount++;
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                Log::info('Categories import started', [
                    'existing_categories' => count($this->existingCategories),
                ]);
            },

            AfterImport::class => function () {
                $duration = round(microtime(true) - $this->startTime, 2);

                Log::info('Categories import completed', [
                    'total_rows' => $this->processedRows,
                    'successful' => $this->successCount,
                    'skipped' => $this->skippedCount,
                    'validation_failures' => count($this->failures()),
                    'duration_seconds' => $duration,
                ]);
            },
        ];
    }

    /**
     * Chunk reading size
     */
    public function chunkSize(): int
    {
        return 100;
    }

    public function getStats(): array
    {
        return [
            'processed' => $this->processedRows,
            'successful' => $this->successCount,
            'skipped' => $this->skippedCount,
            'validation_failures' => count($this->failures()),
            'duration' => round(microtime(true) - $this->startTime, 2),
        ];
    }
}
