<?php

namespace Seba\API\Helpers;

class Utils
{
    /**
     * Returns a GUIDv4 string
     *
     * Uses the best cryptographically secure method
     * for all supported pltforms with fallback to an older,
     * less secure version.
     *
     * @link https://www.php.net/manual/en/function.com-create-guid.php#119168
     *
     * @param bool $trim
     * @return string
     */
    public static function GUIDv4($trim = true): string
    {
        // Windows
        if (function_exists('com_create_guid')) {
            $guidv4 = com_create_guid();
            return $trim ? trim($guidv4, '{}') : $guidv4;
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes')) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((float)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                substr($charid, 0, 8).$hyphen.
                substr($charid, 8, 4).$hyphen.
                substr($charid, 12, 4).$hyphen.
                substr($charid, 16, 4).$hyphen.
                substr($charid, 20, 12).
                $rbrace;
        return $guidv4;
    }

    /**
     * Get the path of the admin password.
     *
     * @return string The path to the password.
     */
    public static function getAdminPasswordPath(): string
    {
        return str_replace("{__DIR__}", ROOT_DIR, $_ENV['ADMIN_PASSWORD_FILE']);
    }
}
