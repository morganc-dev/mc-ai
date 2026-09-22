# mc-harness-ai

```bash
composer config repositories.mc-harness-ai vcs git@github.com:morganc-dev/mc-harness-ai.git
composer require morganc-dev/mc-harness-ai --dev
php artisan boost:install
php artisan mc-harness-ai:install --dry-run
php artisan mc-harness-ai:install
```

Mise à jour :

```bash
composer update morganc-dev/mc-harness-ai
php artisan mc-harness-ai:install --force
php artisan boost:update --discover
```

Vérification : `composer test`.
