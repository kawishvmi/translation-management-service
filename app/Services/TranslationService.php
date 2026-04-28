<?php

namespace App\Services;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TranslationService
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = TranslationValue::query()
            ->with(['key:id,key', 'locale:id,code', 'tags:id,name']);

        $this->applyFilters($query, $filters);

        return $query
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 50));
    }

    public function create(array $data): TranslationValue
    {
        $key = TranslationKey::query()->firstOrCreate(['key' => $data['key']]);
        $locale = Locale::query()->firstOrCreate(['code' => $data['locale']]);

        $value = TranslationValue::query()->updateOrCreate(
            [
                'translation_key_id' => $key->id,
                'locale_id' => $locale->id,
            ],
            [
                'content' => $data['content'],
            ]
        );

        if (isset($data['tags'])) {
            $value->tags()->sync($this->resolveTagIds($data['tags']));
        }

        return $value->load(['key', 'locale', 'tags']);
    }

    public function update(TranslationValue $value, array $data): TranslationValue
    {
        if (array_key_exists('content', $data)) {
            $value->content = $data['content'];
            $value->save();
        }

        if (isset($data['tags'])) {
            $value->tags()->sync($this->resolveTagIds($data['tags']));
        }

        return $value->load(['key', 'locale', 'tags']);
    }

    public function export(array $filters): array
    {
        $query = TranslationValue::query()
            ->with(['key:id,key', 'locale:id,code'])
            ->select(['id', 'translation_key_id', 'locale_id', 'content']);

        $this->applyFilters($query, $filters);

        $payload = [];
        $query->chunkById(5000, function (Collection $rows) use (&$payload): void {
            foreach ($rows as $row) {
                $payload[$row->locale->code][$row->key->key] = $row->content;
            }
        });

        return $payload;
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['key'])) {
            $query->whereHas('key', function (Builder $keyQuery) use ($filters): void {
                $keyQuery->where('key', 'like', '%' . $filters['key'] . '%');
            });
        }

        if (!empty($filters['locale'])) {
            $query->whereHas('locale', function (Builder $localeQuery) use ($filters): void {
                $localeQuery->where('code', $filters['locale']);
            });
        }

        if (!empty($filters['content'])) {
            $query->where('content', 'like', '%' . $filters['content'] . '%');
        }

        if (!empty($filters['tags']) && is_array($filters['tags'])) {
            $query->whereHas('tags', function (Builder $tagQuery) use ($filters): void {
                $tagQuery->whereIn('name', $filters['tags']);
            });
        }
    }

    private function resolveTagIds(array $tags): array
    {
        return collect($tags)
            ->map(static fn (string $name): string => trim($name))
            ->filter()
            ->unique()
            ->map(function (string $name): int {
                return Tag::query()->firstOrCreate(['name' => $name])->id;
            })
            ->values()
            ->all();
    }
}
