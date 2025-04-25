
# Carteira Digital Full-Stack

  

Este projeto implementa uma API em Laravel e um front-end React (CoreUI + Vite), orquestrados via Docker Compose, para simular uma carteira financeira com depósitos, transferências e reversões.

  

---

  

## Pré-requisitos

  

- Docker & Docker Compose instalados

- Git

  

---

  

## 1. Clonar o repositório

  

```bash

git  clone  https://github.com/seu-usuario/carteira-digital.git

cd  carteira-digital
```

## 2.  Subir  os  containers

```bash

docker-compose  up  --build
```

API  Laravel:  http://localhost:8000

  

Documentação  Swagger:  http://localhost:8000/api/docs

  

Front-end  React:  http://localhost:3000

  

As  mensagens  de  status  irão  aparecer  no  terminal  de  cada  container.

  

## 3.  Estrutura  de  serviços

db:  MySQL

  

app:  Laravel (PHP 8.2  +  Sanctum  +  migrations  +  serve)

  

react:  Vite  +  React  +  CoreUI

  

## 4.  Fluxo  de  uso

 ### Registrar  usuários

 1. Acesse  a  UI  React  e  clique  em  Registrar (ou via  API  POST  /api/register).
 2. Crie  pelo  menos  dois  usuários (serão User  A  e  User  B) antes de testar transferências (Aproveite e faça a validação com e-mail errado, senhas diferentes ou nome em branco).
  

### Login

 1. Faça  login  na  UI  ou  em  POST  /api/login  para  receber  um  token  Bearer.

  

### Depósito

  

 1. Em  Depósito,  informe  um  valor  positivo  e  confirme.

  

(API:  POST  /api/deposit  com  body  {  "amount":  100.00  })

  

### Transferência

  

 1. Em  Transferir,  selecione  o  segundo  usuário (ID do  payee) e informe valor.

  

⚠️  É  necessário  ter  pelo  menos  2  contas  registradas  para  que  exista  payer  e  payee.

  

(API:  POST  /api/transfer  com  {  "payee_id":  2,  "amount":  50.00  })

  

### Histórico


 1. Veja  o  histórico  de  transações (API e  tabela  na  UI).

  

### Reversão


 1. Em  Histórico,  clique  em  Reverter  numa  transação  pendente (concluída).

  

(API:  POST  /api/reverse/{transactionId})

  

## Endpoints  principais (API)



Método  Rota  Descrição

 1. POST  /api/register  Registrar  usuário

 2. POST  /api/login  Autenticar  e  obter  token

 3. GET  /api/user  Dados  do  usuário  +  carteira

 4. POST  /api/logout  Revogar  tokens (logout)

 5. POST  /api/deposit  Depósito

 6. POST  /api/transfer  Transferência

 7. GET  /api/transactions  Listar  transações

 8. POST  /api/reverse/{id}  Reverter  transação

 9. GET  /api/docs  Swagger  UI

## Observações

### Ambiente

  

Variáveis  definidas  em  docker-compose.yml  e  .env  do  Laravel.

  

### Banco

  

MySQL  “db:3306”  com  usuário  carteira  /  senha  secret.

  

### Swagger

  

Gere  e  atualize  a  documentação  com  php  artisan  l5-swagger:generate.

  

## Pronto!  🚀

Sua  Carteira  Digital  Full-Stack  está  disponível  para  testes  e  uso.