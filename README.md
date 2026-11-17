<div align="center">

# 🛡️ Simple Web CTF Lab
### Plataforma Moderna de Capture The Flag & Laboratório Web de Cibersegurança (CyberPulse)

![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite_3-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-Apache_2.0-blue?style=for-the-badge&logo=apache)

<p align="center">
  Um laboratório prático, autocontido e moderno de <strong>Capture The Flag (CTF)</strong> focado nas principais vulnerabilidades web da <strong>OWASP Top 10</strong>, pronto para execução em <strong>Docker Apache + PHP</strong>.
</p>

[Visão Geral](#-visão-geral) •
[Ferramentas & Recursos](#-ferramentas-que-o-ctf-oferece) •
[Passo a Passo](#-passo-a-passo-para-executar) •
[Matriz de Desafios](#-matriz-de-desafios-owasp-top-10) •
[Write-Up & Soluções](#-write-up-e-guia-de-resolução) •
[Estrutura](#-estrutura-do-projeto) •
[Solução de Problemas](#-solução-de-problemas-troubleshooting)

</div>

---

## 📖 Visão Geral

O **CyberPulse CTF Lab** foi projetado para estudantes, desenvolvedores e profissionais de cibersegurança que desejam aprender tanto a perspectiva **ofensiva (Red Team / Pentest)** quanto a **defensiva (Blue Team / DevSecOps & Secure Coding)**. 

Diferente de laboratórios legados, o CyberPulse possui uma interface moderna (*Cyber Hacker Dark Mode*), retorno audiovisual em tempo real, validação automatizada de flags, dicas progressivas e exemplos em código de **como mitigar cada falha** na vida real.

---

## 🧰 Ferramentas que o CTF Oferece

A plataforma vai além de simples páginas vulneráveis; ela entrega uma suíte completa de utilitários para treinamento:

1. 🖥️ **Dashboard Interativo & Matriz de Desafios**:
   - Cards com categorização por OWASP Top 10, pontuação dinâmica, nível de dificuldade e status de conclusão (`PENDENTE` / `RESOLVIDO`).
2. 🚩 **Sistema Dinâmico de Validação de Flags**:
   - **Modal Global de Submissão** acessível no topo de qualquer página.
   - **Validação Local nos Cards** para envio rápido.
   - Endpoint REST (`/api/submit_flag.php`) com verificação instantânea.
3. 🔊 **Feedback Audiovisual Nativo (Zero Dependências Externas)**:
   - Sintetizador sonoro de sucesso e erro implementado via **Web Audio API** (funciona 100% offline).
   - Efeito visual de chuva de confetes dinâmico via HTML5 Canvas.
4. 📊 **Placar e Barra de Progresso em Tempo Real**:
   - Totalizador de pontuação e desafios resolvidos persistido na sessão do navegador.
   - Barra de evolução visual em gradiente neon.
5. 💡 **Sistema de Dicas Progressivas (2 Níveis por Desafio)**:
   - Nível 1: Dica conceitual para guiar o raciocínio sem estragar o aprendizado.
   - Nível 2: Dica prática de sintaxe ou payload para destravar o participante.
6. 🛡️ **Módulo Educacional Defensivo ("Como Mitigar")**:
   - Código PHP seguro recomendado (Prepared Statements, contextual encoding, sanitização de shell, JWT/sessões).
   - Diretivas e cabeçalhos de segurança recomendados para servidores Apache (CSP, restrições `.htaccess`).
7. 🔄 **Ferramenta de Restauração com 1 Clique (Lab Reset)**:
   - Restaura instantaneamente o banco de dados SQLite e redefine o progresso do usuário via API (`/api/reset.php`).
8. 💻 **Terminal Emulado com Scanlines**:
   - Janela de console no desafio de Command Injection simulando a saída de comandos do SO Linux em tempo real.
9. 🍪 **Inspetor & Manipulador de Tokens/Cookies**:
   - Ferramenta visual embutida para decodificar, editar e injetar tokens Base64/JSON sem depender obrigatoriamente de extensões de navegador.
10. 🐳 **Ambiente Contido e Seguro em Docker**:
    - Apache 2.4 com `mod_rewrite`, PHP 8.2, SQLite 3 e utilitários de rede (`iputils-ping`) isolados da máquina hospedeira.

---

## 🚀 Passo a Passo para Executar

### Pré-requisitos
Certifique-se de ter instalado em sua máquina:
- [Docker](https://docs.docker.com/get-docker/) (v20+ recomendado)
- [Docker Compose](https://docs.docker.com/compose/) (v2+)

---

### 1. Clonar o Repositório
```bash
git clone https://github.com/PedroCarlos30/simple-web-ctf.git
cd simple-web-ctf
```

### 2. Inicializar o Ambiente

#### Opção A: Script Automático (Recomendado no Linux/macOS)
O script `./start.sh` verifica os sockets do Docker, sobe o contêiner e garante as permissões da pasta do SQLite:
```bash
chmod +x start.sh
./start.sh
```

#### Opção B: Via Docker Compose
```bash
docker compose up -d --build
```

### 3. Acessar a Plataforma
Abra seu navegador e acesse:
👉 **[http://localhost:8080](http://localhost:8080)**

---

### 🛑 Comandos Úteis do Dia a Dia

- **Visualizar logs do Apache/PHP em tempo real:**
  ```bash
  docker compose logs -f
  ```
- **Acessar o terminal interativo dentro do contêiner:**
  ```bash
  docker exec -it ctf_cyberpulse /bin/bash
  ```
- **Parar o laboratório:**
  ```bash
  docker compose down
  ```
- **Reiniciar o contêiner reconstruindo a imagem:**
  ```bash
  docker compose up -d --build --force-recreate
  ```

---

## 🧩 Matriz de Desafios (OWASP Top 10)

| ID | Desafio | Categoria OWASP | Dificuldade | Pontos | Conceito Praticado |
|:---:|---|---|:---:|:---:|---|
| **01** | **Reconhecimento & Vazamento** | Reconnaissance / Info Leak | `Fácil` | 50 | Enumeração de `robots.txt`, inspeção de código-fonte e backups esquecidos (`.bak`). |
| **02** | **SQL Injection: Bypass de Login** | A03:2021 - Injection | `Fácil` | 100 | Quebra de lógica SQL em SQLite via concatenação direta (`' OR 1=1 --`). |
| **03** | **Cross-Site Scripting (XSS)** | A03:2021 - Injection (Client) | `Médio` | 100 | Exploração de XSS Refletido em busca e XSS Armazenado em mural de mensagens. |
| **04** | **IDOR: Controle Quebrado** | A01:2021 - Broken Access Control | `Médio` | 150 | Manipulação de identificadores em parâmetros de consulta (`?user_id=`). |
| **05** | **Local File Inclusion (LFI)** | A01:2021 - Path Traversal | `Médio` | 150 | Travessia de diretórios (`../../`) e leitura de arquivos confidenciais do servidor. |
| **06** | **Command Injection (RCE)** | A03:2021 - Injection (OS) | `Difícil` | 200 | Encadeamento de comandos shell no Linux através de utilitário de diagnóstico de rede. |
| **07** | **Adulteração de Cookies & Sessão** | A07:2021 - Auth Failures | `Médio` | 150 | Elevação de privilégios via decodificação e adulteração de cookie Base64 (`role: admin`). |

**Pontuação Máxima Possível:** `900 Pontos`

---

## 📖 Write-Up e Guia de Resolução

<details>
<summary><strong>🔍 Desafio 1: Reconhecimento & Vazamento de Código (50 pts)</strong></summary>

- **Cenário**: Desenvolvedores deixaram comentários e rotas de backup expostas publicamente.
- **Vetor de Ataque**:
  1. Acesse `http://localhost:8080/robots.txt` no navegador.
  2. Localize a rota desautorizada `/challenges/backup_admin_2026.bak.php`.
  3. Acesse essa URL para ler as credenciais e a flag de reconhecimento.
- **Flag**: `FLAG{r3c0n_m4st3r_d1sc0v3ry}`
- **Correção**: Proibir backups em diretórios web públicos e configurar bloqueios de extensões sensíveis no Apache.
</details>

<details>
<summary><strong>💉 Desafio 2: SQL Injection: Bypass de Autenticação (100 pts)</strong></summary>

- **Cenário**: O login interpola diretamente entradas do usuário na consulta SQLite:
  `SELECT * FROM users WHERE username = '$username' AND password = '$password'`
- **Vetor de Ataque**:
  1. No campo **Usuário**, insira: `' OR 1=1 --` ou `admin' --`
  2. No campo **Senha**, insira qualquer valor.
  3. O delimitador `--` comenta o teste da senha e o retorno é autenticado como o primeiro usuário (`admin`), exibindo a flag.
- **Flag**: `FLAG{sql1_byp4ss_l0g1n_succ3ss}`
- **Correção**: Utilizar *Prepared Statements* com PDO (`$stmt->prepare(...)` e parâmetros `:user`).
</details>

<details>
<summary><strong>⚡ Desafio 3: Cross-Site Scripting (XSS Reflected & Stored) (100 pts)</strong></summary>

- **Cenário**: Textos de busca e comentários são injetados no DOM sem sanitização de entidades HTML.
- **Vetor de Ataque**:
  1. Na barra de pesquisa ou no formulário do mural de recados, injete:
     ```html
     <script>alert(1)</script>
     ```
     ou
     ```html
     <img src=x onerror=alert(1)>
     ```
  2. A execução do JavaScript arbitrário é detectada pelo ambiente, que confirma a exploração e exibe a flag.
- **Flag**: `FLAG{xss_4l3rt_b0und4ry_br34k}`
- **Correção**: Tratar dados com `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` e configurar cabeçalhos `Content-Security-Policy (CSP)`.
</details>

<details>
<summary><strong>🚪 Desafio 4: IDOR - Insecure Direct Object Reference (150 pts)</strong></summary>

- **Cenário**: O endpoint de perfil confia cegamente no identificador numérico enviado via GET (`?user_id=1`).
- **Vetor de Ataque**:
  1. Observe que seu usuário inicial é o estagiário (`user_id=1`).
  2. Modifique o parâmetro para `?user_id=2` e em seguida `?user_id=3`.
  3. O perfil de ID 3 pertence ao CEO da organização e expõe anotações sigilosas com a flag.
- **Flag**: `FLAG{1d0r_pr1v1l3g3_3sc4l4t10n}`
- **Correção**: Implementar checagem de autorização no servidor baseada no ID da sessão autenticada.
</details>

<details>
<summary><strong>📂 Desafio 5: Local File Inclusion (LFI) & Path Traversal (150 pts)</strong></summary>

- **Cenário**: A aplicação inclui arquivos dinamicamente concatenando caminhos:
  `include(__DIR__ . '/pages/' . $_GET['doc']);`
- **Vetor de Ataque**:
  1. Utilize o operador de diretório pai (`../`) para escapar da pasta `/pages/`:
     `http://localhost:8080/challenges/lfi.php?doc=../../secret_flag_system.txt`
  2. Como prova de conceito em servidores Linux, teste também: `?doc=/etc/passwd`.
- **Flag**: `FLAG{lfi_tr4v3rs4l_s3cr3t_f1l3}`
- **Correção**: Usar *whitelist* de arquivos estritos ou sanitizar a entrada com `basename()`.
</details>

<details>
<summary><strong>💻 Desafio 6: Command Injection / RCE no Host (200 pts)</strong></summary>

- **Cenário**: A ferramenta de diagnóstico concatena entradas no utilitário de rede do Linux sem validação:
  `shell_exec("ping -c 2 " . $host);`
- **Vetor de Ataque**:
  1. No campo de IP, use operadores de encadeamento de comandos do Linux (`;`, `&&` ou `|`):
     ```bash
     127.0.0.1; cat ../secret_flag_system.txt
     ```
     ou
     ```bash
     127.0.0.1; ls -la ..
     ```
  2. O terminal renderizará o conteúdo do arquivo confidencial contendo a flag RCE.
- **Flag**: `FLAG{rce_c0mm4nd_1nj3ct10n_pwn3d}`
- **Correção**: Validar formato de IP (`filter_var(..., FILTER_VALIDATE_IP)`) e tratar argumentos com `escapeshellarg()`.
</details>

<details>
<summary><strong>🍪 Desafio 7: Adulteração de Cookies & Sessão Criptográfica (150 pts)</strong></summary>

- **Cenário**: O cofre valida autorização através do cookie `auth_token`, codificado em Base64 fraco:
  `{"user":"guest","role":"guest"}`
- **Vetor de Ataque**:
  1. No inspetor de cookies embutido na página (ou no DevTools `F12 -> Application -> Cookies`), decodifique a string.
  2. Modifique o valor de `"role": "guest"` para `"role": "admin"`.
  3. Codifique novamente em Base64 e atualize o cookie no navegador.
  4. Recarregue a página para desbloquear o cofre VIP e ler a chave mestre.
- **Flag**: `FLAG{c00k13_m4n1pul4t10n_adm1n}`
- **Correção**: Armazenar privilégios na sessão do lado do servidor ou assinar os dados com tokens criptográficos robustos (JWT com chave secreta HMAC-SHA256).
</details>

---

## 📁 Estrutura do Projeto

```
.
├── Dockerfile                  # Construção da imagem PHP 8.2 + Apache + SQLite
├── docker-compose.yml          # Definição do serviço contêiner na porta 8080
├── start.sh                    # Script de inicialização rápida com detecção de ambiente
├── README.md                   # Documentação completa
└── src/                        # Código-fonte da aplicação
    ├── index.php               # Dashboard principal dinâmico (PHP)
    ├── index.html              # Interface inicial estática de compatibilidade
    ├── config.php              # Configuração do SQLite, flags, regras e sessão
    ├── robots.txt              # Alvo do desafio de reconhecimento
    ├── secret_flag_system.txt  # Alvo de arquivos secretos (LFI / RCE)
    ├── api/                    # Endpoints REST (submit_flag, reset, idor)
    ├── assets/                 # Estilos (custom.css) e scripts (confetti, app.js)
    ├── components/             # Componentes modulares (header e footer)
    └── challenges/             # Aplicações vulneráveis dos 7 desafios
```

---

## 🔧 Solução de Problemas (Troubleshooting)

### A porta 8080 já está em uso
Se a porta `8080` estiver ocupada por outro serviço em sua máquina, edite o arquivo `docker-compose.yml` e altere a porta mapeada à esquerda:
```yaml
ports:
  - "8081:80"  # Agora responderá em http://localhost:8081
```

### O banco de dados SQLite não salva alterações
O script `start.sh` ajusta as permissões automaticamente. Caso esteja executando manualmente, certifique-se de conceder permissão na pasta de dados:
```bash
mkdir -p src/data && chmod -R 777 src/data
```

### Erro de conexão com o socket do Docker no Linux
Se o Docker Desktop não estiver em execução, utilize o socket nativo do Linux:
```bash
docker --context default compose up -d --build
```

---

## ⚖️ Licença e Uso Ético

Este projeto foi desenvolvido **exclusivamente para fins educacionais, de pesquisa e treinamento em segurança da informação**. As vulnerabilidades foram implementadas propositalmente dentro de um ambiente seguro e isolado em contêiner Docker. 

Distribuído sob a licença [Apache 2.0](LICENSE).
