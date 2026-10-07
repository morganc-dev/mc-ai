---
name: mc-dev
description: Implement or fix bounded Laravel or web behavior with validation at the right seam. Use for code changes, not for product, content, or design decisions.
---

# Implement a Laravel Change

## Inputs

- The request or an assigned work unit when one exists.
- An explicitly approved implementation plan or concise change proposal.
- Project instructions and local rules covering the affected files.
- Content and design briefs when they exist.
- Neighboring components, modules, and tests as prior art.

## Procedure

1. Record the repository state and preserve pre-existing changes.
2. Read installed versions before relying on a package API. Verify an uncertain or versioned API in the available primary documentation before using it.
3. When data is affected, inspect the actual database schema before changing a migration, model relation, or query.
4. Load `mc-conventions` and only the references matching the files in scope.
5. Choose the highest public seam where the behavior is observable: route, service, rendered view, or browser interaction.
6. Implement the smallest change that fully delivers the behavior. Prefer a vertical slice over layer-by-layer construction.
7. Add or adapt a test when the behavior or contract justifies one. Do not test guarantees already provided by the framework.
8. Run the narrowest validations during development, then proportionate final checks: relevant tests, static analysis and formatting for PHP, build and browser pass for interface work.
9. Visually verify interface changes and inspect the console. State precisely what could not be run.
10. Report the outcome, files changed, validation, assumptions, residual risks and unverified items.

## Stop Conditions

Stop and escalate when a discovery changes the objective, scope, expected architecture, security, data handling, cost, or reversibility. A nearby issue remains out of scope and is reported as a residual risk.
