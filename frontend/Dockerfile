# Mude APENAS esta linha:
FROM node:22-bullseye-slim

# O resto fica igual ao seu arquivo atual
RUN apt-get update && apt-get install -y \
    git \
    python3 \
    make \
    g++ \
    curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN groupadd -r nodejs && useradd -r -g nodejs vue

RUN chown -R vue:nodejs /app
USER vue

EXPOSE 3000

ENV NODE_ENV=development
ENV VITE_HOST=0.0.0.0
ENV VITE_PORT=3000

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:3000 || exit 1

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]