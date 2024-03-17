<?php

namespace Seba\API\Helpers;

use Seba\API\Database;

class NewsLetter
{
    public static function getNewsletterDb(): Database
    {
        return Database::getInstance(str_replace('{__DIR__}', \ROOT_DIR, $_ENV['NEWSLETTER_DB']));
    }
}
