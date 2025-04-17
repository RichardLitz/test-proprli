# Sistema de Gerenciamento de Tarefas Imobiliárias

Este projeto implementa uma API REST em Laravel 10 para gerenciar tarefas em edifícios, permitindo aos proprietários criar tarefas, atribuí-las a membros da equipe e acompanhar o progresso através de comentários.

## Requisitos do Sistema

- PHP 8.1 ou superior
- Composer
- MySQL 5.7 ou superior
- Laravel 10.x

## Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/RichardLitz/test-proprli.git
   
   ```

2. Copie o arquivo de ambiente e configure as variáveis:
   ```bash
   cp .env-local .env
   ```

3. Suba o container:
   ```bash
   docker-compose build
   docker-compose up -d
   ```

4. Instale as dependências:
   ```bash
   docker-compose exec app composer install
   ```

5. Execute as migrações:
   ```bash
   docker-compose exec app php artisan migrate
   
   docker-compose exec app php artisan optimize

   ```

6. Inicie o servidor de desenvolvimento:
   ```bash
  docker-compose exec app php artisan serve
   ```

## Estrutura da API

### Endpoints

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/buildings/{building}/tasks` | Lista as tarefas de um edifício com seus comentários |
| POST | `/api/tasks` | Cria uma nova tarefa |
| PATCH | `/api/tasks/{task}/status` | Atualiza o status de uma tarefa |
| POST | `/api/tasks/{task}/comments` | Adiciona um comentário a uma tarefa existente |

### Autenticação

A API usa Laravel Sanctum para autenticação. Todas as requisições devem incluir um token de autenticação no cabeçalho:

```
Authorization: Bearer seu_token_aqui
```

### Filtros Disponíveis

- `status`: Filtra por status da tarefa (`open`, `in_progress`, `completed`, `rejected`)
- `assigned_to`: Filtra por usuário atribuído (ID do usuário)
- `created_from` e `created_to`: Filtra por intervalo de datas de criação
- `due_date_from` e `due_date_to`: Filtra por intervalo de datas de vencimento

### Exemplo de Requisições

#### Listar tarefas de um edifício
```
GET /api/buildings/1/tasks?status=open&assigned_to=2
```

#### Criar uma nova tarefa
```
POST /api/tasks
Content-Type: application/json

{
  "building_id": 1,
  "title": "Manutenção no Elevador",
  "description": "Realizar manutenção preventiva no elevador",
  "assigned_to": 2,
  "status": "open",
  "due_date": "2023-05-30"
}
```

#### Atualizar o status de uma tarefa
```
PATCH /api/tasks/1/status
Content-Type: application/json

{
  "status": "in_progress"
}
```

#### Adicionar um comentário a uma tarefa
```
POST /api/tasks/1/comments
Content-Type: application/json

{
  "content": "Técnico agendado para quinta-feira às 14h"
}
```

## Estrutura do Banco de Dados

### Tabelas Principais

- **buildings**: Armazena informações dos edifícios
- **users**: Armazena usuários do sistema (proprietários e membros da equipe)
- **tasks**: Armazena as tarefas relacionadas aos edifícios
- **comments**: Armazena comentários das tarefas
- **building_user**: Tabela pivot para relacionar usuários a edifícios

### Relacionamentos

- Um edifício pode ter múltiplos usuários (proprietários, gerentes, equipe)
- Um edifício pode ter múltiplas tarefas
- Uma tarefa pertence a um edifício e pode ter vários comentários
- Uma tarefa é criada por um usuário e pode ser atribuída a um usuário
- Um comentário pertence a uma tarefa e é criado por um usuário

## Executando Testes

Execute os testes unitários e de integração com o comando:

```bash
docker-compose exec app php artisan test
```

