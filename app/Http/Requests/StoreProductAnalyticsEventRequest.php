<?php

namespace App\Http\Requests;

use App\Services\Analytics\ProductAnalyticsEventService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductAnalyticsEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $identifier = 'regex:/^[A-Za-z0-9_.:-]+$/';

        return [
            'project' => ['required', 'string', 'max:80', $identifier],
            'event_name' => ['required', 'string', Rule::in(ProductAnalyticsEventService::ALLOWED_EVENTS)],
            'feature' => ['nullable', 'string', 'max:80', $identifier],
            'source' => ['nullable', 'string', 'max:120', $identifier],
            'destination' => ['nullable', 'string', 'max:120', $identifier],
            'page_path' => ['nullable', 'string', 'max:255', 'regex:/^\/[A-Za-z0-9\/_.-]*$/'],
            'session_id' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9_.:-]+$/'],
            'metadata' => ['nullable', 'array'],
            'occurred_at' => ['nullable', 'date'],
        ];
    }
}
