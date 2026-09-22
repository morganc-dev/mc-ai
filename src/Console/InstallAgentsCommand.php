<?php

declare(strict_types=1);

namespace MorgancDev\McHarnessAi\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class InstallAgentsCommand extends Command
{
    private const MANAGED_BLOCK_START = '<mc-harness-ai>';

    private const MANAGED_BLOCK_END = '</mc-harness-ai>';

    protected $signature = 'mc-harness-ai:install
                            {--dry-run : Affiche les opérations sans écrire de fichier}
                            {--force : Remplace uniquement les fichiers et blocs gérés qui diffèrent}';

    protected $description = 'Installe le noyau .agents et les ponts Codex et Claude Code.';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $sourceDirectory = dirname(__DIR__, 2).'/.agents';
        $targetDirectory = base_path('.agents');

        if (! $this->files->isDirectory($sourceDirectory)) {
            $this->error('Le noyau .agents est absent du package installé.');

            return self::FAILURE;
        }

        $operations = [
            ...$this->plannedCoreOperations($sourceDirectory, $targetDirectory),
            $this->plannedInstructionOperation(base_path('AGENTS.md')),
            $this->plannedInstructionOperation(base_path('CLAUDE.md')),
        ];

        $conflicts = array_filter($operations, fn (array $operation): bool => $operation['status'] === 'conflict');
        $blockedLinks = array_filter($operations, fn (array $operation): bool => $operation['status'] === 'link');
        $invalidTargets = array_filter($operations, fn (array $operation): bool => $operation['status'] === 'invalid');

        foreach ($operations as $operation) {
            $status = $operation['status'] === 'conflict' && $this->option('force') ? 'replace' : $operation['status'];
            $this->line(sprintf('[%s] %s', $status, $operation['relativePath']));
        }

        if ($blockedLinks !== []) {
            $this->error('Des liens symboliques existent dans les cibles gérées. Ils ne sont jamais suivis ou remplacés.');

            return self::FAILURE;
        }

        if ($invalidTargets !== []) {
            $this->error('Des balises de gestion sont absentes, dupliquées ou mal ordonnées. Corrigez-les manuellement.');

            return self::FAILURE;
        }

        if ($conflicts !== [] && ! $this->option('force')) {
            $this->warn('Des fichiers ou blocs gérés diffèrent. Aucun fichier ne sera remplacé sans --force.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info('Simulation terminée : aucun fichier n’a été modifié.');

            return self::SUCCESS;
        }

        foreach ($operations as $operation) {
            if ($operation['kind'] !== 'core' || $operation['status'] === 'current') {
                continue;
            }

            $this->files->ensureDirectoryExists(dirname($operation['targetPath']));
            $this->files->copy($operation['sourcePath'], $operation['targetPath']);
        }

        foreach ($operations as $operation) {
            if ($operation['kind'] !== 'instruction' || $operation['status'] === 'current') {
                continue;
            }

            $this->writeInstructionOperation($operation);
        }

        $this->info('Noyau .agents et ponts Codex/Claude Code installés. Les skills sont gérés par Laravel Boost.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{kind: string, sourcePath: string, targetPath: string, relativePath: string, status: string}>
     */
    private function plannedCoreOperations(string $sourceDirectory, string $targetDirectory): array
    {
        $operations = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relativePath = $iterator->getSubPathname();
            $targetPath = $targetDirectory.DIRECTORY_SEPARATOR.$relativePath;

            $operations[] = [
                'kind' => 'core',
                'sourcePath' => $file->getPathname(),
                'targetPath' => $targetPath,
                'relativePath' => '.agents/'.str_replace(DIRECTORY_SEPARATOR, '/', $relativePath),
                'status' => $this->coreOperationStatus($file->getPathname(), $targetPath),
            ];
        }

        return $operations;
    }

    /**
     * @return array{kind: string, targetPath: string, relativePath: string, status: string, content: string}
     */
    private function plannedInstructionOperation(string $targetPath): array
    {
        $operation = [
            'kind' => 'instruction',
            'targetPath' => $targetPath,
            'relativePath' => basename($targetPath),
            'status' => 'create',
            'content' => '',
        ];

        if ($this->files->isLink($targetPath)) {
            $operation['status'] = 'link';

            return $operation;
        }

        if (! $this->files->exists($targetPath)) {
            $operation['content'] = $this->managedBlock().PHP_EOL;

            return $operation;
        }

        if (! $this->files->isFile($targetPath)) {
            $operation['status'] = 'invalid';

            return $operation;
        }

        $existing = $this->files->get($targetPath);
        $startCount = substr_count($existing, self::MANAGED_BLOCK_START);
        $endCount = substr_count($existing, self::MANAGED_BLOCK_END);

        if ($startCount === 0 && $endCount === 0) {
            $operation['status'] = 'append';
            $operation['content'] = $this->appendManagedBlock($existing);

            return $operation;
        }

        $start = strpos($existing, self::MANAGED_BLOCK_START);
        $end = strpos($existing, self::MANAGED_BLOCK_END);

        if ($startCount !== 1 || $endCount !== 1 || $start === false || $end === false || $end < $start) {
            $operation['status'] = 'invalid';

            return $operation;
        }

        $end += strlen(self::MANAGED_BLOCK_END);
        $replacement = substr($existing, 0, $start).$this->managedBlock().substr($existing, $end);
        $operation['content'] = $replacement;
        $operation['status'] = $replacement === $existing ? 'current' : 'conflict';

        return $operation;
    }

    /**
     * @param array{kind: string, targetPath: string, relativePath: string, status: string, content: string} $operation
     */
    private function writeInstructionOperation(array $operation): void
    {
        $this->files->ensureDirectoryExists(dirname($operation['targetPath']));
        $this->files->put($operation['targetPath'], $operation['content']);
    }

    private function coreOperationStatus(string $sourcePath, string $targetPath): string
    {
        if ($this->files->isLink($targetPath)) {
            return 'link';
        }

        if (! $this->files->exists($targetPath)) {
            return 'create';
        }

        if (! $this->files->isFile($targetPath)) {
            return 'invalid';
        }

        return hash_equals(
            (string) hash_file('sha256', $sourcePath),
            (string) hash_file('sha256', $targetPath),
        ) ? 'current' : 'conflict';
    }

    private function appendManagedBlock(string $existing): string
    {
        if ($existing === '') {
            return $this->managedBlock().PHP_EOL;
        }

        $separator = str_ends_with($existing, "\n") ? PHP_EOL : PHP_EOL.PHP_EOL;

        return $existing.$separator.$this->managedBlock().PHP_EOL;
    }

    private function managedBlock(): string
    {
        return <<<'MARKDOWN'
<mc-harness-ai>
Pour chaque demande, avant toute action ou délégation :

1. Charger `mc-qualify-request`.
2. Lire `.agents/registry.yaml` lorsqu’il existe.
3. Classer la demande : directe, simple ou coordonnée.
4. Conserver la responsabilité des décisions partagées, du choix des rôles, de la revue et de la réponse finale.
5. Ne déléguer que des unités indépendantes, avec un propriétaire, un périmètre et une validation explicites.
6. Exécuter directement les demandes directes ou simples.
7. Ne demander une revue indépendante que si le risque, le contrat d’unité ou la demande utilisateur le justifie.

Les rôles spécialisés ne réorganisent pas le travail. Ils signalent au principal toute découverte qui change le périmètre, le risque ou l’objectif.
</mc-harness-ai>
MARKDOWN;
    }
}