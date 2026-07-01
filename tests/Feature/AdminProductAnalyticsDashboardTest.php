<?php

namespace Tests\Feature;

use App\Models\ProductAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductAnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_blocks_unauthenticated_access(): void
    {
        config([
            'admin.username' => 'admin',
            'admin.password' => 'secret',
        ]);

        $this->get('/admin')
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Basic realm="Rock Code Labs Admin"');
    }

    public function test_admin_dashboard_blocks_access_when_credentials_are_not_configured(): void
    {
        config([
            'admin.username' => '',
            'admin.password' => '',
        ]);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode(':'),
        ])->get('/admin')
            ->assertUnauthorized();
    }

    public function test_admin_dashboard_displays_basic_aggregates(): void
    {
        config([
            'admin.username' => 'admin',
            'admin.password' => 'secret',
        ]);

        $this->travelTo('2026-06-30 12:00:00');

        ProductAnalyticsEvent::query()->create([
            'project' => 'rockcode-site',
            'event_name' => 'page_viewed',
            'page_path' => '/',
            'session_id' => 'session-private',
            'metadata' => ['variant' => 'private'],
            'occurred_at' => now(),
        ]);

        ProductAnalyticsEvent::query()->create([
            'project' => 'rockcode-site',
            'event_name' => 'cta_clicked',
            'destination' => 'contact_cta',
            'page_path' => '/',
            'occurred_at' => now(),
        ]);

        ProductAnalyticsEvent::query()->create([
            'project' => 'rockcode-site',
            'event_name' => 'tool_opened',
            'feature' => 'base64',
            'page_path' => '/ferramentas/base64',
            'occurred_at' => now(),
        ]);

        ProductAnalyticsEvent::query()->create([
            'project' => 'rockcode-site',
            'event_name' => 'project_card_clicked',
            'destination' => 'controle-financeiro',
            'page_path' => '/apps',
            'occurred_at' => now(),
        ]);

        $this->getWithAdminAuth('/admin')
            ->assertOk()
            ->assertSee('Total de eventos')
            ->assertSee('4')
            ->assertSee('2026-06-30')
            ->assertSee('/')
            ->assertSee('base64')
            ->assertSee('contact_cta')
            ->assertSee('controle-financeiro')
            ->assertDontSee('session-private')
            ->assertDontSee('variant')
            ->assertDontSee('private');
    }

    public function test_admin_dashboard_displays_empty_state(): void
    {
        config([
            'admin.username' => 'admin',
            'admin.password' => 'secret',
        ]);

        $this->getWithAdminAuth('/admin')
            ->assertOk()
            ->assertSee('Nenhum evento encontrado')
            ->assertSee('Ainda não há eventos persistidos para o período selecionado.')
            ->assertSee('Total de eventos')
            ->assertSee('0');
    }

    private function getWithAdminAuth(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode('admin:secret'),
        ])->get($uri);
    }
}
