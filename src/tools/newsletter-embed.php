<?php

require_once __DIR__ . "/environment.php";

chdir(__DIR__ . '/newsletter/');

exec("npm i && \
    npm run rollup -- --environment API_URL:" . $_ENV['API_URL'] . " #&& \
    #npm run minify
", $output, $result_code);

if($result_code !== 0) {
    error("There was an error creating the newsletter file.");
}

if(!rename(__DIR__ . "/newsletter/newsletter.js", ROOT_DIR . "/generated/newsletter.js")) {
    error("There was an errro while moving " . __DIR__ . "/newsletter/newsletter.js to" . ROOT_DIR . "/generated/newsletter.js");
}
