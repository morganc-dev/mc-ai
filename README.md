# mc-ai

Conventions, procédures et rôles réutilisables pour mes projets Laravel, distribués par Laravel Boost. Le package ne contient que du markdown et ne suppose aucune configuration de poste, ni agent, ni serveur MCP, ni secret, ni chemin. Boost installe, met à jour et retire tout ce qu’il livre.

## Contenu

Le dossier `resources/boost/guidelines/` contient `core.md`. Boost l'injecte dans le fichier d'instructions de chaque agent sélectionné, `AGENTS.md` ou `CLAUDE.md` selon l'outil. Il fixe les frontières de responsabilité, la langue de travail, la discipline de changement et les sept étapes d'orchestration.

Le dossier `resources/boost/skills/` contient sept skills. Boost les copie dans le dossier de skills de chaque agent.

| Skill | Usage |
|---|---|
| `mc-qualify` | Qualifier une demande, choisir entre exécution directe et délégation, définir les unités de travail. Il porte aussi les rôles et les contrats. |
| `mc-dev` | Implémenter un changement Laravel ou web borné et vérifié. |
| `mc-conventions` | Conventions PHP, Blade, Tailwind, JavaScript et tests. |
| `mc-design` | Direction visuelle et livrables de design prêts à implémenter. |
| `mc-content` | Fiche produit, brief de page, contenu, SEO on-page et conversion. |
| `mc-review` | Revue indépendante d'une livraison, sans modifier la cible. |
| `mc-security` | Revue de sécurité d'un changement Laravel ou web. |

Les rôles sont des consignes que l'agent principal applique lui-même ou confie à un sous-agent quand l'outil le permet. Ils vivent dans `mc-qualify/roles/` et rendent compte avec les contrats de `mc-qualify/contracts/`.

## Installation

Le package exige un accès Git au dépôt et s’installe en dépendance de développement. Au premier prompt de `boost:install`, cocher « AI Guidelines » et « Agent Skills ». Au prompt des packages tiers, cocher `morganc-dev/mc-ai`. Choisir ensuite les intégrations et les agents.

```bash
composer config repositories.mc-ai vcs git@github.com:morganc-dev/mc-ai.git
composer require morganc-dev/mc-ai:dev-main --dev
php artisan boost:install
```

## Mise à jour

```bash
composer update morganc-dev/mc-ai
php artisan boost:update
```

Boost réécrit le bloc de guidelines et recopie chaque skill. Un skill renommé ou retiré du package est supprimé du projet.

## Adaptation locale

Une instruction explicite du projet prévaut sur le package. Les règles propres à un projet se placent dans son fichier d'instructions ou dans `.ai/guidelines/`, jamais dans les fichiers installés par Boost, qui sont écrasés à chaque mise à jour.

## Migration depuis `mc-harness-ai`

Les versions précédentes s’appelaient `morganc-dev/mc-harness-ai` et installaient un dossier `.agents/` et un bloc `<mc-harness-ai>` dans `AGENTS.md` ou `CLAUDE.md`. Le nom du package ayant changé, `composer update` ne suffit pas, et `boost.json` référence encore l’ancien nom. Procéder dans cet ordre :

```bash
composer remove morganc-dev/mc-harness-ai
composer config --unset repositories.mc-harness-ai
composer config repositories.mc-ai vcs git@github.com:morganc-dev/mc-ai.git
composer require morganc-dev/mc-ai:dev-main --dev
php artisan boost:install
```

Au prompt des packages tiers, cocher `morganc-dev/mc-ai`. Boost supprime les anciens skills et installe les nouveaux. Supprimer ensuite à la main `.agents/registry.yaml`, `.agents/roles/`, `.agents/contracts/`, le dossier `.mc-harness-ai/` et le bloc `<mc-harness-ai>`. Conserver `.agents/skills/` seulement si un agent qui l’utilise est installé, comme Codex ou OpenCode. Claude Code utilise `.claude/skills/`.
