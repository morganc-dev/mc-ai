# mc-harness-ai

Skills Boost et noyau `.agents` pour projets Laravel. Les rôles sont des consignes, pas des sous-agents créés automatiquement.

Installation (accès Git requis ; sélectionner les skills Codex et/ou Claude Code dans Boost) :

```bash
composer config repositories.mc-harness-ai vcs git@github.com:morganc-dev/mc-harness-ai.git
composer require morganc-dev/mc-harness-ai:dev-main --dev
php artisan boost:install
php artisan mc-harness-ai:install --dry-run
php artisan mc-harness-ai:install
```

Mise à jour :

```bash
composer update morganc-dev/mc-harness-ai
php artisan boost:update --discover
php artisan mc-harness-ai:install --dry-run
php artisan mc-harness-ai:install
```

En cas de conflits, `--force` sauvegarde les fichiers remplacés dans `.mc-harness-ai/backups/`. Les fichiers retirés du package restent dans le projet et se suppriment manuellement.

Tests, depuis ce dépôt après `composer install` : `composer test`.
