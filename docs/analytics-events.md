# Analytics events endpoint

Endpoint publico para receber eventos anonimos de produto da Rock Code Labs.

## Rota

`POST /api/analytics/events`

Rate limit: 30 requisicoes por minuto por IP resolvido.

## Ambiente publico

Configure os dominios autorizados para CORS:

```env
CORS_ALLOWED_ORIGINS=https://rockcodelabs.com.br,https://www.rockcodelabs.com.br
```

Configure os proxies confiaveis quando houver Cloudflare, Nginx, load balancer ou outro edge antes do Laravel:

```env
TRUSTED_PROXIES=REMOTE_ADDR
```

Use IPs ou CIDRs conhecidos quando o trafego puder chegar diretamente no servidor. Use `REMOTE_ADDR` quando o servidor de aplicacao receber chamadas somente do proxy imediatamente anterior. Evite `*` em producao se o app puder receber requisicoes diretas, porque headers `X-Forwarded-*` poderiam ser forjados pelo cliente.

Se `TRUSTED_PROXIES=REMOTE_ADDR` for usado, bloqueie acesso direto ao origin por firewall, Nginx, Cloudflare ou regra equivalente. Se o origin precisar aceitar trafego direto, prefira uma lista explicita de IPs/CIDRs confiaveis do proxy.

Com proxies confiaveis configurados, o rate limit `analytics-events` usa o IP real resolvido por `$request->ip()`. Sem essa configuracao, varias pessoas podem ser agrupadas pelo IP do proxy.

## Eventos permitidos

- `page_viewed`
- `cta_clicked`
- `tool_card_clicked`
- `project_card_clicked`
- `tool_opened`
- `tool_result_copied`
- `tool_example_used`
- `tool_cleared`

## Campos aceitos

| Campo | Obrigatorio | Regra |
| --- | --- | --- |
| `project` | Sim | Identificador controlado, ate 80 caracteres. |
| `event_name` | Sim | Deve estar na allowlist de eventos. |
| `feature` | Nao | Identificador controlado, ate 80 caracteres. |
| `source` | Nao | Identificador controlado, ate 120 caracteres. |
| `destination` | Nao | Identificador controlado, ate 120 caracteres. |
| `page_path` | Nao | Caminho interno iniciado por `/`, sem query string. |
| `session_id` | Nao | Identificador anonimo, ate 80 caracteres. |
| `metadata` | Nao | Objeto plano com ate 10 itens escalares. |
| `occurred_at` | Nao | Data/hora parseavel pelo Laravel. |

Identificadores controlados aceitam letras, numeros, `_`, `.`, `:` e `-`.

## Metadata

`metadata` e opcional e deve ser usada apenas para valores controlados pelo produto, como variante, posicao ou modo da interface.

Regras:

- ate 10 chaves;
- chaves com ate 40 caracteres;
- valores somente `string`, `number`, `boolean` ou `null`;
- strings sao truncadas em 120 caracteres;
- payload final de metadata limitado a 2 KB;
- strings de metadata nao aceitam espacos nem texto livre.

Chaves com termos sensiveis sao rejeitadas, incluindo `input`, `output`, `text`, `content`, `payload`, `json`, `base64`, `hash`, `url`, `email`, `phone`, `name`, `query`, `result` e `value`.

Valores com e-mail, URL ou telefone tambem sao rejeitados.

## Exemplo aceito

```json
{
  "project": "rockcode-site",
  "event_name": "cta_clicked",
  "feature": "home",
  "source": "hero",
  "destination": "contact_cta",
  "page_path": "/",
  "session_id": "session-123",
  "metadata": {
    "variant": "primary",
    "position": 1
  },
  "occurred_at": "2026-06-30T12:00:00Z"
}
```

Resposta:

```json
{
  "status": "accepted"
}
```

A resposta nao retorna `id` incremental. O contrato publico deve permanecer apenas com `status: accepted` para nao expor sequencia interna da tabela.

## Dados proibidos

Nao enviar nem persistir input digitado, output gerado, JSON colado, Base64, hashes, URLs digitadas em ferramentas, texto livre, e-mail, telefone, nome, dados pessoais ou fingerprinting.

## Roadmap de teste manual antes do deploy publico

- Rodar as migrations no ambiente correto.
- Enviar um POST valido via browser a partir de `https://rockcodelabs.com.br` e confirmar `201` com `status: accepted`.
- Confirmar no banco que o evento permitido foi persistido sem `input`, `output`, texto livre ou dados pessoais.
- Enviar evento invalido e confirmar `422`.
- Enviar metadata sensivel, aninhada ou com valor livre e confirmar `422`.
- Enviar metadata com string acima de 120 caracteres e confirmar truncamento controlado.
- Enviar payload de metadata acima de 2 KB e confirmar `422`.
- Executar preflight `OPTIONS` com origem publica e confirmar headers CORS.
- Repetir preflight com origem desconhecida e confirmar ausencia de `Access-Control-Allow-Origin`.
- Validar em log temporario ou shell que `$request->ip()` representa o IP esperado atras de Cloudflare/Nginx.
- Disparar 31 requisicoes no mesmo minuto para o mesmo IP resolvido e confirmar `429`.
- Repetir o teste de rate limit com dois IPs reais diferentes atras do proxy e confirmar que eles nao compartilham o mesmo bucket.
