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
   git clone https://github.com/seu-usuario/building-task-manager.git
   cd building-task-manager
   ```

2. Instale as dependências:
   ```bash
   composer install
   ```

3. Copie o arquivo de ambiente e configure as variáveis:
   ```bash
   cp .env.example .env
   ```

4. Configure o arquivo `.env` com suas credenciais de banco de dados:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=building_task_manager
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

5. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```

6. Execute as migrações:
   ```bash
   php artisan migrate
   ```

7. Opcionalmente, execute os seeders para popular o banco com dados de teste:
   ```bash
   php artisan db:seed
   ```

8. Gere uma chave para API tokens do Sanctum:
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

9. Inicie o servidor de desenvolvimento:
   ```bash
   php artisan serve
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
php artisan test
```

## Padrões de Codificação

Este projeto segue o padrão de codificação PSR-12. Para verificar o código, você pode executar:

```bash
./vendor/bin/phpcs --standard=PSR12 app/
```

## Contribuindo

1. Faça um fork do projeto
2. Crie sua branch de feature (`git checkout -b feature/nome-da-feature`)
3. Faça commit das suas alterações (`git commit -m 'Adiciona nova feature'`)
4. Faça push para a branch (`git push origin feature/nome-da-feature`)
5. Abra um Pull Request