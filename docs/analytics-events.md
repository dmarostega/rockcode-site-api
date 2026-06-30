# Analytics events endpoint

Endpoint publico para receber eventos anonimos de produto da Rock Code Labs.

## Rota

`POST /api/analytics/events`

Rate limit: 30 requisicoes por minuto por origem.

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
  "id": 1,
  "status": "accepted"
}
```

## Dados proibidos

Nao enviar nem persistir input digitado, output gerado, JSON colado, Base64, hashes, URLs digitadas em ferramentas, texto livre, e-mail, telefone, nome, dados pessoais ou fingerprinting.
