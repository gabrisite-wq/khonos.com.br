# Guia de integração do formulário de contato

Este guia descreve a configuração e a operação do formulário em produção. O fluxo usa PHP, MySQL/PDO e PHPMailer via SMTP:

1. A pessoa preenche o formulário em `contato.php`.
2. O servidor valida os dados e o consentimento obrigatório.
3. O lead é gravado na tabela `contact_leads`.
4. Se o SMTP estiver configurado, o servidor envia uma notificação para o e-mail da equipe.
5. O resultado da notificação é registrado no lead. Uma falha de e-mail não apaga o lead já salvo.

> O formulário não possui painel administrativo. Os leads devem ser consultados por uma ferramenta de banco de dados ou por uma solução administrativa autorizada.

## Lógica e fluxo dos dados

### Componentes

| Componente | Onde fica | Função |
|---|---|---|
| `contato.php` | Raiz pública do site | Exibe o formulário, valida, grava no banco e envia o e-mail. |
| `database.local.php` | Pasta privada (`khonos-private-files`) | Retorna um array com `host`, `port`, `database`, `username` e `password` do MySQL. |
| `mail.local.php` | Pasta privada (`khonos-private-files`) | Retorna um array com os dados SMTP: servidor, porta, criptografia, usuário, senha, remetente e destinatário. |
| `vendor/` | Raiz pública | Contém o PHPMailer, carregado por `vendor/autoload.php`. |
| `contact_leads` | MySQL | Tabela onde cada contato fica registrado. |
| `error_log` | Raiz do site | Registra falhas de configuração, banco e SMTP. |

Os caminhos das configurações privadas são definidos em `contato.php`, em `$mailConfigPath` e `$databaseConfigPath`. Como `__DIR__` não termina com barra, o caminho deve começar com `/`:

```php
$mailConfigPath = __DIR__ . '/../khonos-private-files/mail.local.php';
$databaseConfigPath = __DIR__ . '/../khonos-private-files/database.local.php';
```

### Fluxo de uma mensagem

```text
Visitante
   │  1. GET contato.php
   ▼
contato.php ── gera token CSRF na sessão ──► exibe o formulário
   │  2. POST (nome, e-mail, telefone, mensagem, consentimentos, token)
   ▼
Validação (qualquer falha → volta ao formulário com os erros)
   │  token CSRF · campo oculto website_url vazio · nome · e-mail ·
   │  telefone · mensagem (10–4.000) · consentimento · intervalo de 20 s
   ▼
Carrega database.local.php ── ausente ou inválido → erro "não configurado"
   ▼
Conexão PDO com o MySQL
   ▼
INSERT em contact_leads (email_notification_status = 'pending')
   │  falha → erro "não foi possível registrar" e nada é enviado
   ▼
Carrega vendor/ e mail.local.php
   │  ausentes ou inválidos → status 'not_configured'
   ▼
PHPMailer envia o e-mail à equipe (Reply-To = e-mail do visitante)
   │  sucesso → 'sent' · exceção → 'failed' (registrada no error_log)
   ▼
UPDATE contact_leads SET email_notification_status
   ▼
Novo token CSRF · mensagem de sucesso na sessão · redirecionamento 303 para contato.php
```

### Regras de negócio

1. **O banco é a fonte da verdade.** O lead só é considerado recebido depois do `INSERT`. O e-mail é apenas um aviso para a equipe.
2. **Falha de e-mail não perde o lead.** Se o SMTP falhar, a pessoa vê "Mensagem registrada com sucesso" e o status fica `failed` ou `not_configured`.
3. **Falha de banco interrompe tudo.** Sem registro, o e-mail não é enviado e a pessoa vê o erro.
4. **O consentimento de contato é obrigatório.** O de marketing é opcional e não condiciona o envio. Cada um é gravado com data em UTC.
5. **Proteção contra abuso.** Token CSRF, campo oculto (honeypot) e intervalo mínimo de 20 segundos por sessão.
6. **Redirecionamento após o envio (POST/Redirect/GET).** Evita reenvio ao atualizar a página.

### Dados gravados em `contact_leads`

| Campo | Origem |
|---|---|
| `name`, `email`, `phone`, `message` | Campos do formulário, já validados. |
| `contact_consent`, `contact_consent_at` | Checkbox obrigatório. A data é gerada pelo banco (`UTC_TIMESTAMP()`). |
| `marketing_consent`, `marketing_consent_at` | Checkbox opcional. A data só é preenchida quando marcado. |
| `created_at` | Gerado pelo banco em UTC. |
| `email_notification_status` | `pending` no `INSERT`. Depois vira `sent`, `failed` ou `not_configured`. |

### Estados da notificação por e-mail

| Status | Significado | O que fazer |
|---|---|---|
| `pending` | Lead gravado, mas o status final não foi atualizado. Pode indicar falha após o `INSERT`. | Consulte o `error_log`. |
| `sent` | E-mail entregue ao servidor SMTP. | Nada. |
| `failed` | Houve tentativa de envio e o SMTP recusou. | Confira o `error_log` e o `mail.local.php`. |
| `not_configured` | `vendor/` ou `mail.local.php` ausente ou inválido. | Instale o `vendor/` e preencha o `mail.local.php`. |

### Mensagens de erro e causas

| Mensagem na tela | Causa | Evidência no `error_log` |
|---|---|---|
| "O formulário ainda não está configurado…" | `database.local.php` ausente, no caminho errado ou inválido. | `Contact form is not configured` ou `database configuration is invalid` |
| "Não foi possível registrar sua mensagem…" | Falha de conexão ou de gravação no MySQL. | `Contact form database write failed: SQLSTATE[...]` |
| "Mensagem registrada com sucesso. A notificação interna por e-mail não pôde ser enviada…" | Lead salvo, mas o e-mail falhou. | `email notification failed` ou `not configured` |

Códigos comuns do MySQL: `1045` (usuário ou senha incorretos), `1044` (usuário sem permissão no banco), `1049` (banco inexistente) e `42S02` (tabela inexistente).

## 1. Requisitos da hospedagem

- PHP **8.0 ou superior**.
- Extensões PHP `pdo_mysql`, `mbstring` e `openssl` habilitadas.
- MySQL ou MariaDB com suporte a `InnoDB` e `utf8mb4`.
- Composer para instalar a dependência, ou os arquivos `vendor/` gerados antes do envio.
- Conexões SMTP de saída permitidas pela hospedagem.
- HTTPS habilitado para o domínio. Em HTTPS, o formulário configura o cookie de sessão como seguro.

## 2. Banco de dados

1. Crie o banco de dados de produção no painel da hospedagem. O exemplo de configuração usa `arata_leads`; substitua esse nome se o banco tiver outro nome.
2. Execute [database/schema.sql](./database/schema.sql) no banco criado. O script cria a tabela `contact_leads`.
3. Crie um usuário próprio para a aplicação; não use `root` nem o usuário administrador da hospedagem.
4. Conceda à aplicação somente `INSERT` e `UPDATE` na tabela `contact_leads`. Use uma conta administrativa separada para criar o banco/tabela e consultar os leads.
5. Faça backup periódico do banco e defina por quanto tempo os dados serão mantidos.

O arquivo [config/database.example.php](./config/database.example.php) é apenas um modelo. Crie `config/database.local.php` no servidor com os dados reais:

```php
<?php
return [
    'host' => 'HOST_DO_BANCO',
    'port' => 3306,
    'database' => 'NOME_DO_BANCO',
    'username' => 'USUARIO_DA_APLICACAO',
    'password' => 'SENHA_DO_BANCO',
];
```

Use o host, a porta e as credenciais fornecidas pela hospedagem. Alguns provedores exigem um prefixo no nome do banco e do usuário.

## 3. Dependência PHPMailer

O projeto mantém a versão da dependência em `composer.lock`. No diretório do projeto, instale as dependências de produção:

```sh
composer install --no-dev --optimize-autoloader
```

O arquivo `vendor/autoload.php` precisa existir no servidor para que o envio SMTP funcione. Se a hospedagem não tiver Composer, execute a instalação em um ambiente compatível e envie a pasta `vendor/` junto com o projeto. Não edite manualmente o conteúdo dessa pasta.

## 4. E-mail de notificação (SMTP)

Crie `config/mail.local.php` no servidor, usando [config/mail.example.php](./config/mail.example.php) como referência:

```php
<?php
return [
    'host' => 'SERVIDOR_SMTP',
    'port' => 587,
    'username' => 'USUARIO_SMTP',
    'password' => 'SENHA_OU_SENHA_DE_APLICATIVO',
    'from_email' => 'REMETENTE_AUTORIZADO',
    'from_name' => 'Aratã',
    'to_email' => 'CAIXA_QUE_RECEBE_OS_CONTATOS',
    'to_name' => 'Contatos Aratã',
    'encryption' => 'tls',
];
```

- Obtenha host, porta e criptografia com o provedor de e-mail. A configuração aceita `tls` ou `ssl`; use `587` com `tls` ou `465` com `ssl` somente quando indicado pelo provedor.
- Em Gmail, use uma **senha de app** em uma conta com verificação em duas etapas; não use a senha normal da conta.
- `from_email` deve ser um remetente permitido/autenticado pelo servidor SMTP. O e-mail informado pelo visitante é usado como endereço de resposta.
- `to_email` é a caixa da equipe que receberá a notificação.
- Nunca coloque credenciais no repositório, em `*.example.php`, no HTML ou em mensagens de erro. Restrinja a leitura dos arquivos `.local.php` à conta do servidor web.

O diretório `config/` tem um `.htaccess` que bloqueia acesso HTTP em servidores Apache. Se a hospedagem não for Apache ou não respeitar `.htaccess`, configure uma regra equivalente no servidor web.

## 5. Proteção dos arquivos de configuração

O `.gitignore` do projeto exclui `config/mail.local.php` e `config/database.local.php`. Confirme que esses arquivos não serão publicados no Git:

```sh
git check-ignore config/mail.local.php config/database.local.php
```

Em produção (cPanel/HostGator), mantenha `database.local.php` e `mail.local.php` em uma pasta fora da raiz pública, ao lado da pasta do site (por exemplo, `/home1/USUARIO/khonos-private-files/`), e aponte `contato.php` para ela, como mostrado em "Lógica e fluxo dos dados". Assim, os arquivos não podem ser acessados por HTTP. No cPanel, o usuário MySQL deve ser associado ao banco com os privilégios necessários; sem isso ocorre o erro `1044`.

Guarde os arquivos locais fora do versionamento, proteja backups e não compartilhe credenciais em chamados ou capturas de tela. Em caso de exposição, troque imediatamente as senhas no provedor correspondente.

## 6. Teste de ponta a ponta

Faça os testes primeiro em uma caixa de e-mail e banco controlados:

1. Abra `https://SEU_DOMINIO/contato.php` e envie uma mensagem com dados válidos.
2. Confirme a mensagem de sucesso no navegador.
3. Confirme no banco que um novo registro foi criado e que `email_notification_status` está como `sent`.
4. Confirme o recebimento do e-mail e teste a resposta ao endereço do visitante.
5. Envie sem o consentimento obrigatório: o formulário deve recusar o envio e não criar um lead.
6. Teste dados inválidos, incluindo mensagem com menos de 10 caracteres, e corrija até o formulário aceitá-los.
7. Teste a proteção contra envios repetidos: a aplicação limita novos envios na mesma sessão a um intervalo de 20 segundos.
8. Se possível, faça um teste controlado com SMTP indisponível: o lead deve continuar no banco, com estado `failed`, e a pessoa deve receber a confirmação de que a mensagem foi registrada, mas a notificação por e-mail não pôde ser enviada.
9. Depois dos testes, remova os leads de teste conforme a política de retenção.

Não use dados pessoais reais de clientes para testar.

## 7. Acompanhar leads e notificações

Para consultar os registros, use uma conexão administrativa segura ao banco e limite o acesso a pessoas autorizadas. Uma consulta operacional básica é:

```sql
SELECT
    id,
    name,
    email,
    phone,
    message,
    contact_consent,
    marketing_consent,
    email_notification_status,
    created_at
FROM contact_leads
ORDER BY created_at DESC
LIMIT 100;
```

Estados de `email_notification_status`:

- `sent`: notificação enviada pelo SMTP.
- `failed`: o lead foi gravado, mas o SMTP falhou.
- `not_configured`: o lead foi gravado, mas o SMTP ou o autoloader do PHPMailer não está configurado.
- `pending`: estado inicial da gravação; pode indicar que a atualização final do estado não ocorreu.

Se um lead estiver `failed` ou `not_configured`, verifique o log de erros PHP do servidor e as credenciais/restrições SMTP. O detalhe técnico é registrado no log do servidor, não exibido à pessoa que preencheu o formulário. O código não reenvia automaticamente notificações com falha.

## 8. Validação e privacidade

O formulário inclui token CSRF, campo honeypot, limite de frequência por sessão, validação e escape dos dados. Nome, e-mail, telefone, mensagem, consentimentos e datas são persistidos; não são armazenados IP nem agente do navegador.

Antes de divulgar o formulário, revise o aviso de privacidade exibido em `contato.php` com a política oficial da empresa: informe quem controla os dados, as finalidades, o prazo de retenção, como exercer direitos e o canal de contato. O consentimento para receber marketing é opcional e separado do consentimento necessário para responder à solicitação. Não use leads sem consentimento de marketing para campanhas.

## 9. Se algo não funcionar

- **A página não abre ou retorna erro 500:** verifique a versão do PHP, as extensões requeridas e o log PHP/Apache da hospedagem. Não habilite a exibição pública de erros em produção.
- **O formulário diz que não conseguiu registrar:** confirme a presença e sintaxe de `config/database.local.php`, host/porta, permissões do usuário e existência da tabela.
- **A pessoa vê confirmação, mas o e-mail não chega:** consulte `email_notification_status`, o log PHP, pasta de spam, host/porta/criptografia SMTP e regras de saída do provedor.
- **A notificação chega a spam ou não é aceita:** confirme que `from_email` é autorizado e configure SPF, DKIM e DMARC conforme as instruções do provedor do domínio.
- **O formulário aceita o envio, mas não chega notificação:** isso pode ser normal se o SMTP não estiver configurado; o lead continua salvo no banco e o estado será `not_configured`.
