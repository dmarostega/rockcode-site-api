<?php

namespace App\Services\Analytics;

use App\Models\ProductAnalyticsEvent;
use Illuminate\Validation\ValidationException;

class ProductAnalyticsEventService
{
    public const ALLOWED_EVENTS = [
        'page_viewed',
        'cta_clicked',
        'tool_card_clicked',
        'project_card_clicked',
        'tool_opened',
        'tool_result_copied',
        'tool_example_used',
        'tool_cleared',
    ];

    private const FORBIDDEN_METADATA_KEYS = [
        'base64',
        'content',
        'email',
        'hash',
        'input',
        'json',
        'name',
        'output',
        'payload',
        'phone',
        'query',
        'result',
        'text',
        'url',
        'value',
    ];

    private const MAX_METADATA_ITEMS = 10;

    private const MAX_METADATA_STRING_LENGTH = 120;

    private const MAX_METADATA_JSON_BYTES = 2048;

    public function store(array $payload): ProductAnalyticsEvent
    {
        $payload['metadata'] = $this->sanitizeMetadata($payload['metadata'] ?? []);
        $payload['occurred_at'] ??= now();

        return ProductAnalyticsEvent::create($payload);
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, bool|float|int|string|null>
     */
    private function sanitizeMetadata(array $metadata): array
    {
        if (count($metadata) > self::MAX_METADATA_ITEMS) {
            $this->fail('Metadata supports up to '.self::MAX_METADATA_ITEMS.' items.');
        }

        $sanitized = [];

        foreach ($metadata as $key => $value) {
            if (! is_string($key) || ! preg_match('/^[a-zA-Z0-9_.-]{1,40}$/', $key)) {
                $this->fail('Metadata keys must be short identifiers.');
            }

            if ($this->isForbiddenKey($key)) {
                $this->fail('Metadata contains a forbidden key.');
            }

            if (! is_null($value) && ! is_bool($value) && ! is_int($value) && ! is_float($value) && ! is_string($value)) {
                $this->fail('Metadata values must be scalar.');
            }

            if (is_string($value)) {
                $value = trim($value);

                if ($this->looksSensitive($value)) {
                    $this->fail('Metadata contains sensitive content.');
                }

                if (mb_strlen($value) > self::MAX_METADATA_STRING_LENGTH) {
                    $value = mb_substr($value, 0, self::MAX_METADATA_STRING_LENGTH);
                }

                if (! preg_match('/^[A-Za-z0-9_.:\/-]*$/', $value)) {
                    $this->fail('Metadata string values must be controlled identifiers.');
                }
            }

            $sanitized[$key] = $value;
        }

        $encoded = json_encode($sanitized, JSON_THROW_ON_ERROR);

        if (strlen($encoded) > self::MAX_METADATA_JSON_BYTES) {
            $this->fail('Metadata payload is too large.');
        }

        return $sanitized;
    }

    private function isForbiddenKey(string $key): bool
    {
        $normalized = strtolower($key);

        foreach (self::FORBIDDEN_METADATA_KEYS as $forbiddenKey) {
            if (str_contains($normalized, $forbiddenKey)) {
                return true;
            }
        }

        return false;
    }

    private function looksSensitive(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        if (preg_match('/https?:\/\/|www\.|[a-z0-9.-]+\.[a-z]{2,}/i', $value)) {
            return true;
        }

        if (preg_match('/\+?\d[\d\s().-]{7,}\d/', $value)) {
            return true;
        }

        return false;
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'metadata' => [$message],
        ]);
    }
}
