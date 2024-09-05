FROM willwill/wait-for-it

RUN apt-get update && apt-get install -y curl

# Assume main project directory as docker context. See docker-compose.yaml
COPY .github/workflows/es-index-template.json es-index-template.json
