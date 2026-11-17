#!/bin/bash
# Script de Inicialização Rápida - Simple Web CTF Lab

echo "=========================================================="
echo "      SIMPLE WEB CTF LAB - DOCKER APACHE INICIANDO        "
echo "=========================================================="

# Detectar contexto apropriado do Docker
DOCKER_CMD="docker compose"
if ! docker info > /dev/null 2>&1; then
    if docker --context default info > /dev/null 2>&1; then
        echo "[INFO] Usando contexto Docker padrão do sistema (unix:///var/run/docker.sock)..."
        DOCKER_CMD="docker --context default compose"
    fi
fi

echo "[1/3] Construindo e iniciando o contêiner Apache + PHP..."
$DOCKER_CMD up -d --build

echo "[2/3] Ajustando permissões de escrita para a pasta de dados SQLite..."
mkdir -p src/data
chmod -R 777 src/data 2>/dev/null || true

echo "[3/3] Verificando status do contêiner..."
$DOCKER_CMD ps

echo "=========================================================="
echo " [OK] CTF Lab está ativo e pronto para uso!"
echo " Acesse no seu navegador: http://localhost:8080"
echo " Para parar: $DOCKER_CMD down"
echo "=========================================================="

