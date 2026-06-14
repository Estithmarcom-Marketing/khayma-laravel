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
        Category::select('id', 'name_en', 'name_ar', 'parent_id')
            ->get()
            ->each(function ($category) {
                $this->existingCategories[$category->id] = $category;
            });
    }

    public function model(array $row): ?Category
    {
        $this->processedRows++;

        try {
            $nameEn = trim($row['name_en']);
            $nameAr = trim($row['name_ar']);

            $parentId = $this->resolveParentId($row);

            $enCategory = Category::where('name_en', $nameEn)->first();
            $arCategory = Category::where('name_ar', $nameAr)->first();

            if (
                $enCategory &&
                $arCategory &&
                $enCategory->id !== $arCategory->id
            ) {
                Log::warning('Conflicting category names detected', [
                    'row' => $this->processedRows,
                    'name_en' => $nameEn,
                    'name_ar' => $nameAr,
                ]);

                $this->skippedCount++;
                return null;
            }

            $category = $enCategory ?? $arCategory;

            if (
                $category &&
                $parentId &&
                $this->wouldCreateCircularReference($category->id, $parentId)
            ) {
                Log::warning('Circular reference detected', [
                    'row' => $this->processedRows,
                    'category_id' => $category->id,
                    'parent_id' => $parentId,
                ]);

                $this->skippedCount++;
                return null;
            }

            $category = DB::transaction(function () use ($row, $parentId, $category, $nameEn, $nameAr) {
                if (!$category) {
                    $category = new Category();
                }

                $category->name_en = $nameEn;
                $category->name_ar = $nameAr;
                $category->description_en = !empty($row['description_en'])
                    ? trim($row['description_en'])
                    : null;
                $category->description_ar = !empty($row['description_ar'])
                    ? trim($row['description_ar'])
                    : null;
                $category->parent_id = $parentId;
                $category->save();

                return $category;
            });

            $this->existingCategories[$category->id] = $category;
            $this->successCount++;

            if ($this->processedRows % 100 === 0) {
                Log::info("Import progress: {$this->processedRows} rows processed");
            }

            return $category;
        } catch (\Throwable $e) {
            Log::error('Category import row failed', [
                'row' => $this->processedRows,
                'name_en' => $row['name_en'] ?? 'N/A',
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

            if (!isset($this->existingCategories[$parentId])) {
                Log::warning('Parent category ID not found', [
                    'row' => $this->processedRows,
                    'parent_id' => $parentId,
                ]);

                return null;
            }

            return $parentId;
        }

        $parentName = trim($row['parent_id']);

        $parent = Category::where('name_en', $parentName)
            ->orWhere('name_ar', $parentName)
            ->first();

        if (!$parent) {
            Log::warning('Parent category not found', [
                'row' => $this->processedRows,
                'parent_name' => $parentName,
            ]);

            return null;
        }

        return $parent->id;
    }

    private function wouldCreateCircularReference(int $childId, int $potentialParentId): bool
    {
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
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name_en.required' => 'English name is required (column: name_en)',
            'name_ar.required' => 'Arabic name is required (column: name_ar)',
            'description_en.max' => 'English description cannot exceed 500 characters',
            'description_ar.max' => 'Arabic description cannot exceed 500 characters',
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
