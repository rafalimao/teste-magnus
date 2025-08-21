# Documentação da API FIPE

## Visão Geral

Esta API fornece acesso aos dados de veículos da tabela FIPE através de endpoints REST seguros e eficientes. A API utiliza autenticação JWT e implementa cache para otimização de performance.

**Base URL:** `http://localhost:8000`

## Autenticação

Todos os endpoints (exceto login) requerem autenticação via token JWT no header `Authorization`.

### Formato do Header
```
Authorization: Bearer {token}
```

## Endpoints

### 1. Autenticação

#### POST /index.php?route=auth/login

Realiza autenticação do usuário e retorna token JWT.

**Parâmetros:**
- Nenhum parâmetro de URL

**Body (JSON):**
```json
{
    "username": "string",
    "password": "string"
}
```

**Exemplo de Requisição:**
```bash
curl -X POST http://localhost:8000/index.php?route=auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "password"
  }'
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Login successful",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwiZXhwIjoxNjQwOTk1MjAwfQ.signature"
}
```

**Resposta de Erro (401):**
```json
{
    "error": "Invalid credentials"
}
```

---

### 2. Carga Inicial de Dados

#### POST /index.php?route=vehicles/load-initial

Inicia o processo de carga inicial dos dados de veículos da API FIPE.

**Headers:**
```
Authorization: Bearer {token}
```

**Parâmetros:**
- Nenhum

**Exemplo de Requisição:**
```bash
curl -X POST http://localhost:8000/index.php?route=vehicles/load-initial \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Initial data load started",
    "brands_count": 150
}
```

**Resposta de Erro (400):**
```json
{
    "error": "No brands found"
}
```

**Resposta de Erro (401):**
```json
{
    "error": "Invalid or expired token"
}
```

---

### 3. Consulta de Marcas

#### GET /index.php?route=vehicles/brands

Retorna todas as marcas de veículos cadastradas no sistema.

**Headers:**
```
Authorization: Bearer {token}
```

**Parâmetros:**
- Nenhum

**Exemplo de Requisição:**
```bash
curl -X GET http://localhost:8000/index.php?route=vehicles/brands \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

**Resposta de Sucesso (200):**
```json
[
    {
        "brand": "Audi"
    },
    {
        "brand": "BMW"
    },
    {
        "brand": "Chevrolet"
    },
    {
        "brand": "Fiat"
    },
    {
        "brand": "Ford"
    },
    {
        "brand": "Honda"
    },
    {
        "brand": "Hyundai"
    },
    {
        "brand": "Nissan"
    },
    {
        "brand": "Toyota"
    },
    {
        "brand": "Volkswagen"
    }
]
```

**Cache:** Este endpoint utiliza cache Redis com TTL de 1 hora.

---

### 4. Consulta de Modelos por Marca

#### GET /index.php?route=vehicles/models

Retorna todos os modelos de veículos de uma marca específica.

**Headers:**
```
Authorization: Bearer {token}
```

**Parâmetros de Query:**
- `brand` (string, obrigatório): Nome da marca

**Exemplo de Requisição:**
```bash
curl -X GET "http://localhost:8000/index.php?route=vehicles/models?brand=Toyota" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

**Resposta de Sucesso (200):**
```json
[
    {
        "id": 1,
        "code": "001",
        "model": "Corolla 1.8 GLi",
        "observations": ""
    },
    {
        "id": 2,
        "code": "002",
        "model": "Corolla 2.0 XEi",
        "observations": "Modelo premium"
    },
    {
        "id": 3,
        "code": "003",
        "model": "Camry 3.5 V6",
        "observations": ""
    },
    {
        "id": 4,
        "code": "004",
        "model": "Prius Hybrid",
        "observations": "Veículo híbrido"
    }
]
```

**Resposta de Erro (400):**
```json
{
    "error": "Brand parameter is required"
}
```

**Cache:** Este endpoint utiliza cache Redis com TTL de 1 hora, chave baseada na marca.

---

### 5. Atualização de Veículo

#### PUT /index.php?route=vehicles/update

Atualiza as informações de modelo e observações de um veículo específico.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "id": "integer",
    "model": "string",
    "observations": "string (opcional)"
}
```

**Exemplo de Requisição:**
```bash
curl -X PUT http://localhost:8000/index.php?route=vehicles/update \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "model": "Corolla 1.8 GLi 2024",
    "observations": "Modelo atualizado para 2024"
  }'
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Vehicle updated successfully"
}
```

**Resposta de Erro (400):**
```json
{
    "error": "ID and model are required"
}
```

**Resposta de Erro (500):**
```json
{
    "error": "Failed to update vehicle"
}
```

**Nota:** Este endpoint limpa automaticamente o cache relacionado após a atualização.

---

## Códigos de Status HTTP

| Código | Descrição |
|--------|-----------|
| 200 | Sucesso |
| 400 | Requisição inválida |
| 401 | Não autorizado |
| 404 | Rota não encontrada |
| 405 | Método não permitido |
| 500 | Erro interno do servidor |

## Estrutura de Erro Padrão

Todas as respostas de erro seguem o formato:

```json
{
    "error": "Descrição do erro"
}
```

## Rate Limiting

Atualmente não há limitação de taxa implementada, mas recomenda-se:
- Máximo 100 requisições por minuto por token
- Uso responsável da API de carga inicial

## Cache

A API implementa cache Redis para otimização:

- **Marcas**: Cache de 1 hora
- **Modelos por marca**: Cache de 1 hora por marca
- **Limpeza automática**: Cache é limpo quando dados são atualizados

## Segurança

### Autenticação JWT

- **Algoritmo**: HS256
- **Expiração**: 24 horas
- **Renovação**: Necessário novo login após expiração

### Validações

- Sanitização de entrada com `htmlspecialchars()`
- Prepared statements para prevenir SQL injection
- Validação de tipos de dados

### Headers CORS

A API está configurada para aceitar requisições de qualquer origem:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

## Exemplos de Uso Completo

### Fluxo Típico de Uso

1. **Fazer Login:**
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/index.php?route=auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}' | \
  jq -r '.token')
```

2. **Iniciar Carga de Dados:**
```bash
curl -X POST http://localhost:8000/index.php?route=vehicles/load-initial \
  -H "Authorization: Bearer $TOKEN"
```

3. **Aguardar Processamento (2-3 minutos)**

4. **Consultar Marcas:**
```bash
curl -X GET http://localhost:8000/index.php?route=vehicles/brands \
  -H "Authorization: Bearer $TOKEN"
```

5. **Consultar Modelos:**
```bash
curl -X GET "http://localhost:8000/index.php?route=vehicles/models?brand=Toyota" \
  -H "Authorization: Bearer $TOKEN"
```

6. **Atualizar Veículo:**
```bash
curl -X PUT http://localhost:8000/index.php?route=vehicles/update \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"id":1,"model":"Corolla 2024","observations":"Atualizado"}'
```

## Monitoramento

### Logs de Acesso

Os logs podem ser monitorados através do Docker:

```bash
docker-compose logs -f api-1
```

### Métricas de Performance

- Tempo de resposta médio: < 200ms (com cache)
- Tempo de resposta médio: < 1s (sem cache)
- Taxa de acerto do cache: > 80%

### Health Check

Endpoint simples para verificar saúde da API:

```bash
curl -X GET http://localhost:8000/index.php
```

## Limitações Conhecidas

1. **API FIPE Externa**: Dependente da disponibilidade da API externa
2. **Rate Limiting**: API externa pode ter limitações de taxa
3. **Processamento Inicial**: Pode levar alguns minutos para completar
4. **Memória**: Processamento de grandes volumes pode consumir memória

## Versionamento

- **Versão Atual**: 1.0
- **Compatibilidade**: Mantida para versões menores
- **Mudanças Breaking**: Apenas em versões maiores

## Suporte

Para suporte técnico:
1. Verificar logs dos containers
2. Consultar documentação de instalação
3. Verificar conectividade com dependências (MySQL, Redis)
4. Validar autenticação e tokens

