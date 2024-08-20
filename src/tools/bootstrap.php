<?php

require_once __DIR__ . "/environment.php";

use Seba\API\Helpers\NewsletterHelper;
use Seba\API\Helpers\Utils;

function truncate($string, $length, $dots = "..."): string
{
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

$newsletterdb = NewsletterHelper::getNewsletterDb()->init(ROOT_DIR . "/database/newsletter.sql");

foreach($newsletterdb as $stmt) {
    echo $stmt->ok ? ANSI_GREEN : ANSI_RED;
    echo $stmt->ok ? (($pos = strpos($stmt->query, ' (')) !== false ? substr($stmt->query, 0, $pos) : $stmt->query) : $stmt->error;
    echo "...\n" . ANSI_RESET;
}

file_put_contents(
    Utils::getAdminPasswordPath(),
    password_hash(getenv('ADMIN_PASSWORD') ?? readline("Insert the new admin password: "), PASSWORD_BCRYPT)
);
