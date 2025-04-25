# Carteira Digital — Front-end React

Este é o front-end em React da **Carteira Digital**, consumindo a API em Laravel que implementa um sistema de depósito, transferência e reversão de transações, com autenticação via Sanctum.

---

## 🚀 Funcionalidades

1. **Autenticação**  
   - **Registro** de usuário com validação de campos (nome, e-mail, senha)  
   - **Login** com e-mail e senha, recebendo token Bearer  
   - **Logout** que revoga o token via API e limpa o estado local  

2. **Proteção de rotas**  
   - Uso de **PrivateRoute** para bloquear acesso a páginas internas quando não autenticado  

3. **Dashboard**  
   - Gráfico de **Saldo ao longo do tempo** (Line Chart)  
   - Gráfico de **Depósitos vs Transferências líquidas** (Bar Chart)  
   - Gráfico de **Distribuição de operações** (Pie Chart)  
   - Gráfico de **Fluxo de Caixa Diário** (Line Chart com área preenchida)  

4. **Depósito**  
   - Formulário com validação  
   - Botão bloqueado e spinner durante o envio  
   - Feedback de sucesso e redirecionamento ao histórico  

5. **Transferência**  
   - Formulário para escolher destinatário (lista de usuários)  
   - Exibição do saldo atual na página  
   - Validação de valor e seleção de usuário  
   - Botão bloqueado e spinner durante o envio  

6. **Histórico de Transações**  
   - Tabela listando data, tipo, valor, status  
   - **Ocultar/mostrar** valores com ícone de olho (FaEye/FaEyeSlash)  
   - Cores diferenciadas para **entradas** (verde) e **saídas** (vermelho), com sinal “–”  
   - Botão **Reverter** transações concluídas, com confirmação e spinner  

7. **Menu lateral (Sidebar)**  
   - Itens de navegação: Dashboard, Depósito, Transferir, Histórico  
   - Botão **Sair** que dispara logout via API e redireciona ao login  

8. **Cabeçalho (Header)**  
   - Dropdown com nome do usuário em caixa alta  
   - Link para Histórico  
   - Botão de Logout  

---

## 🛠 Instalação e uso

1. Clone este repositório:
   ```bash
   git clone <repo-url> carteira-digital-react
   cd carteira-digital-react
