# Guia de Deploy: VPS Hostinger (AlmaLinux 9 + CyberPanel)

Como o sistema agora é nativo em **PHP e MySQL**, o processo de deploy no CyberPanel (OpenLiteSpeed) é extremamente simples. Você não precisa configurar portas de proxy (como no Node.js) nem rodar processos em segundo plano (PM2). O próprio servidor web já gerencia tudo nativamente.

Aqui está o passo a passo para colocar o sistema no ar:

## Passo 1: Preparar os arquivos
A forma mais fácil de levar os arquivos para o servidor é compactando a pasta local.
1. No seu Mac, abra a pasta `/Users/dalmovieiradasilva/agendamento`.
2. Selecione todos os arquivos (menos a pasta `.git` se ela estiver visível).
3. Clique com o botão direito e escolha **Comprimir**. Isso gerará um arquivo `.zip`.

## Passo 2: Criar o Banco de Dados no CyberPanel
1. Acesse o seu CyberPanel (geralmente `https://62.72.11.207:8090`).
2. No menu lateral, vá em **Databases** > **Create Database**.
3. Selecione o site `rioclaro.rj.gov.br`.
4. Preencha os campos:
   - **Database Name:** `agendamento` (ficará algo como `rioclaro_agendamento`)
   - **User Name:** `admin_agenda` (ficará algo como `rioclaro_admin_agenda`)
   - **Password:** Crie uma senha forte e anote-a.
5. Clique em **Create Database**.

## Passo 3: Importar a Estrutura (SQL)
1. Ainda no CyberPanel, vá em **Databases** > **PHPMYADMIN**.
2. Faça login se solicitado, e clique no banco de dados que você acabou de criar no menu à esquerda.
3. Clique na aba **Importar** (Import).
4. Envie o arquivo `database.sql` que criamos localmente.
5. Clique em **Executar** (Go).

## Passo 4: Upload dos Arquivos (File Manager)
1. No menu lateral do CyberPanel, vá em **Websites** > **List Websites**.
2. Encontre `rioclaro.rj.gov.br` e clique em **File Manager** (Gerenciador de Arquivos).
3. Navegue até a pasta `public_html`.
4. Clique no botão **New Folder** (Nova Pasta) no topo e crie uma pasta chamada `agendamento`.
5. Entre na pasta `agendamento`.
6. Use a função **Upload** no topo para enviar o arquivo `.zip` que você criou no Passo 1.
7. Após o upload, clique com o botão direito no `.zip` e escolha **Extract** (Extrair).

## Passo 5: Atualizar as Credenciais do Banco
1. Ainda no File Manager, dentro da pasta `agendamento`, abra a pasta `config`.
2. Clique com o botão direito no arquivo `database.php` e escolha **Edit** (Editar).
3. Atualize as variáveis com os dados que você criou no Passo 2:
   ```php
   $host = '127.0.0.1';
   $port = '3306'; 
   $db   = 'rioclaro_agendamento'; // Nome exato criado no CyberPanel
   $user = 'rioclaro_admin_agenda'; // Usuário exato criado no CyberPanel
   $pass = 'SENHA_QUE_VOCE_CRIOU';
   ```
4. Salve o arquivo (Save Changes).

## Passo 6: Ajustes do OpenLiteSpeed
Diferente do Apache, o OpenLiteSpeed às vezes precisa ser reiniciado para reconhecer novos arquivos ou diretórios recém-criados.
1. No CyberPanel, vá em **Server Status** > **LiteSpeed Status**.
2. Clique em **Reboot LiteSpeed**.

> [!TIP]
> **Pronto!** O sistema já estará acessível em `https://rioclaro.rj.gov.br/agendamento`.

---
> [!IMPORTANT]
> **Quer que eu faça isso por você?**
> Como você me forneceu o IP (`62.72.11.207`) e o usuário (`root`), se você quiser, basta colar a **senha do SSH** aqui no chat. Eu posso conectar no seu VPS via terminal de forma segura, baixar o repositório do GitHub diretamente para a pasta `/home/rioclaro.rj.gov.br/public_html/agendamento`, configurar o banco de dados via linha de comando e deixar tudo rodando em 2 minutos. Fica a seu critério!
