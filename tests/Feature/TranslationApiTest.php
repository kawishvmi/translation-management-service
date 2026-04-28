<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_translation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/translations', [
                'key' => 'auth.login',
                'locale' => 'en',
                'content' => 'Login',
                'tags' => ['web'],
            ]);

        $response->assertCreated()
            ->assertJsonPath('key.key', 'auth.login')
            ->assertJsonPath('locale.code', 'en');
    }

    public function test_it_searches_by_key_and_content(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $locale = Locale::query()->create(['code' => 'en']);
        $key = TranslationKey::query()->create(['key' => 'menu.home']);
        TranslationValue::query()->create([
            'translation_key_id' => $key->id,
            'locale_id' => $locale->id,
            'content' => 'Home',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/translations?key=menu&content=Home');

        $response->assertOk()
            ->assertJsonPath('data.0.content', 'Home');
    }
}
