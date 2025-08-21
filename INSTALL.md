# Instruções de Instalação e Execução

## Pré-requisitos

Antes de iniciar, certifique-se de ter instalado:

- **Docker** (versão 20.10 ou superior)
- **Docker Compose** (versão 1.29 ou superior)
- **Git** (para clonar o repositório)

### Verificação dos pré-requisitos

```bash
# Verificar Docker
docker --version

# Verificar Docker Compose
docker-compose --version

# Verificar Git
git --version
```

## Instalação Passo a Passo

### 1. Preparação do Ambiente

```bash
# Navegar para o diretório do projeto
cd fipe-project/docker

# Verificar se todos os arquivos estão presentes
ls -la
```

### 2. Construção e Inicialização dos Containers

```bash
# Construir e iniciar todos os serviços
docker-compose up -d

# Verificar se todos os containers estão rodando
docker-compose ps
```

**Saída esperada:**
```
Name                Command               State           Ports
----------------------------------------------------------------
docker_api-1_1     docker-php-entrypoint apac ...   Up      0.0.0.0:8000->80/tcp
docker_api-2_1     docker-php-entrypoint php  ...   Up
docker_db_1        docker-entrypoint.sh mysqld      Up      0.0.0.0:3306->3306/tcp
docker_redis_1     docker-entrypoint.sh redis ...   Up      0.0.0.0:6379->6379/tcp
```

### 3. Configuração do Banco de Dados

```bash
# Aguardar o MySQL inicializar completamente (30-60 segundos)
sleep 60

# Executar as migrações do banco de dados
docker-compose exec api-1 php /var/www/html/../database/migrations.php
```

**Saída esperada:**
```
Running database migrations...
Executed: CREATE DATABASE IF NOT EXISTS fipe_db...
Executed: USE fipe_db...
Executed: CREATE TABLE IF NOT EXISTS vehicles (...
Database migrations completed successfully!
```

### 4. Inicialização do Worker

```bash
# Iniciar o worker da API-2 em background
docker-compose exec -d api-2 php worker.php
```

### 5. Verificação da Instalação

#### Testar conectividade da API-1
```bash
curl -X GET http://localhost:8000/index.php?route=auth/login
```

#### Testar conectividade do banco de dados
```bash
docker-compose exec db mysql -u user -ppassword -e "SHOW DATABASES;"
```

#### Testar conectividade do Redis
```bash
docker-compose exec redis redis-cli ping
```

## Testes de Funcionalidade

### 1. Autenticação

```bash
# Fazer login e obter token
curl -X POST http://localhost:8000/index.php?route=auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'
```

**Resposta esperada:**
```json
{
    "message": "Login successful",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### 2. Carga Inicial de Dados

```bash
# Substituir {TOKEN} pelo token obtido no passo anterior
curl -X POST http://localhost:8000/index.php?route=vehicles/load-initial \
  -H "Authorization: Bearer {TOKEN}"
```

### 3. Consulta de Marcas

```bash
# Aguardar processamento (2-3 minutos) e consultar marcas
curl -X GET http://localhost:8000/index.php?route=vehicles/brands \
  -H "Authorization: Bearer {TOKEN}"
```

### 4. Consulta de Modelos

```bash
# Consultar modelos de uma marca específica
curl -X GET "http://localhost:8000/index.php?route=vehicles/models?brand=Toyota" \
  -H "Authorization: Bearer {TOKEN}"
```

## Execução de Testes Automatizados

```bash
# Executar testes da API-1
docker-compose exec api-1 php /var/www/html/../tests/VehicleControllerTest.php
```

## Monitoramento e Logs

### Visualizar logs dos containers

```bash
# Logs da API-1
docker-compose logs -f api-1

# Logs da API-2 (worker)
docker-compose logs -f api-2

# Logs do banco de dados
docker-compose logs -f db

# Logs do Redis
docker-compose logs -f redis
```

### Monitorar processamento

```bash
# Verificar fila no Redis
docker-compose exec redis redis-cli llen brands_queue

# Verificar dados no banco
docker-compose exec db mysql -u user -ppassword fipe_db -e "SELECT COUNT(*) as total_vehicles FROM vehicles;"
```

## Solução de Problemas

### Problema: Containers não iniciam

**Solução:**
```bash
# Parar todos os containers
docker-compose down

# Limpar volumes (ATENÇÃO: apaga dados)
docker-compose down -v

# Reconstruir imagens
docker-compose build --no-cache

# Iniciar novamente
docker-compose up -d
```

### Problema: Erro de conexão com banco de dados

**Solução:**
```bash
# Verificar se o MySQL está rodando
docker-compose exec db mysqladmin -u root -proot_password ping

# Verificar logs do MySQL
docker-compose logs db

# Reiniciar apenas o banco
docker-compose restart db
```

### Problema: Worker não processa dados

**Solução:**
```bash
# Verificar se o worker está rodando
docker-compose exec api-2 ps aux | grep php

# Reiniciar o worker
docker-compose exec api-2 pkill php
docker-compose exec -d api-2 php worker.php

# Verificar logs do worker
docker-compose logs api-2
```

### Problema: Cache não funciona

**Solução:**
```bash
# Verificar Redis
docker-compose exec redis redis-cli ping

# Limpar cache
docker-compose exec redis redis-cli flushall

# Reiniciar Redis
docker-compose restart redis
```

## Parada do Sistema

```bash
# Parar todos os serviços
docker-compose down

# Parar e remover volumes (apaga dados)
docker-compose down -v
```

## Configurações Avançadas

### Alterar portas

Edite o arquivo `docker-compose.yml` e modifique as portas conforme necessário:

```yaml
services:
  api-1:
    ports:
      - "8080:80"  # Alterar porta da API-1
  db:
    ports:
      - "3307:3306"  # Alterar porta do MySQL
```

### Configurar variáveis de ambiente

Crie um arquivo `.env` no diretório `docker/`:

```env
DB_PASSWORD=nova_senha
REDIS_PASSWORD=redis_senha
JWT_SECRET=novo_secret_jwt
```

### Backup do banco de dados

```bash
# Criar backup
docker-compose exec db mysqldump -u user -ppassword fipe_db > backup.sql

# Restaurar backup
docker-compose exec -T db mysql -u user -ppassword fipe_db < backup.sql
```

## Suporte

Para suporte técnico ou dúvidas sobre a instalação, consulte:

1. Logs dos containers
2. Documentação do Docker
3. Issues do repositório Git
4. Documentação da API FIPE

