<?php

namespace Tests\Feature;

use App\Models\ProductAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ProductAnalyticsEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_allowed_event_is_persisted(): void
    {
        $response = $this->postJson('/api/analytics/events', [
            'project' => 'rockcode-site',
            'event_name' => 'cta_clicked',
            'feature' => 'home',
            'source' => 'hero',
            'destination' => 'contact_cta',
            'page_path' => '/',
            'session_id' => 'session-123',
            'metadata' => [
                'variant' => 'primary',
                'position' => 1,
            ],
            'occurred_at' => '2026-06-30T12:00:00Z',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'status' => 'accepted',
            ]);

        $this->assertDatabaseHas('product_analytics_events', [
            'project' => 'rockcode-site',
            'event_name' => 'cta_clicked',
            'feature' => 'home',
            'source' => 'hero',
            'destination' => 'contact_cta',
            'page_path' => '/',
            'session_id' => 'session-123',
        ]);

        $event = ProductAnalyticsEvent::query()->firstOrFail();

        $this->assertSame([
            'variant' => 'primary',
            'position' => 1,
        ], $event->metadata);
    }

    public function test_disallowed_event_is_rejected(): void
    {
        $response = $this->postJson('/api/analytics/events', [
            'project' => 'rockcode-site',
            'event_name' => 'typed_tool_input',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('product_analytics_events', 0);
    }

    public function test_sensitive_metadata_is_rejected(): void
    {
        $response = $this->postJson('/api/analytics/events', [
            'project' => 'rockcode-site',
            'event_name' => 'tool_opened',
            'metadata' => [
                'user_email' => 'cliente@example.com',
            ],
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('product_analytics_events', 0);
    }

    public function test_analytics_events_are_rate_limited(): void
    {
        RateLimiter::clear('127.0.0.1');

        for ($attempt = 1; $attempt <= 30; $attempt++) {
            $this->postJson('/api/analytics/events', [
                'project' => 'rockcode-site',
                'event_name' => 'page_viewed',
                'page_path' => '/',
            ])->assertCreated();
        }

        $this->postJson('/api/analytics/events', [
            'project' => 'rockcode-site',
            'event_name' => 'page_viewed',
            'page_path' => '/',
        ])->assertTooManyRequests();
    }
}
