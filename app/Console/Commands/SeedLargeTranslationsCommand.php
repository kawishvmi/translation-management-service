<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedLargeTranslationsCommand extends Command
{
    protected $signature = 'translations:seed-large {count=100000}';
    protected $description = 'Seed large translation dataset for performance testing';

    public function handle(): int
    {
        $count = max((int) $this->argument('count'), 100000);
        $locales = Locale::query()->pluck('id', 'code');
        $tags = Tag::query()->pluck('id')->all();

        if ($locales->isEmpty() || empty($tags)) {
            $this->error('Run php artisan db:seed first to initialize locales/tags.');
            return self::FAILURE;
        }

        $batchSize = 1000;
        for ($i = 0; $i < $count; $i += $batchSize) {
            $upper = min($i + $batchSize, $count);
            for ($j = $i; $j < $upper; $j++) {
                $key = TranslationKey::query()->firstOrCreate([
                    'key' => sprintf('feature.section_%d.title', $j),
                ]);

                foreach ($locales as $code => $localeId) {
                    $value = TranslationValue::query()->updateOrCreate(
                        [
                            'translation_key_id' => $key->id,
                            'locale_id' => $localeId,
                        ],
                        [
                            'content' => Str::title($code . ' translation ' . $j),
                        ]
                    );

                    $value->tags()->syncWithoutDetaching([$tags[array_rand($tags)]]);
                }
            }
            $this->info("Seeded {$upper} keys");
        }

        return self::SUCCESS;
    }
}
