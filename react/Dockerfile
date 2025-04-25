# carteira-digital/react/Dockerfile

FROM node:18-alpine

WORKDIR /app

# 1) Instala dependências
COPY package*.json ./
RUN npm install

# 2) Copia código
COPY . .

# 3) Copia e libera o entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 4) Expõe a porta onde o Vite vai rodar
EXPOSE 3000

# 5) Usa nosso script para iniciar o Vite + mensagem terminal
ENTRYPOINT ["entrypoint.sh"]
