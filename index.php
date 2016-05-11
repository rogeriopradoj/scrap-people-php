<?php

require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$svnDirectory = getenv('SVN_DIR');

$client = new \Goutte\Client();
$svnAuthors = shell_exec(
    'cd '. $svnDirectory
    . ' && svn log --quiet | grep "^r" | awk \'{print $3}\' | sort | uniq'
);
$line = strtok($svnAuthors, PHP_EOL);
while ( $line !== false) {
    $crawler = $client
        ->request('GET', 'http://people.php.net/' . $line)
        ->filter('h1[property="foaf:name"]')
        ->each(function ($node) use ($line) {
            printf("\"%s\", \"%s\"\n", $line, $node->text());
        });

    $line = strtok(PHP_EOL);
}
