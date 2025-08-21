# Projeto FIPE - Integração com API FIPE

Este projeto implementa uma solução completa para integração com a API FIPE, utilizando PHP 8+, Docker, Redis para cache e filas, MySQL para persistência de dados e autenticação JWT.

## Arquitetura

O projeto é composto por duas APIs principais:

- **API-1**: Responsável pelos endpoints REST principais e interface com o usuário
- **API-2**: Responsável pelo processamento assíncrono de dados via filas

### Componentes

- **MySQL**: Banco de dados para persistência
- **Redis**: Cache e sistema de filas
- **Docker**: Containerização e orquestração
- **JWT**: Autenticação e autorização

## Estrutura do Projeto

```
fipe-project/
├── api-1/                 # API principal (REST endpoints)
│   ├── config/           # Configurações de banco e Redis
│   ├── controllers/      # Controladores da aplicação
│   ├── middleware/       # Middleware de autenticação
│   ├── models/          # Modelos de dados
│   ├── services/        # Serviços externos (FIPE API)
│   └── index.php        # Ponto de entrada da API
├── api-2/                # API de processamento
│   ├── config/          # Configurações
│   ├── models/          # Modelos de dados
│   ├── services/        # Serviços externos
│   ├── workers/         # Processadores de fila
│   └── worker.php       # Worker principal
├── database/            # Scripts de banco de dados
│   ├── schema.sql       # Esquema do banco
│   └── migrations.php   # Script de migração
├── docker/              # Configurações Docker
│   └── docker-compose.yml
└── tests/               # Testes automatizados
```

## Instalação e Execução

### Pré-requisitos

- Docker
- Docker Compose

### Passos para execução

1. **Clone o projeto e navegue até o diretório:**
   ```bash
   cd fipe-project/docker
   ```

2. **Execute o ambiente com Docker Compose:**
   ```bash
   docker-compose up -d
   ```

3. **Execute as migrações do banco de dados:**
   ```bash
   docker-compose exec api-1 php /var/www/html/../database/migrations.php
   ```

4. **Inicie o worker da API-2:**
   ```bash
   docker-compose exec api-2 php worker.php
   ```

### Verificação da instalação

- API-1 estará disponível em: `http://localhost:8000`
- MySQL estará disponível em: `localhost:3306`
- Redis estará disponível em: `localhost:6379`

## Endpoints da API

### Autenticação

#### POST /index.php?route=auth/login
Realiza login e retorna token JWT.

**Requisição:**
```json
{
    "username": "admin",
    "password": "password"
}
```

**Resposta:**
```json
{
    "message": "Login successful",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### Veículos

#### POST /index.php?route=vehicles/load-initial
Inicia a carga inicial de dados da FIPE.

**Headers:**
```
Authorization: Bearer {token}
```

**Resposta:**
```json
{
    "message": "Initial data load started",
    "brands_count": 150
}
```

#### GET /index.php?route=vehicles/brands
Retorna todas as marcas cadastradas.

**Headers:**
```
Authorization: Bearer {token}
```

**Resposta:**
```json
[
    {
        "brand": "Toyota"
    },
    {
        "brand": "Honda"
    }
]
```

#### GET /index.php?route=vehicles/models?brand={marca}
Retorna modelos de uma marca específica.

**Headers:**
```
Authorization: Bearer {token}
```

**Parâmetros:**
- `brand`: Nome da marca

**Resposta:**
```json
[
    {
        "id": 1,
        "code": "001",
        "model": "Corolla",
        "observations": ""
    }
]
```

#### PUT /index.php?route=vehicles/update
Atualiza dados de um veículo.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Requisição:**
```json
{
    "id": 1,
    "model": "Corolla 2024",
    "observations": "Modelo atualizado"
}
```

**Resposta:**
```json
{
    "message": "Vehicle updated successfully"
}
```

## Testes

Para executar os testes:

```bash
docker-compose exec api-1 php /var/www/html/../tests/VehicleControllerTest.php
```

## Funcionalidades Implementadas

### ✅ Requisitos Atendidos

1. **Serviço REST para carga inicial** - Endpoint para iniciar processamento
2. **Busca de marcas na FIPE** - Integração com API externa
3. **Sistema de filas** - Redis para processamento assíncrono
4. **Processamento de modelos** - API-2 consome filas e processa dados
5. **Persistência em SQL** - MySQL com esquema otimizado
6. **Cache Redis** - Cache de consultas para performance
7. **Autenticação JWT** - Proteção de endpoints
8. **Docker** - Ambiente containerizado completo

### 🏗️ Arquitetura e Boas Práticas

- **Clean Architecture**: Separação clara de responsabilidades
- **SOLID**: Princípios aplicados nos controladores e serviços
- **Design Patterns**: Factory, Repository, Strategy
- **REST**: Endpoints seguem padrões REST
- **DDD**: Domínio bem definido com entidades e serviços
- **Testes**: Cobertura de testes automatizados

## Monitoramento e Logs

O sistema inclui logs de processamento que podem ser consultados na tabela `processing_logs` do banco de dados.

## Segurança

- Autenticação JWT com expiração
- Sanitização de dados de entrada
- Prepared statements para prevenir SQL injection
- Headers CORS configurados adequadamente

## Performance

- Cache Redis para consultas frequentes
- Índices otimizados no banco de dados
- Processamento assíncrono via filas
- Conexões persistentes com banco de dados

## Contribuição

Para contribuir com o projeto:

1. Faça fork do repositório
2. Crie uma branch para sua feature
3. Implemente os testes
4. Faça commit das mudanças
5. Abra um Pull Request

## Licença

Este projeto está sob licença MIT.

