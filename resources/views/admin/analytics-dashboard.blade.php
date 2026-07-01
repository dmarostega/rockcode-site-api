<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Rock Code Labs</title>
    <style>
        :root {
            color: #1f2933;
            background: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
        }

        main {
            margin: 0 auto;
            max-width: 1120px;
            padding: 32px 20px 48px;
        }

        header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        h1 {
            font-size: 28px;
            margin: 0 0 8px;
        }

        p {
            color: #52606d;
            line-height: 1.5;
            margin: 0;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        nav a {
            background: #ffffff;
            border: 1px solid #d9e2ec;
            border-radius: 6px;
            color: #334e68;
            padding: 8px 12px;
            text-decoration: none;
        }

        nav a[aria-current="true"] {
            background: #1f2933;
            border-color: #1f2933;
            color: #ffffff;
        }

        .notice,
        .empty {
            background: #ffffff;
            border: 1px solid #d9e2ec;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 16px;
        }

        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-bottom: 20px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #d9e2ec;
            border-radius: 8px;
            padding: 18px;
        }

        .card h2 {
            color: #52606d;
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 12px;
            text-transform: uppercase;
        }

        .metric {
            color: #102a43;
            font-size: 34px;
            font-weight: 700;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid #edf2f7;
            padding: 10px 0;
            text-align: left;
            vertical-align: top;
        }

        th:last-child,
        td:last-child {
            text-align: right;
        }

        .muted {
            color: #829ab1;
        }

        @media (max-width: 720px) {
            header {
                display: block;
            }

            nav {
                margin-top: 16px;
            }
        }
    </style>
</head>
<body>
<main>
    <header>
        <div>
            <h1>Dashboard interno</h1>
            <p>Dados agregados e estimativos de eventos de produto da Rock Code Labs.</p>
        </div>

        <nav aria-label="Período">
            @foreach ($periods as $period)
                <a href="{{ route('admin.analytics', ['period' => $period]) }}" aria-current="{{ $summary['periodDays'] === $period ? 'true' : 'false' }}">
                    {{ $period }} dias
                </a>
            @endforeach
        </nav>
    </header>

    <section class="notice">
        <p>Uso interno. Esta página mostra apenas contagens agregadas e não exibe sessões, metadados ou dados digitados por usuários.</p>
    </section>

    @if (! $summary['hasEvents'])
        <section class="empty">
            <h2>Nenhum evento encontrado</h2>
            <p>Ainda não há eventos persistidos para o período selecionado.</p>
        </section>
    @endif

    <section class="grid">
        <article class="card">
            <h2>Total de eventos</h2>
            <div class="metric">{{ number_format($summary['totalEvents'], 0, ',', '.') }}</div>
        </article>

        <article class="card">
            <h2>Período</h2>
            <div class="metric">{{ $summary['periodDays'] }}</div>
            <p class="muted">dias analisados</p>
        </article>
    </section>

    <section class="grid">
        @include('admin.partials.analytics-table', [
            'title' => 'Eventos por dia',
            'rows' => $summary['eventsByDay'],
            'empty' => 'Sem eventos por dia.',
        ])

        @include('admin.partials.analytics-table', [
            'title' => 'Top páginas',
            'rows' => $summary['topPages'],
            'empty' => 'Sem páginas registradas.',
        ])

        @include('admin.partials.analytics-table', [
            'title' => 'Top ferramentas',
            'rows' => $summary['topTools'],
            'empty' => 'Sem ferramentas registradas.',
        ])

        @include('admin.partials.analytics-table', [
            'title' => 'Top CTAs',
            'rows' => $summary['topCtas'],
            'empty' => 'Sem CTAs registrados.',
        ])

        @include('admin.partials.analytics-table', [
            'title' => 'Top projetos/apps',
            'rows' => $summary['topProjects'],
            'empty' => 'Sem projetos ou apps registrados.',
        ])
    </section>
</main>
</body>
</html>
