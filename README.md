# Saúde-Atendimento: Sistema de Escalas e Atendimentos (Rio Claro RJ)

Este documento resume a conclusão do projeto de migração e deploy do sistema de escalas e atendimentos da Secretaria Municipal de Saúde de Rio Claro.

## 1. Visão Geral
O sistema foi reconstruído como uma aplicação monolítica em **PHP Puro**, utilizando **MySQL/MariaDB**, **Vanilla JavaScript** e **Bootstrap 5**. A arquitetura foi desenhada para ser leve, sem dependências de build externas, facilitando a manutenção e portabilidade.

## 2. Deploy em Produção
O sistema foi implantado com sucesso no servidor VPS da prefeitura (Hostinger, AlmaLinux 9).

- **Caminho Físico:** `/home/rioclaro.rj.gov.br/public_html/agendamento/`
- **URL Pública:** [https://rioclaro.rj.gov.br/agendamento/](https://rioclaro.rj.gov.br/agendamento/)
- **Painel Admin:** [https://rioclaro.rj.gov.br/agendamento/login.php](https://rioclaro.rj.gov.br/agendamento/login.php)

### 2.1. Estrutura de Subpasta
Para permitir a coexistência com o WordPress na raiz do site, o sistema foi adaptado para suportar caminhos relativos e dinâmicos:
- Implementação da variável `$baseUrl` no PHP para prefixar links e redirecionamentos.
- Injeção de `window.baseUrl` no JavaScript para chamadas de API (`fetch`).
- Configuração de caminhos baseada em detecção automática de URI.

### 2.2. Banco de Dados
- **Nome:** `saude_atendimento`
- **Acesso:** Configurado via `config/database.php`.
- **Segurança:** Senhas armazenadas com `bcrypt` (password_hash).

## 3. Funcionalidades Implementadas
- **Escalas de Atendimento:** Filtros por Unidade, Especialidade, Profissional e Tipo.
- **Relatório de Atendimentos:** Visualização pública de atendimentos realizados por período e unidade.
- **Gestão Administrativa:** CRUD completo de Médicos, Especialidades, Unidades, Escalas e Atendimentos.
- **Segurança Anti-bot:** Math Captcha nativo no login para prevenir ataques automatizados.
- **Identidade Visual:** Aplicação das cores institucionais e logos oficiais da Prefeitura de Rio Claro.

## 4. Credenciais e Acesso
- **Administrador:** `admin` / `admin123`
- **Recuperação:** Fluxo de contato direto com o setor de TI configurado em `recuperar_senha.php`.

## 5. Manutenção Futura
O código está versionado na branch `migracao-php` do repositório GitHub. Para atualizações futuras:
1. Realizar o `git push` das alterações locais.
2. No servidor, executar `git pull` dentro da pasta `/home/rioclaro.rj.gov.br/public_html/agendamento/`.

---
**Status do Projeto:** Concluído e em Produção.
**Data de Entrega:** 29 de Abril de 2026.
