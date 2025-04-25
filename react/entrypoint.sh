#!/bin/sh

# 1) Sobe o Vite em background, escutando em 0.0.0.0:3000
npm start -- --host 0.0.0.0 --port 3000 &


# 2) Mensagem final
echo
echo "🌐 Front-end React disponível em: http://localhost:3000"
echo

# 3) Mantém o container vivo até o Vite encerrar
wait
