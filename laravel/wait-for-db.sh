#!/bin/sh

# 1) Espera o MySQL ficar pronto via PDO
until php -r "new PDO('mysql:host=db;dbname=carteira','carteira','secret');" > /dev/null 2>&1; do
  echo "⏳ Aguardando banco de dados (PDO)..."
  sleep 2
done

echo "✔ Banco disponível!"

# 2) Roda migrations
php artisan migrate --force
echo "✔ Migrations concluídas!"

# 3) Inicia o servidor Laravel em background
php artisan serve --host=0.0.0.0 --port=8000 &

# 4) Mensagens finais
echo
echo "🚀 API Laravel rodando!"
echo "📖 Documentação Swagger: http://localhost:8000/api/docs"
echo

# 5) Mantém o script vivo enquanto o serve estiver rodando
wait
