# Admin dashboard

Dashboard interno mínimo para acompanhar eventos agregados de produto em `/admin`.

## Autenticação

O acesso usa HTTP Basic Auth com credenciais definidas no ambiente:

- `ADMIN_USERNAME`
- `ADMIN_PASSWORD`

Se qualquer credencial estiver vazia ou incorreta, o acesso é bloqueado com `401`.

## Dados exibidos

O dashboard exibe somente dados agregados:

- total de eventos;
- eventos por dia;
- top páginas;
- top ferramentas;
- top CTAs;
- top projetos/apps clicados.

Não são exibidos `session_id`, `metadata` ou eventos individuais.

## Roteiro manual de teste

1. Definir `ADMIN_USERNAME` e `ADMIN_PASSWORD` no ambiente.
2. Acessar `/admin` sem credenciais e confirmar bloqueio `401`.
3. Acessar `/admin` com credenciais válidas e confirmar abertura da dashboard.
4. Enviar alguns eventos para `/api/analytics/events`.
5. Reabrir `/admin` e conferir totais, rankings e filtros de 7, 30 e 90 dias.
6. Confirmar que não aparecem dados individuais de sessão ou metadados.
