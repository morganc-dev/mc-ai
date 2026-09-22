<?php

declare(strict_types=1);

namespace MorgancDev\McHarnessAi\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Throwable;

final class InstallAgentsCommand extends Command
{
    private const MANAGED_BLOCK_START = '<mc-harness-ai>';

    private const MANAGED_BLOCK_END = '</mc-harness-ai>';

    private const QUALIFICATION_SKILLS = [
        'AGENTS.md' => '.agents/skills/mc-qualify-request/SKILL.md',
        'CLAUDE.md' => '.claude/skills/mc-qualify-request/SKILL.md',
    ];

    protected $signature = 'mc-harness-ai:install
                            {--dry-run : Affiche les opérations sans écrire de fichier}
                            {--force : Sauvegarde puis remplace les fichiers et blocs gérés qui diffèrent}';

    protected $description = 'Installe le noyau .agents et les ponts des agents sélectionnés dans Boost.';

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

        $instructionPaths = $this->availableInstructionPaths();

        if ($instructionPaths === []) {
            $this->error('Aucun skill mc-qualify-request de Boost trouvé. Exécutez boost:install avec les skills Codex ou Claude Code, puis relancez cette commande.');

            return self::FAILURE;
        }

        $operations = [
            ...$this->plannedCoreOperations($sourceDirectory, $targetDirectory),
            ...array_map(
                fn (string $path): array => $this->plannedInstructionOperation(base_path($path)),
                $instructionPaths,
            ),
        ];

        $conflicts = array_filter($operations, fn (array $operation): bool => $operation['status'] === 'conflict');
        $blockedLinks = array_filter($operations, fn (array $operation): bool => $operation['status'] === 'link');
        $invalidTargets = array_filter($operations, fn (array $operation): bool => $operation['status'] === 'invalid');

        foreach ($operations as $operation) {
            $status = $operation['status'] === 'conflict' && $this->option('force') ? 'backup+replace' : $operation['status'];
            $this->line(sprintf('[%s] %s', $status, $operation['relativePath']));
        }

        if ($blockedLinks !== []) {
            $this->error('Des liens symboliques existent dans les cibles gérées. Ils ne sont jamais suivis ou remplacés.');

            return self::FAILURE;
        }

        if ($invalidTargets !== []) {
            $this->error('Des cibles sont invalides ou leurs balises de gestion sont absentes, dupliquées ou mal ordonnées.');

            return self::FAILURE;
        }

        if ($conflicts !== [] && ! $this->option('force')) {
            $this->warn('Des fichiers ou blocs gérés diffèrent. Utilisez --force après examen pour les sauvegarder et les remplacer.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info('Simulation terminée : aucun fichier n’a été modifié.');

            return self::SUCCESS;
        }

        try {
            $writes = $this->prepareWrites($operations);
            $backupDirectory = $this->backupConflicts($writes);
        } catch (Throwable $exception) {
            $this->error('Préparation interrompue : '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! $this->applyWrites($writes)) {
            return self::FAILURE;
        }

        if ($backupDirectory !== null) {
            $this->warn('Copies précédentes conservées dans '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $backupDirectory));
        }

        $this->info('Noyau .agents et ponts sélectionnés installés. Les skills sont gérés par Laravel Boost.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function availableInstructionPaths(): array
    {
        $paths = [];

        foreach (self::QUALIFICATION_SKILLS as $instructionPath => $skillPath) {
            if ($this->files->isFile(base_path($skillPath))) {
                $paths[] = $instructionPath;
            }
        }

        return $paths;
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

        if (is_link($targetPath)) {
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

    private function coreOperationStatus(string $sourcePath, string $targetPath): string
    {
        if ($this->hasLinkedAncestor($targetPath)) {
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

    private function hasLinkedAncestor(string $path): bool
    {
        $root = rtrim(base_path(), DIRECTORY_SEPARATOR);
        $current = $path;

        while (str_starts_with($current, $root.DIRECTORY_SEPARATOR)) {
            if (is_link($current)) {
                return true;
            }

            $current = dirname($current);
        }

        return false;
    }

    /**
     * @param array<int, array<string, string>> $operations
     * @return array<int, array{targetPath: string, relativePath: string, status: string, content: string, original: ?string}>
     */
    private function prepareWrites(array $operations): array
    {
        $writes = [];

        foreach ($operations as $operation) {
            if ($operation['status'] === 'current') {
                continue;
            }

            $targetPath = $operation['targetPath'];
            $writes[] = [
                'targetPath' => $targetPath,
                'relativePath' => $operation['relativePath'],
                'status' => $operation['status'],
                'content' => $operation['kind'] === 'core'
                    ? $this->files->get($operation['sourcePath'])
                    : $operation['content'],
                'original' => $this->files->exists($targetPath) ? $this->files->get($targetPath) : null,
            ];
        }

        return $writes;
    }

    /**
     * @param array<int, array{targetPath: string, relativePath: string, status: string, content: string, original: ?string}> $writes
     */
    private function backupConflicts(array $writes): ?string
    {
        $conflicts = array_filter($writes, fn (array $write): bool => $write['status'] === 'conflict');

        if ($conflicts === []) {
            return null;
        }

        $directory = base_path('.mc-harness-ai/backups/'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(4)));

        foreach ($conflicts as $write) {
            $path = $directory.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $write['relativePath']);
            $this->writeChecked($path, $write['original'] ?? '');
        }

        return $directory;
    }

    /**
     * @param array<int, array{targetPath: string, relativePath: string, status: string, content: string, original: ?string}> $writes
     */
    private function applyWrites(array $writes): bool
    {
        $journal = [];

        try {
            foreach ($writes as $write) {
                $journal[] = $write;
                $this->writeChecked($write['targetPath'], $write['content']);
            }
        } catch (Throwable $exception) {
            $rollbackErrors = [];

            foreach (array_reverse($journal) as $write) {
                try {
                    if ($write['original'] === null) {
                        if ($this->files->exists($write['targetPath']) && ! $this->files->delete($write['targetPath'])) {
                            throw new RuntimeException('Suppression impossible.');
                        }
                    } else {
                        $this->writeChecked($write['targetPath'], $write['original']);
                    }
                } catch (Throwable $rollbackException) {
                    $rollbackErrors[] = $write['relativePath'].' : '.$rollbackException->getMessage();
                }
            }

            $this->error('Installation interrompue : '.$exception->getMessage());

            if ($rollbackErrors !== []) {
                $this->error('Restauration incomplète : '.implode(' ; ', $rollbackErrors));
            }

            return false;
        }

        return true;
    }

    private function writeChecked(string $path, string $content): void
    {
        if ($this->hasLinkedAncestor($path)) {
            throw new RuntimeException('Lien symbolique dans la cible : '.$path);
        }

        $this->files->ensureDirectoryExists(dirname($path));
        $written = $this->files->put($path, $content, true);

        if ($written !== strlen($content)) {
            throw new RuntimeException('Écriture incomplète : '.$path);
        }
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
