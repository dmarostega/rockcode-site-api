# Admin dashboard

Dashboard interno minimo para acompanhar eventos agregados de produto em `/admin`.

## Autenticacao

O acesso usa HTTP Basic Auth com credenciais definidas no ambiente:

- `ADMIN_USERNAME`
- `ADMIN_PASSWORD`

Se qualquer credencial estiver vazia ou incorreta, o acesso e bloqueado com `401`.

Em producao, use este admin somente com HTTPS ativo. HTTP Basic Auth nao deve ser exposto em HTTP puro.

Se o ambiente usar cache de configuracao, altere as credenciais e regenere o cache conforme o fluxo de deploy:

```text
php artisan config:clear
php artisan config:cache
```

O navegador pode manter credenciais Basic Auth em cache durante a sessao; trate isso como limitacao operacional do MVP.

## Dados exibidos

O dashboard exibe somente dados agregados:

- total de eventos;
- eventos por dia;
- top paginas;
- top ferramentas;
- top CTAs;
- top projetos/apps clicados.

Nao sao exibidos `session_id`, `metadata` ou eventos individuais.

## Protecoes da resposta

A rota `/admin` envia headers para reduzir cache e indexacao:

- `Cache-Control: must-revalidate, no-cache, no-store, private`
- `Pragma: no-cache`
- `X-Robots-Tag: noindex, nofollow, noarchive`

Como camada adicional fora da aplicacao, avalie Cloudflare Access, allowlist de IP, Nginx ou rate limit no edge se o admin ficar exposto publicamente.

## Roteiro manual de teste

1. Definir `ADMIN_USERNAME` e `ADMIN_PASSWORD` no ambiente.
2. Acessar `/admin` sem credenciais e confirmar bloqueio `401`.
3. Acessar `/admin` com credenciais validas e confirmar abertura da dashboard.
4. Enviar alguns eventos para `/api/analytics/events`.
5. Reabrir `/admin` e conferir totais, rankings e filtros de 7, 30 e 90 dias.
6. Confirmar que nao aparecem dados individuais de sessao ou metadados.
7. Confirmar que o acesso publico ocorre somente via HTTPS.
