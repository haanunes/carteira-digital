# Carteira Digital API

API RESTful para gerenciamento de carteira financeira, construída em Laravel 10 com Sanctum, incluindo funcionalidades de autenticação, depósito, transferência, histórico de transações e reversão.

---

## 🚀 Tecnologias

- **Laravel 10**
- **PHP 8.2**
- **MySQL**
- **Laravel Sanctum** (autenticação via token)
- **Docker Compose** (PHP-FPM, MySQL)
- **PHPUnit** (testes unitários, feature e integração)
- **L5-Swagger** (documentação OpenAPI)

---

## 📥 Instalação

1. Clone o repositório:

   ```bash
   git clone https://github.com/seu-usuario/carteira-digital.git
   cd carteira-digital
   ```

2. Copie o `.env.example` e ajuste as variáveis:

   ```bash
   cp .env.example .env
   # definir DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   ```

3. Gere a key do aplicativo:

   ```bash
   php artisan key:generate
   ```

4. Instale dependências PHP:

   ```bash
   composer install
   ```

5. (Opcional) Subir via Docker Compose:

   ```bash
   docker-compose up -d --build
   ```

6. Execute as migrations:

   ```bash
   php artisan migrate
   ```

7. Teste local com `php artisan serve` ou configure seu Nginx/Apache.

---

## 🔑 Endpoints API

Todos os endpoints estão sob a rota base `/api`. As rotas protegidas usam autenticação Bearer Token (Sanctum).

### Autenticação (`AuthController`)

| Método | Endpoint        | Ação                                    | Autenticação | Request Body                | Response                                      |
| ------ | --------------- | --------------------------------------- | ------------ | --------------------------- | --------------------------------------------- |
| POST   | `/api/register` | Cadastro de usuário + criação de wallet | Não          | `{ name, email, password }` | `{ message, user: { id, name, email }}` (201) |
| POST   | `/api/login`    | Gera token de acesso                    | Não          | `{ email, password }`       | `{ token }` (200)                             |
| GET    | `/api/user`     | Retorna dados do usuário autenticado    | Sim (Bearer) | —                           | `{ id, name, email, wallet, joined }` (200)   |
| POST   | `/api/logout`   | Revoga todos os tokens (logout)         | Sim (Bearer) | —                           | `{ message }` (200)                           |

### Transações (`TransactionController`)

| Método | Endpoint            | Ação                                                        | Autenticação | Request Body           | Response                                        |
| ------ | ------------------- | ----------------------------------------------------------- | ------------ | ---------------------- | ----------------------------------------------- |
| POST   | `/api/deposit`      | Depósito em wallet                                          | Sim          | `{ amount }`           | `{ message, transactionId, transaction }` (201) |
| POST   | `/api/transfer`     | Transferência entre usuários                                | Sim          | `{ payee_id, amount }` | `{ message, transactionId, transaction }` (201) |
| GET    | `/api/transactions` | Lista todas as transações do usuário                        | Sim          | —                      | `[{...}, {...}]` (200)                          |
| POST   | `/api/reverse/{id}` | Reversão de depósito ou transferência (route-model binding) | Sim          | —                      | `{ message, transactionId, reversal }` (201)    |

---

## 🧩 Arquitetura & Padrões

- **Controllers** finos: orquestram validação, chamam Services e formatam resposta.
- **FormRequest**: `DepositRequest`, `TransferRequest`, `LoginRequest`, `RegisterRequest` para validação e mensagens em português.
- **Service Layer**:
  - `AuthService`: registro, login, logout, recuperação de usuário via token.
  - `TransactionService`: deposit, transfer, reverse, history.
- **Resources**: `UserResource`, `TransactionResource` para transformação de JSON.
- **Exceptions Customizadas**: handler JSON para 401/404 em rotas API.
- **Route-Model Binding**: `reverse/{transaction}`.

---

## 🧪 Testes

- **Unitários** (`tests/Unit`): `TransactionServiceTest`, `AuthServiceTest`.
- **Feature** (`tests/Feature`): `AuthTest`, `TransactionTest` (fluxos básicos).
- **Integração** (`tests/Feature/IntegrationTest`): valida fluxo completo de ponta a ponta.

Execute todos:

```bash
php artisan test
```

---

## 📄 Documentação (Swagger)

1. Gere a spec e UI:
   ```bash
   php artisan l5-swagger:generate
   ```
2. Acesse:
   ```bash
   http://localhost:8000/api/docs
   ```
---

## 🐳 Docker Compose

Exemplo de serviço mínimo:
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: carteira
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
````


---

© 2025 — Desenvolvido por Hélder Nunes

