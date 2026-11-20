<div align="center">

# 🛡️ Simple Web CTF Lab
### Plataforma Moderna de Capture The Flag & Laboratório Web de Cibersegurança (CyberPulse v3.0)

![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite_3-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS_Local-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![OWASP Top 10](https://img.shields.io/badge/OWASP_Top_10-9_Desafios-orange?style=for-the-badge)
![License](https://img.shields.io/badge/License-Apache_2.0-blue?style=for-the-badge&logo=apache)

<p align="center">
  Um laboratório prático, autocontido, moderno e <strong>100% offline</strong> de <strong>Capture The Flag (CTF)</strong> focado nas principais vulnerabilidades web da <strong>OWASP Top 10</strong>, pronto para execução em <strong>Docker (Apache + PHP 8.2 + SQLite 3)</strong>.
</p>

[Visão Geral](#-visão-geral) •
[Novidades da Versão 3.0](#-novidades-da-versão-30) •
[Recursos & Funcionalidades](#-ferramentas-que-o-ctf-oferece) •
[Como Executar](#-passo-a-passo-para-executar) •
[Matriz de Desafios](#-matriz-de-desafios-owasp-top-10) •
[Write-Up & Guia de Resolução](#-write-up-e-guia-de-resolução) •
[Estrutura do Projeto](#-estrutura-do-projeto) •
[Troubleshooting](#-solução-de-problemas-troubleshooting)

</div>

---

## 📖 Visão Geral

O **CyberPulse CTF Lab** foi projetado para estudantes, desenvolvedores, pesquisadores e profissionais de cibersegurança que desejam dominar tanto a perspectiva **ofensiva (Red Team / Pentest)** quanto a **defensiva (Blue Team / DevSecOps & Secure Coding)**.

Diferente de laboratórios legados, o CyberPulse possui:
- Interface moderna com estética *Cyber Hacker Dark Mode*.
- Retorno audiovisual em tempo real gerado nativamente pelo navegador via **Web Audio API**.
- Validação dinâmica de flags por modal global e em cada card de desafio.
- Sistema de **dicas progressivas com desbloqueio dinâmico**.
- Módulo educacional com snippets de **código PHP seguro e diretivas Apache** para mitigar cada vulnerabilidade na vida real.
- Execução isolada e autocontida em contêiner Docker.

---

## ⚡ Novidades da Versão 3.0

1. **Expansão para 9 Desafios OWASP Top 10 (+350 pontos)**:
   - Adicionado **Desafio 08: SSRF (Server-Side Request Forgery)** contra serviços internos de metadados.
   - Adicionado **Desafio 09: Brute Force & Enumeração de Usuários** com análise de timing e mensagens de erro diferenciadas.
   - Pontuação máxima expandida de 900 para **1.250 Pontos**.
2. **Ambiente 100% Offline (Zero Dependências de CDN Externa)**:
   - TailwindCSS e FontAwesome 6.4.0 servidos localmente dentro do contêiner.
   - Funciona perfeitamente em redes corporativas com proxy restrito ou ambientes sem conexão à internet.
3. **Identidade Sonora CyberPulse Sintetizada**:
   - Novo pulso cibernético tático (*CyberPulse Click*) sintetizado via Web Audio API (snap mecânico em 2.6 kHz + sweep FM futurista + sub-punch de baixa latência).
4. **Persistência Criptografada e Segurança de Sessão**:
   - Progresso armazenado via cookie seguro assinado com **HMAC-SHA256**.
   - Proteção contra **CSRF** em ações críticas de laboratório (reset do banco de dados).
   - Diretivas de segurança do Apache no `.htaccess` (`DirectoryIndex`, bloqueio de acesso a `.db`, `.env`, `.log` e charset UTF-8 universal).
5. **Decoy Realista de API**:
   - Rota `/api/secret/` com resposta JSON autêntica (`403 Forbidden` / bypass restrito por localhost via SSRF).

---

## 🧰 Ferramentas que o CTF Oferece

1. 🖥️ **Dashboard Interativo & Matriz de 9 Desafios**:
   - Cards categorizados por vulnerabilidade OWASP Top 10, pontuação dinâmica, nível de dificuldade e status de conclusão (`PENDENTE` / `RESOLVIDO`).
2. 🚩 **Sistema Dinâmico de Validação de Flags**:
   - **Modal Global de Submissão** acessível no topo de qualquer página através do botão `Submeter Flag`.
   - **Validação Local nos Cards** para envio rápido direto no card do desafio.
   - Endpoint REST (`/api/submit_flag.php`) com verificação instantânea.
3. 🔊 **Feedback Audiovisual Nativo (Web Audio API + Canvas)**:
   - Sintetizador de áudio de pulso cibernético, som de acerto vitorioso e som de recusa implementados sem arquivos externos de áudio.
   - Efeito visual de chuva de confetes dinâmico via HTML5 Canvas.
4. 📊 **Placar e Barra de Progresso em Tempo Real**:
   - Totalizador de pontuação e desafios resolvidos persistido via cookie HMAC-SHA256 de 30 dias.
   - Barra de evolução visual em gradiente neon com percentual concluído.
5. 💡 **Sistema de Dicas Progressivas (2 Níveis por Desafio)**:
   - Nível 1: Dica conceitual para guiar o raciocínio sem entregar a resposta.
   - Nível 2: Dica prática com payload e técnica, desbloqueada após a primeira tentativa.
6. 🛡️ **Módulo Educacional Defensivo ("Como Mitigar")**:
   - Código PHP seguro recomendado (Prepared Statements, contextual encoding, validação de IP, restrições no Apache e rate limiting).
   - Cabeçalhos de segurança recomendados para servidores Apache (CSP, restrições no `.htaccess`).
7. 🔄 **Ferramenta de Restauração com 1 Clique (Lab Reset)**:
   - Restaura instantaneamente o banco de dados SQLite e redefine o progresso do usuário via API (`/api/reset.php`) protegida por token CSRF.
8. 💻 **Terminal Emulado com Scanlines**:
   - Janela de console no desafio de Command Injection simulando a saída de comandos do SO Linux em tempo real.
9. 🍪 **Inspetor & Manipulador de Tokens/Cookies**:
   - Ferramenta visual embutida para decodificar, editar e injetar tokens Base64/JSON diretamente pelo navegador.
10. 🐳 **Ambiente Contido e Seguro em Docker**:
    - Apache 2.4 com `mod_rewrite` e `mod_headers`, PHP 8.2 com SQLite 3, curl e utilitários de rede isolados da máquina hospedeira.

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
| **01** | **Reconhecimento & Vazamento** | Reconnaissance / Info Leak | `Fácil` | 50 | Enumeração de `robots.txt`, inspeção de código-fonte e backups desprotegidos (`.bak`). |
| **02** | **SQL Injection: Bypass de Login** | A03:2021 - Injection | `Fácil` | 100 | Quebra de lógica SQL em SQLite via concatenação direta (`' OR 1=1 --`). |
| **03** | **Cross-Site Scripting (XSS)** | A03:2021 - Injection (Client) | `Médio` | 100 | Exploração de XSS Refletido em busca e XSS Armazenado em mural de mensagens. |
| **04** | **IDOR: Controle Quebrado** | A01:2021 - Broken Access Control | `Médio` | 150 | Manipulação de identificadores em parâmetros de consulta (`?user_id=`). |
| **05** | **Local File Inclusion (LFI)** | A01:2021 - Path Traversal | `Médio` | 150 | Travessia de diretórios (`../../`) e leitura de arquivos confidenciais do servidor. |
| **06** | **Command Injection (RCE)** | A03:2021 - Injection (OS) | `Difícil` | 200 | Encadeamento de comandos shell no Linux através de utilitário de diagnóstico de rede. |
| **07** | **Adulteração de Cookies & Sessão** | A07:2021 - Auth Failures | `Médio` | 150 | Elevação de privilégios via decodificação e adulteração de cookie Base64 (`role: admin`). |
| **08** | **SSRF: Falsificação de Requisição** | A10:2021 - Server-Side Request Forgery | `Difícil` | 200 | Forçar o servidor a requisitar endpoints e serviços internos inacessíveis externamente. |
| **09** | **Brute Force & Enumeração de Usuários** | A07:2021 - Identification & Auth Failures | `Médio` | 150 | Enumeração de usuários por mensagens de erro distintas e quebra de senha sem rate limit. |

**Pontuação Máxima Possível:** `1.250 Pontos`

---

## 📖 Write-Up e Guia de Resolução

<details>
<summary><strong>🔍 Desafio 1: Reconhecimento & Vazamento de Código (50 pts)</strong></summary>

- **Cenário**: Desenvolvedores deixaram comentários e rotas de backup expostas publicamente.
- **Vetor de Ataque**:
  1. Acesse `http://localhost:8080/robots.txt` no navegador.
  2. Localize a rota desautorizada `/challenges/backup_admin_2026.bak`.
  3. Acesse essa URL para ler as credenciais de desenvolvimento e a flag.
- **Flag**: `FLAG{r3c0n_m4st3r_d1sc0v3ry}`
- **Correção**: Proibir backups em diretórios públicos do servidor web e configurar o Apache para negar acesso a extensões como `.bak`, `.old`, `.swp`.
</details>

<details>
<summary><strong>💉 Desafio 2: SQL Injection: Bypass de Autenticação (100 pts)</strong></summary>

- **Cenário**: O portal de login interpola entradas do usuário diretamente na query SQL sem sanitização:
  `SELECT * FROM users WHERE username = '$username' AND password = '$password'`
- **Vetor de Ataque**:
  1. No campo **Usuário**, insira: `' OR 1=1 --` ou `admin' --`
  2. No campo **Senha**, digite qualquer valor.
  3. O delimitador `--` comenta a verificação de senha e autentica como o primeiro registro (`admin`), exibindo a flag.
- **Flag**: `FLAG{sql1_byp4ss_l0g1n_succ3ss}`
- **Correção**: Utilizar *Prepared Statements* com PDO (`$stmt->prepare(...)` e parâmetros nomeados `:user`).
</details>

<details>
<summary><strong>⚡ Desafio 3: Cross-Site Scripting (XSS Reflected & Stored) (100 pts)</strong></summary>

- **Cenário**: Textos de busca e comentários são injetados no DOM sem sanitização de caracteres especiais HTML.
- **Vetor de Ataque**:
  1. Na barra de pesquisa ou no formulário do mural de recados, injete:
     ```html
     <script>alert(1)</script>
     ```
     ou
     ```html
     <img src=x onerror=alert(1)>
     ```
  2. A execução do JavaScript arbitrário é detectada pelo laboratório e a flag é exibida no código-fonte ou no painel.
- **Flag**: `FLAG{xss_4l3rt_b0und4ry_br34k}`
- **Correção**: Tratar dados de saída com `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` e configurar cabeçalhos `Content-Security-Policy (CSP)`.
</details>

<details>
<summary><strong>🚪 Desafio 4: IDOR - Insecure Direct Object Reference (150 pts)</strong></summary>

- **Cenário**: O endpoint de perfil aceita um identificador numérico via GET (`?user_id=1`) sem validar permissões de acesso.
- **Vetor de Ataque**:
  1. O usuário inicial é o estagiário (`user_id=1`).
  2. Modifique o parâmetro da URL para `?user_id=2` e em seguida `?user_id=3`.
  3. O perfil de ID 3 pertence ao CEO da organização e revela anotações sigilosas com a flag.
- **Flag**: `FLAG{1d0r_pr1v1l3g3_3sc4l4t10n}`
- **Correção**: Implementar checagem rigorosa de autorização no backend validando se o usuário autenticado na sessão tem permissão para acessar o ID requisitado.
</details>

<details>
<summary><strong>📂 Desafio 5: Local File Inclusion (LFI) & Path Traversal (150 pts)</strong></summary>

- **Cenário**: O visualizador inclui arquivos dinamicamente concatenando caminhos:
  `include(__DIR__ . '/pages/' . $_GET['doc']);`
- **Vetor de Ataque**:
  1. Utilize o operador de diretório pai (`../`) para escapar da pasta `/pages/`:
     `http://localhost:8080/challenges/lfi.php?doc=../../secret_flag_system.txt`
  2. Como prova de conceito em ambientes Linux, teste também: `?doc=/etc/passwd`.
- **Flag**: `FLAG{lfi_tr4v3rs4l_s3cr3t_f1l3}`
- **Correção**: Usar *whitelist* estrita de arquivos permitidos ou sanitizar entradas com `basename()` e `realpath()`.
</details>

<details>
<summary><strong>💻 Desafio 6: Command Injection / RCE no Host (200 pts)</strong></summary>

- **Cenário**: A ferramenta de diagnóstico de rede concatena entradas no utilitário de sistema `ping` sem sanitização:
  `shell_exec("ping -c 2 " . $host);`
- **Vetor de Ataque**:
  1. No campo de IP, use operadores de encadeamento de comandos do shell (`;`, `&&` ou `|`):
     ```bash
     127.0.0.1; cat ../secret_flag_system.txt
     ```
     ou
     ```bash
     127.0.0.1; ls -la ..
     ```
  2. O terminal emulado renderizará o conteúdo do arquivo confidencial contendo a flag.
- **Flag**: `FLAG{rce_c0mm4nd_1nj3ct10n_pwn3d}`
- **Correção**: Validar formato de IP com `filter_var(..., FILTER_VALIDATE_IP)` e tratar argumentos com `escapeshellarg()`.
</details>

<details>
<summary><strong>🍪 Desafio 7: Adulteração de Cookies & Sessão Criptográfica (150 pts)</strong></summary>

- **Cenário**: O cofre valida autorização através do cookie `auth_token`, codificado em Base64 fraco:
  `{"user":"guest","role":"guest"}`
- **Vetor de Ataque**:
  1. No editor embutido na página (ou no DevTools `F12 -> Application -> Cookies`), decodifique o token Base64.
  2. Altere o valor de `"role": "guest"` para `"role": "admin"`.
  3. Codifique novamente em Base64 e aplique o novo cookie.
  4. Recarregue a página para desbloquear o cofre VIP e ler a flag.
- **Flag**: `FLAG{c00k13_m4n1pul4t10n_adm1n}`
- **Correção**: Armazenar privilégios na sessão do lado do servidor ou assinar os dados com tokens criptográficos robustos (JWT com HMAC-SHA256).
</details>

<details>
<summary><strong>🌐 Desafio 8: SSRF - Server-Side Request Forgery (200 pts)</strong></summary>

- **Cenário**: A ferramenta corporativa de verificação de links faz requisições HTTP a partir do servidor sem filtrar IPs privados ou redes internas (`127.0.0.1`, `localhost`).
- **Vetor de Ataque**:
  1. Se você tentar acessar `http://localhost:8080/api/secret/` diretamente pelo seu navegador, receberá um `403 Forbidden`.
  2. Acesse a ferramenta em `/challenges/ssrf.php` e envie no campo de URL:
     ```
     http://127.0.0.1/api/secret/?token=internal
     ```
  3. A requisição será executada pelo próprio servidor a partir da rede local (localhost), liberando o acesso ao serviço interno e retornando a flag.
- **Flag**: `FLAG{ssrf_1nt3rn4l_s3rv1c3_pwn3d}`
- **Correção**: Bloquear requisições para endereços de loopback (`127.0.0.0/8`, `::1`) e faixas privadas RFC 1918 (`10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`), além de desabilitar redirecionamentos com bibliotecas seguras (ex: SafeCURL).
</details>

<details>
<summary><strong>🔑 Desafio 9: Brute Force & Enumeração de Usuários (150 pts)</strong></summary>

- **Cenário**: O portal de folha de pagamento retorna mensagens de erro distintas para usuário inexistente vs. senha incorreta, e não implementa rate limiting por IP ou conta.
- **Vetor de Ataque**:
  1. Acesse `/challenges/bruteforce.php`.
  2. Teste diferentes nomes de usuário. O sistema retorna *"Usuário não encontrado no sistema"* quando o usuário não existe, e *"Senha incorreta para este usuário"* quando o usuário existe.
  3. Identifique que o usuário `carlos` existe no sistema.
  4. Teste as senhas da wordlist exibida na página (ataque de dicionário) até submeter `password123`.
  5. O login é concluído com sucesso e o painel exibe a flag.
- **Flag**: `FLAG{brut3f0rc3_n0_r4t3_l1m1t}`
- **Correção**: Utilizar mensagens de erro genéricas (*"Usuário ou senha inválidos"*), aplicar rate limiting (máximo de 5 tentativas por janela de 15 minutos), exigir senhas fortes e implementar bloqueio progressivo ou CAPTCHA.
</details>

---

## 📁 Estrutura do Projeto

```
.
├── Dockerfile                  # Construção da imagem PHP 8.2 + Apache + SQLite + curl
├── docker-compose.yml          # Definição do serviço na porta 8080
├── start.sh                    # Script de inicialização rápida com detecção de ambiente
├── README.md                   # Documentação completa e Write-Up
├── LICENSE                     # Licença Apache 2.0
├── .gitignore                  # Arquivos ignorados pelo Git (banco de dados, logs, etc.)
└── src/                        # Código-fonte da aplicação
    ├── index.php               # Dashboard principal dinâmico (PHP)
    ├── config.php              # Configuração do SQLite, flags, HMAC-SHA256 e sessão
    ├── robots.txt              # Alvo de reconhecimento de rotas desautorizadas
    ├── secret_flag_system.txt  # Alvo de leitura de arquivos sigilosos (LFI / RCE)
    ├── .htaccess               # Diretivas do Apache (bloqueios, rewrites e charset)
    ├── api/                    # Endpoints REST (submit_flag, reset, idor, secret)
    │   ├── idor_user.php       # API vulnerável a IDOR
    │   ├── reset.php           # Reset completo do laboratório protegido por CSRF
    │   ├── submit_flag.php     # Validação centralizada de flags
    │   └── secret/index.php    # Serviço interno de metadados (alvo do SSRF)
    ├── assets/                 # Estilos, ícones e scripts offline
    │   ├── css/                # TailwindCSS local (min.js) e custom.css
    │   ├── fontawesome/        # FontAwesome 6.4.0 local (CSS e webfonts)
    │   └── js/                 # app.js (lógica e áudio Web Audio API) e confetti.min.js
    ├── components/             # Componentes reutilizáveis (header e footer)
    ├── data/                   # Diretório de persistência do SQLite (ctf.db)
    └── challenges/             # Aplicações vulneráveis dos 9 desafios
        ├── recon.php           # Desafio 01: Reconhecimento & Vazamento
        ├── backup_admin_2026.bak # Arquivo de backup exposto
        ├── sqli.php            # Desafio 02: SQL Injection
        ├── xss.php             # Desafio 03: Cross-Site Scripting (XSS)
        ├── idor.php            # Desafio 04: Insecure Direct Object Reference (IDOR)
        ├── lfi.php             # Desafio 05: Local File Inclusion (LFI)
        ├── cmdi.php            # Desafio 06: Command Injection (RCE)
        ├── crypto.php          # Desafio 07: Adulteração de Cookies & Sessão
        ├── ssrf.php            # Desafio 08: Server-Side Request Forgery (SSRF)
        ├── bruteforce.php      # Desafio 09: Brute Force & Enumeração
        └── pages/              # Páginas internas da documentação (alvo de LFI)
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
