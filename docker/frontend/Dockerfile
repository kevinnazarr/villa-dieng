# Development runtime for the React/Vite frontend.
# Debian-based (glibc) so bind-mounted host node_modules stays compatible.
# Alpine (musl) breaks rolldown's platform-specific native binding.
FROM node:22-slim

WORKDIR /app

COPY docker/frontend/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 5173

ENTRYPOINT ["docker-entrypoint.sh"]
