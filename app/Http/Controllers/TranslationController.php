<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Models\TranslationValue;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TranslationController extends Controller
{
    public function __construct(private readonly TranslationService $translationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $results = $this->translationService->search($request->all());

        return response()->json($results);
    }

    public function store(StoreTranslationRequest $request): JsonResponse
    {
        $value = $this->translationService->create($request->validated());

        return response()->json($value, 201);
    }

    public function show(TranslationValue $translation): JsonResponse
    {
        $translation->load(['key', 'locale', 'tags']);

        return response()->json($translation);
    }

    public function update(UpdateTranslationRequest $request, TranslationValue $translation): JsonResponse
    {
        $value = $this->translationService->update($translation, $request->validated());

        return response()->json($value);
    }

    public function export(Request $request): StreamedResponse
    {
        $data = $this->translationService->export($request->all());

        return response()->streamDownload(
            static function () use ($data): void {
                echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            },
            'translations.json',
            ['Content-Type' => 'application/json']
        );
    }
}
