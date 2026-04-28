<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_returns_json_under_reasonable_time(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('perf')->plainTextToken;

        $locale = Locale::query()->create(['code' => 'en']);
        for ($i = 0; $i < 1000; $i++) {
            $key = TranslationKey::query()->create(['key' => 'perf.key.' . $i]);
            TranslationValue::query()->create([
                'translation_key_id' => $key->id,
                'locale_id' => $locale->id,
                'content' => 'value ' . $i,
            ]);
        }

        $start = microtime(true);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/translations/export');
        $elapsedMs = (microtime(true) - $start) * 1000;

        $response->assertOk();
        $this->assertLessThan(500, $elapsedMs);
    }
}
