# mc-harness

## Frontières de responsabilité

- Ce package fournit uniquement des conventions et procédures transverses.
- Le dépôt applicatif reste la source de vérité pour le métier, l'architecture, les exceptions locales et les décisions de produit.
- La configuration de poste reste locale : agents, MCP externes, secrets, clés, chemins et profils d'exécution ne sont ni attendus ni configurés ici.
- Une instruction explicite du projet concerné prévaut sur une convention de ce package. Signaler le conflit ; ne pas le résoudre silencieusement.

## Discipline de changement

- Lire les conventions applicables et les composants voisins avant de modifier le code.
- Préserver les modifications préexistantes sans rapport avec la demande.
- Ne pas ajouter de dépendance, de service externe, de secret ou de configuration d'exécution sans demande explicite.
- Vérifier le changement au niveau le plus proche du comportement observé et indiquer précisément les contrôles non exécutés.
