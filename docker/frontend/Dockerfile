# syntax=docker/dockerfile:1

FROM node:22-alpine AS frontend_deps
WORKDIR /app
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci

# Dev : serveur Vite, code monté en volume (node_modules conservé via volume anonyme).
FROM node:22-alpine AS frontend_dev
WORKDIR /app
COPY frontend/package.json frontend/package-lock.json ./
RUN npm install
COPY frontend/ ./
EXPOSE 80
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "80"]

FROM frontend_deps AS frontend_build
COPY frontend/ ./
RUN npm run build

FROM nginx:1.27-alpine AS frontend_prod
COPY docker/frontend/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=frontend_build /app/dist /usr/share/nginx/html
EXPOSE 80
