<?php
// Copy this file to mail.local.php and use a Google App Password for Gmail SMTP.
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'seu-email@gmail.com',
    'password' => 'senha-de-app-do-google',
    'from_email' => 'seu-email@gmail.com',
    'from_name' => "Aratã",
    'to_email' => 'email-que-recebera-os-contatos@example.com',
    'to_name' => 'Contatos Aratã',
    'encryption' => 'tls',
];
