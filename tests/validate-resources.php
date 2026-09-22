<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

require dirname(__DIR__).'/vendor/autoload.php';

$root = dirname(__DIR__);
$skillsDirectory = $root.'/resources/boost/skills';
$guidelinesDirectory = $root.'/resources/boost/guidelines';
$errors = [];
$names = [];

if (! is_dir($guidelinesDirectory)) {
    $errors[] = 'Le répertoire resources/boost/guidelines est absent.';
}

if (! is_dir($skillsDirectory)) {
    $errors[] = 'Le répertoire resources/boost/skills est absent.';
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
);

foreach ($iterator as $file) {
    if (! $file->isFile()) {
        continue;
    }

    $path = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());

    if (str_starts_with($path, str_replace(DIRECTORY_SEPARATOR, '/', $root.'/vendor/'))) {
        continue;
    }

    $filename = strtolower($file->getFilename());

    if (
        preg_match('/^\.env(?:\.|$)/', $filename) === 1
        || $filename === '.mcp.json'
        || preg_match('/^(id_rsa|id_ed25519|.*\.pem|.*\.key)$/', $filename) === 1
    ) {
        $errors[] = "Fichier interdit dans le dépôt : {$path}";
    }
}

foreach (glob($skillsDirectory.'/*', GLOB_ONLYDIR) ?: [] as $directory) {
    $skillFile = $directory.'/SKILL.md';

    if (! is_file($skillFile)) {
        $errors[] = "SKILL.md absent : {$directory}";
        continue;
    }

    $content = file_get_contents($skillFile);

    if ($content === false || preg_match('/^---\R(.*?)\R---\R/s', $content, $matches) !== 1) {
        $errors[] = "Frontmatter YAML invalide ou absent : {$skillFile}";
        continue;
    }

    try {
        $frontmatter = Yaml::parse($matches[1]);
    } catch (Throwable $exception) {
        $errors[] = "Frontmatter YAML invalide : {$skillFile} ({$exception->getMessage()})";
        continue;
    }

    if (! is_array($frontmatter)) {
        $errors[] = "Frontmatter non associatif : {$skillFile}";
        continue;
    }

    foreach (['name', 'description'] as $field) {
        if (! isset($frontmatter[$field]) || ! is_string($frontmatter[$field]) || trim($frontmatter[$field]) === '') {
            $errors[] = "Champ {$field} manquant ou vide : {$skillFile}";
        }
    }

    $name = $frontmatter['name'] ?? null;

    if (is_string($name)) {
        if (
            str_contains($name, '..')
            || str_contains($name, '/')
            || str_contains($name, chr(92))
            || str_contains($name, chr(0))
            || trim($name, ". \t\n\r\0\x0B") === ''
        ) {
            $errors[] = "Nom de skill invalide : {$name}";
        }

        if (isset($names[$name])) {
            $errors[] = "Nom de skill dupliqué : {$name}";
        }

        $names[$name] = true;
    }
}

if ($names === []) {
    $errors[] = 'Aucune skill n\'a été trouvée.';
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors).PHP_EOL);
    exit(1);
}

fwrite(STDOUT, sprintf("%d skill(s) et la structure Boost sont valides.%s", count($names), PHP_EOL));
