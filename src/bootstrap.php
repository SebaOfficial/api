<?php

require_once __DIR__ . "/environment.php";

use Seba\API\Helpers\NewsletterHelper;
use Seba\API\Helpers\Utils;

const ANSI_RESET = "\033[0m";
const ANSI_GREEN = "\033[32m";
const ANSI_RED = "\033[31m";

if(php_sapi_name() != 'cli') {
    echo "The bootstrap must be started by the CLI.";
    exit(1);
}

function truncate($string, $length, $dots = "...")
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
    password_hash(readline("Insert the new admin password: "), PASSWORD_BCRYPT)
);
