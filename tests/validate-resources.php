<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

require dirname(__DIR__).'/vendor/autoload.php';

$root = dirname(__DIR__);
$skillsDirectory = $root.'/resources/boost/skills';
$guidelinesDirectory = $root.'/resources/boost/guidelines';
$errors = [];
$names = [];

if (! is_file($guidelinesDirectory.'/core.md') && ! is_file($guidelinesDirectory.'/core.blade.php')) {
    $errors[] = 'Guideline core absente.';
}

if (! is_dir($skillsDirectory)) {
    $errors[] = 'Le répertoire resources/boost/skills est absent.';
}

$scan = new RecursiveCallbackFilterIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
    fn (SplFileInfo $file): bool => ! $file->isDir() || ! in_array($file->getFilename(), ['.git', 'vendor'], true),
);

foreach (new RecursiveIteratorIterator($scan) as $file) {
    if (! $file->isFile()) {
        continue;
    }

    $path = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());
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
            || $name !== basename($directory)
        ) {
            $errors[] = "Nom de skill invalide ou différent du dossier : {$name}";
        }

        if (isset($names[$name])) {
            $errors[] = "Nom de skill dupliqué : {$name}";
        }

        $names[$name] = true;
    }

    preg_match_all('/\]\((references\/[^)#]+)(?:#[^)]+)?\)/', $content, $references);

    foreach ($references[1] as $reference) {
        if (! is_file($directory.'/'.$reference)) {
            $errors[] = "Référence absente : {$directory}/{$reference}";
        }
    }
}

if ($names === []) {
    $errors[] = 'Aucune skill n’a été trouvée.';
}

$registryPath = $root.'/.agents/registry.yaml';

try {
    $registry = Yaml::parseFile($registryPath);
} catch (Throwable $exception) {
    $errors[] = "Registre invalide : {$exception->getMessage()}";
    $registry = null;
}

if (! is_array($registry) || ! isset($registry['roles']) || ! is_array($registry['roles'])) {
    $errors[] = 'Le registre ne contient pas de rôles valides.';
} else {
    $qualificationSkill = $registry['orchestration']['qualification_skill'] ?? null;

    if (! is_string($qualificationSkill) || ! isset($names[$qualificationSkill])) {
        $errors[] = 'Le skill de qualification du registre est absent.';
    }

    foreach (['work_contract', 'handoff_contract', 'review_contract'] as $contract) {
        $path = $registry['orchestration'][$contract] ?? null;

        if (! is_string($path) || ! is_file($root.'/'.$path)) {
            $errors[] = "Contrat absent : {$contract}";
        }
    }

    foreach ($registry['roles'] as $roleName => $role) {
        if (! is_array($role) || ! isset($role['instructions']) || ! is_string($role['instructions']) || ! is_file($root.'/'.$role['instructions'])) {
            $errors[] = "Instructions absentes pour le rôle {$roleName}.";
        }

        if (! is_array($role['skills'] ?? null)) {
            $errors[] = "Skills absents pour le rôle {$roleName}.";

            continue;
        }

        foreach ($role['skills'] as $skill) {
            if (! is_string($skill) || ! isset($names[$skill])) {
                $errors[] = "Skill inconnu pour le rôle {$roleName} : ".(string) $skill;
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors).PHP_EOL);

    exit(1);
}

fwrite(STDOUT, sprintf("%d skill(s), leurs références et le registre sont valides.%s", count($names), PHP_EOL));
