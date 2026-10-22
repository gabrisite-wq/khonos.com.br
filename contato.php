<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

function escapeContactValue(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isValidContactMailConfig($config): bool
{
    $required = ['host', 'username', 'password', 'from_email', 'from_name', 'to_email', 'to_name', 'encryption'];
    if (!is_array($config)) {
        return false;
    }

    foreach ($required as $key) {
        if (!isset($config[$key]) || !is_string($config[$key]) || $config[$key] === '') {
            return false;
        }
    }

    $port = filter_var($config['port'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
    return $port !== false
        && in_array($config['encryption'], ['tls', 'ssl'], true)
        && filter_var($config['from_email'], FILTER_VALIDATE_EMAIL) !== false
        && filter_var($config['to_email'], FILTER_VALIDATE_EMAIL) !== false;
}

function isValidContactDatabaseConfig($config): bool
{
    if (!is_array($config)) {
        return false;
    }

    foreach (['host', 'database', 'username', 'password'] as $key) {
        if (!array_key_exists($key, $config) || !is_string($config[$key])) {
            return false;
        }
    }

    $port = filter_var($config['port'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
    return $port !== false
        && preg_match('/^[a-zA-Z0-9.-]+$/', $config['host']) === 1
        && preg_match('/^[a-zA-Z0-9_]+$/', $config['database']) === 1
        && $config['username'] !== '';
}

if (empty($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

$formValues = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'message' => '',
];
$errors = [];
$successMessage = $_SESSION['contact_success'] ?? '';
unset($_SESSION['contact_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formValues as $field => $_) {
        $formValues[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
    }

    $submittedToken = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : '';
    if (!hash_equals($_SESSION['contact_csrf'], $submittedToken)) {
        $errors[] = 'Não foi possível validar o envio. Atualize a página e tente novamente.';
    }

    if (!empty($_POST['website_url'] ?? '')) {
        $errors[] = 'Não foi possível enviar sua mensagem. Revise o formulário e tente novamente.';
    }

    if ($formValues['name'] === '' || mb_strlen($formValues['name']) > 120) {
        $errors[] = 'Informe seu nome (até 120 caracteres).';
    }

    if (!filter_var($formValues['email'], FILTER_VALIDATE_EMAIL) || strlen($formValues['email']) > 254) {
        $errors[] = 'Informe um endereço de e-mail válido.';
    }

    $phoneDigits = preg_replace('/\D+/', '', $formValues['phone']);
    if (!preg_match('/^\+?[\d\s().-]{10,20}$/', $formValues['phone']) || strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
        $errors[] = 'Informe um telefone válido com DDD.';
    }

    $messageLength = mb_strlen($formValues['message']);
    if ($messageLength < 10 || $messageLength > 4000) {
        $errors[] = 'Sua mensagem deve ter entre 10 e 4.000 caracteres.';
    }

    if (($_POST['contact_consent'] ?? '') !== 'yes') {
        $errors[] = 'Autorize o uso dos dados para receber uma resposta à sua solicitação.';
    }

    if (!$errors && !empty($_SESSION['contact_last_sent']) && time() - $_SESSION['contact_last_sent'] < 20) {
        $errors[] = 'Aguarde alguns segundos antes de enviar outra mensagem.';
    }

    if (!$errors) {
        $autoloadPath = __DIR__ . '/vendor/autoload.php';
        $mailConfigPath = __DIR__ . '/config/mail.local.php';
        $databaseConfigPath = __DIR__ . '/config/database.local.php';
        $mailConfig = null;
        $mailIsConfigured = is_file($autoloadPath) && is_file($mailConfigPath);

        if ($mailIsConfigured) {
            try {
                $mailConfig = require $mailConfigPath;
                $mailIsConfigured = isValidContactMailConfig($mailConfig);
            } catch (ParseError $exception) {
                error_log('Contact form mail configuration could not be parsed: ' . $exception->getMessage());
                $mailIsConfigured = false;
            }
        }

        if (!$mailIsConfigured) {
            error_log('Contact form email notification is not configured.');
        }

        if (!is_file($databaseConfigPath)) {
            error_log('Contact form is not configured: create config/database.local.php.');
            $errors[] = 'O formulário ainda não está configurado para registrar sua mensagem. Tente novamente mais tarde.';
        } else {
            $databaseConfig = require $databaseConfigPath;

            if (!isValidContactDatabaseConfig($databaseConfig)) {
                error_log('Contact form database configuration is invalid.');
                $errors[] = 'O formulário ainda não está configurado para registrar sua mensagem. Tente novamente mais tarde.';
            } else {
                $databasePort = (int) $databaseConfig['port'];
                $marketingConsent = ($_POST['marketing_consent'] ?? '') === 'yes';

                try {
                    $pdo = new PDO(
                        'mysql:host=' . $databaseConfig['host'] . ';port=' . $databasePort . ';dbname=' . $databaseConfig['database'] . ';charset=utf8mb4',
                        $databaseConfig['username'],
                        $databaseConfig['password'],
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ]
                    );
                    $insert = $pdo->prepare(
                        'INSERT INTO contact_leads
                            (name, email, phone, message, contact_consent, marketing_consent, contact_consent_at, marketing_consent_at, created_at, email_notification_status)
                         VALUES
                            (:name, :email, :phone, :message, 1, :marketing_consent, UTC_TIMESTAMP(),
                             IF(:marketing_consent_at = 1, UTC_TIMESTAMP(), NULL), UTC_TIMESTAMP(), :email_notification_status)'
                    );
                    $insert->execute([
                        'name' => $formValues['name'],
                        'email' => $formValues['email'],
                        'phone' => $formValues['phone'],
                        'message' => $formValues['message'],
                        'marketing_consent' => $marketingConsent ? 1 : 0,
                        'marketing_consent_at' => $marketingConsent ? 1 : 0,
                        'email_notification_status' => 'pending',
                    ]);
                    $leadId = (int) $pdo->lastInsertId();
                } catch (PDOException $exception) {
                    error_log('Contact form database write failed: ' . $exception->getMessage());
                    $errors[] = 'Não foi possível registrar sua mensagem agora. Tente novamente mais tarde.';
                }

                if (!$errors) {
                    $emailNotificationStatus = 'not_configured';

                    if ($mailIsConfigured) {
                        require $autoloadPath;

                        try {
                            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                            $mail->isSMTP();
                            $mail->Host = $mailConfig['host'];
                            $mail->SMTPAuth = true;
                            $mail->Username = $mailConfig['username'];
                            $mail->Password = $mailConfig['password'];
                            $mail->Port = (int) $mailConfig['port'];
                            $mail->SMTPSecure = $mailConfig['encryption'];
                            $mail->CharSet = 'UTF-8';
                            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
                            $mail->addAddress($mailConfig['to_email'], $mailConfig['to_name']);
                            $mail->addReplyTo($formValues['email'], $formValues['name']);
                            $mail->Subject = 'Novo contato pelo site Aratã';
                            $mail->Body = implode("\n", [
                                'Novo contato recebido pelo site Aratã.',
                                '',
                                'Nome: ' . $formValues['name'],
                                'E-mail: ' . $formValues['email'],
                                'Telefone: ' . $formValues['phone'],
                                'Autorizou comunicações de marketing: ' . ($marketingConsent ? 'Sim' : 'Não'),
                                '',
                                'Mensagem:',
                                $formValues['message'],
                            ]);
                            $mail->send();
                            $emailNotificationStatus = 'sent';
                        } catch (PHPMailer\PHPMailer\Exception $exception) {
                            error_log('Contact form email notification failed: ' . $exception->getMessage());
                            $emailNotificationStatus = 'failed';
                        }
                    }

                    try {
                        $update = $pdo->prepare('UPDATE contact_leads SET email_notification_status = :status WHERE id = :id');
                        $update->execute(['status' => $emailNotificationStatus, 'id' => $leadId]);
                    } catch (PDOException $exception) {
                        error_log('Contact form notification status update failed: ' . $exception->getMessage());
                    }

                    $_SESSION['contact_last_sent'] = time();
                    $_SESSION['contact_success'] = $emailNotificationStatus === 'sent'
                        ? 'Mensagem registrada e enviada. Obrigado por entrar em contato com a Aratã.'
                        : 'Mensagem registrada com sucesso. A notificação interna por e-mail não pôde ser enviada agora.';
                    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
                    header('Location: contato.php', true, 303);
                    exit;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contato | Aratã</title>
    <meta name="description" content="Entre em contato com a Aratã. Envie sua mensagem e, se desejar, receba novidades e ofertas." />
    <link rel="icon" type="image/png" href="src/images/Logotipo%20-%20Varia%C3%A7%C3%B5es/fav-icon-y.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=4.5">
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="container contact-page">
        <section class="nh contact-panel" aria-labelledby="contact-title">
            <div class="section-title">
                <h1 id="contact-title">Fale com a Aratã</h1>
                <p>Conte como podemos ajudar. Se quiser, marque a opção para receber novidades e ofertas.</p>
            </div>

            <?php if ($successMessage !== ''): ?>
                <p class="contact-feedback contact-feedback--success" role="status"><?= escapeContactValue($successMessage) ?></p>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="contact-feedback contact-feedback--error" role="alert">
                    <p>Revise os dados do formulário:</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= escapeContactValue($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="contact-form" method="post" action="contato.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= escapeContactValue($_SESSION['contact_csrf']) ?>">
                <div class="contact-form__trap" aria-hidden="true">
                    <label for="website_url">Deixe este campo vazio</label>
                    <input id="website_url" name="website_url" type="text" tabindex="-1" autocomplete="off">
                </div>

                <div class="contact-form__fields">
                    <div class="contact-field">
                        <label for="name">Nome</label>
                        <input id="name" name="name" type="text" maxlength="120" autocomplete="name" value="<?= escapeContactValue($formValues['name']) ?>" required>
                    </div>
                    <div class="contact-field">
                        <label for="email">E-mail</label>
                        <input id="email" name="email" type="email" maxlength="254" autocomplete="email" value="<?= escapeContactValue($formValues['email']) ?>" required>
                    </div>
                    <div class="contact-field">
                        <label for="phone">Telefone com DDD</label>
                        <input id="phone" name="phone" type="tel" maxlength="20" autocomplete="tel" inputmode="tel" value="<?= escapeContactValue($formValues['phone']) ?>" required>
                    </div>
                    <div class="contact-field contact-field--message">
                        <label for="message">Mensagem</label>
                        <textarea id="message" name="message" rows="6" minlength="10" maxlength="4000" required><?= escapeContactValue($formValues['message']) ?></textarea>
                    </div>
                </div>

                <div class="contact-consents">
                    <label class="contact-consent">
                        <input type="checkbox" name="contact_consent" value="yes" required <?= ($_POST['contact_consent'] ?? '') === 'yes' ? 'checked' : '' ?>>
                        <span>Autorizo o uso dos meus dados para que a Aratã responda a esta solicitação. <span aria-hidden="true">*</span></span>
                    </label>
                    <label class="contact-consent">
                        <input type="checkbox" name="marketing_consent" value="yes" <?= ($_POST['marketing_consent'] ?? '') === 'yes' ? 'checked' : '' ?>>
                        <span>Quero receber novidades e ofertas da Aratã por e-mail, telefone ou WhatsApp. Sei que posso retirar essa autorização quando quiser.</span>
                    </label>
                </div>

                <p class="contact-privacy">Seus dados serão registrados no banco de dados da Aratã e enviados ao e-mail de contato configurado para acompanhamento. O consentimento para receber comunicações de marketing é opcional e não condiciona o envio da mensagem.</p>
                <button class="button button-primary" type="submit">Enviar mensagem</button>
            </form>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
