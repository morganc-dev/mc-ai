<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use MorgancDev\McHarnessAi\Console\InstallAgentsCommand;
use Symfony\Component\Console\Tester\CommandTester;

require dirname(__DIR__).'/vendor/autoload.php';

$files = new Filesystem;
$packageRoot = dirname(__DIR__);
$tempRoot = realpath(sys_get_temp_dir());

if ($tempRoot === false) {
    throw new RuntimeException('Répertoire temporaire introuvable.');
}

$createdRoots = [];
$checks = 0;

$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;

    if (! $condition) {
        throw new RuntimeException($message);
    }
};

$makeProject = static function () use ($files, $tempRoot, &$createdRoots): string {
    $root = $tempRoot.DIRECTORY_SEPARATOR.'mc-harness-ai-test-'.bin2hex(random_bytes(8));
    $files->ensureDirectoryExists($root);
    $createdRoots[] = $root;

    return $root;
};

$installSkill = static function (string $root, string $agent) use ($files): void {
    $path = $agent === 'codex'
        ? $root.'/.agents/skills/mc-qualify-request/SKILL.md'
        : $root.'/.claude/skills/mc-qualify-request/SKILL.md';

    $files->ensureDirectoryExists(dirname($path));
    $files->put($path, "---\nname: mc-qualify-request\ndescription: Test skill\n---\n");
};

$run = static function (string $root, array $options = [], ?Filesystem $filesystem = null): array {
    $app = new Application($root);
    $command = new InstallAgentsCommand($filesystem ?? new Filesystem);
    $command->setLaravel($app);
    $tester = new CommandTester($command);
    $status = $tester->execute($options, ['interactive' => false]);

    return [$status, $tester->getDisplay()];
};

try {
    $withoutSkill = $makeProject();
    [$status, $output] = $run($withoutSkill);
    $assert($status === 1, 'Une installation sans skill Boost doit échouer.');
    $assert(str_contains($output, 'boost:install'), 'Le diagnostic doit expliquer le prérequis Boost.');
    $assert(! is_file($withoutSkill.'/AGENTS.md'), 'Aucun pont ne doit être écrit sans skill.');

    $codexProject = $makeProject();
    $installSkill($codexProject, 'codex');
    $boostBlock = "<laravel-boost-guidelines>\nBoost\n</laravel-boost-guidelines>\n";
    $files->put($codexProject.'/AGENTS.md', $boostBlock);

    [$status] = $run($codexProject, ['--dry-run' => true]);
    $assert($status === 0, 'La simulation initiale doit réussir.');
    $assert(! is_file($codexProject.'/.agents/registry.yaml'), 'La simulation ne doit pas écrire le noyau.');

    [$status] = $run($codexProject);
    $assert($status === 0, 'L’installation Codex doit réussir.');
    $assert(is_file($codexProject.'/.agents/registry.yaml'), 'Le registre doit être copié.');
    $assert(str_contains($files->get($codexProject.'/AGENTS.md'), $boostBlock), 'Le bloc Boost doit être conservé.');
    $assert(str_contains($files->get($codexProject.'/AGENTS.md'), '<mc-harness-ai>'), 'Le bloc du package doit être ajouté.');
    $assert(! is_file($codexProject.'/CLAUDE.md'), 'Le pont Claude ne doit pas être écrit sans skill Claude.');

    $firstInstructions = $files->get($codexProject.'/AGENTS.md');
    [$status] = $run($codexProject);
    $assert($status === 0, 'Une réinstallation sans changement doit réussir.');
    $assert($files->get($codexProject.'/AGENTS.md') === $firstInstructions, 'Une réinstallation doit rester idempotente.');

    $rolePath = $codexProject.'/.agents/roles/mc-reviewer/ROLE.md';
    $originalRole = $files->get($rolePath);
    $localRole = $originalRole."\nAdaptation locale.\n";
    $files->put($rolePath, $localRole);

    [$status] = $run($codexProject);
    $assert($status === 1, 'Un fichier personnalisé doit bloquer la mise à jour ordinaire.');
    $assert($files->get($rolePath) === $localRole, 'Une mise à jour refusée ne doit pas modifier le rôle.');

    [$status] = $run($codexProject, ['--force' => true]);
    $assert($status === 0, 'La mise à jour forcée doit réussir.');
    $assert($files->get($rolePath) === $originalRole, 'Le rôle du package doit être restauré après sauvegarde.');

    $backups = glob($codexProject.'/.mc-harness-ai/backups/*/.agents/roles/mc-reviewer/ROLE.md') ?: [];
    $assert(count($backups) === 1, 'Une seule sauvegarde du rôle doit être créée.');
    $assert($files->get($backups[0]) === $localRole, 'La sauvegarde doit contenir l’adaptation locale.');

    $changedInstructions = str_replace('3. Classer la demande', '3. Classement local de la demande', $files->get($codexProject.'/AGENTS.md'));
    $files->put($codexProject.'/AGENTS.md', $changedInstructions);
    [$status] = $run($codexProject, ['--force' => true]);
    $assert($status === 0, 'Le bloc d’instructions divergent doit pouvoir être mis à jour.');
    $assert(str_contains($files->get($codexProject.'/AGENTS.md'), $boostBlock), 'Le contenu hors bloc géré doit être préservé.');
    $assert(! str_contains($files->get($codexProject.'/AGENTS.md'), 'Classement local'), 'Le bloc du package doit être remis à jour.');
    $instructionBackups = glob($codexProject.'/.mc-harness-ai/backups/*/AGENTS.md') ?: [];
    $assert(count($instructionBackups) === 1 && $files->get($instructionBackups[0]) === $changedInstructions, 'Le bloc divergent doit être sauvegardé.');

    $claudeProject = $makeProject();
    $installSkill($claudeProject, 'claude');
    [$status] = $run($claudeProject);
    $assert($status === 0, 'L’installation Claude doit réussir.');
    $assert(is_file($claudeProject.'/CLAUDE.md'), 'Le pont Claude doit être créé.');
    $assert(! is_file($claudeProject.'/AGENTS.md'), 'Le pont Codex ne doit pas être créé sans skill Codex.');

    $failureProject = $makeProject();
    $installSkill($failureProject, 'codex');
    $failingFiles = new class extends Filesystem
    {
        private bool $failed = false;

        public function put($path, $contents, $lock = false)
        {
            if (! $this->failed && str_ends_with((string) $path, DIRECTORY_SEPARATOR.'review.md')) {
                $this->failed = true;

                return false;
            }

            return parent::put($path, $contents, $lock);
        }
    };

    [$status, $output] = $run($failureProject, [], $failingFiles);
    $assert($status === 1, 'Une écriture incomplète doit faire échouer la commande.');
    $assert(str_contains($output, 'Installation interrompue'), 'L’échec d’écriture doit être signalé.');
    $assert(! is_file($failureProject.'/.agents/registry.yaml'), 'Les fichiers écrits avant l’échec doivent être restaurés.');
    $assert(! is_file($failureProject.'/AGENTS.md'), 'Le pont ne doit pas être laissé après un échec.');

    fwrite(STDOUT, "{$checks} contrôles d’installation valides.".PHP_EOL);
} finally {
    foreach ($createdRoots as $root) {
        $prefix = $tempRoot.DIRECTORY_SEPARATOR.'mc-harness-ai-test-';

        if (! str_starts_with($root, $prefix) || dirname($root) !== $tempRoot) {
            throw new RuntimeException('Suppression temporaire hors périmètre refusée.');
        }

        $files->deleteDirectory($root);
    }
}
