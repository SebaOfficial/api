<?php

require_once __DIR__ . "/../environment.php";

const ANSI_RESET = "\033[0m";
const ANSI_GREEN = "\033[32m";
const ANSI_RED = "\033[31m";

if(php_sapi_name() != 'cli') {
    echo ANSI_RED . "The tools must be started by the CLI." . ANSI_RESET;
    exit(1);
}

function commandExists(string $command): bool
{
    exec("which $command", $output, $return_var);
    return $return_var === 0;
}

function error(string $str, int $exitCode = 1): void {
    echo ANSI_RED . $str . ANSI_RESET . "\n";
    exit($exitCode);
}
