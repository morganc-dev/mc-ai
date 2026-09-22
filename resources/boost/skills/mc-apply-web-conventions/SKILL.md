---
name: mc-apply-web-conventions
description: Apply shared conventions when writing or reviewing PHP, Laravel, Blade, Tailwind, JavaScript, or tests. Explicit project rules remain authoritative.
---

# Apply Web Conventions

These conventions are personal cross-project defaults, not universal Laravel rules. They apply only when the project has no clear conflicting convention. Never refactor an existing project merely to align it with this skill. Before creating an abstraction, component, or module, search the repository for an existing equivalent.

## Precedence

1. The explicit request contract and local project rules.
2. This skill's references.
3. Package documentation and framework guidance.
4. Language defaults.

Report conflicts; never resolve them silently.

## Usage

Load only the references matching the affected files:

- [references/php.md](references/php.md) for PHP and Laravel;
- [references/frontend.md](references/frontend.md) for Blade, Tailwind, and CSS;
- [references/javascript.md](references/javascript.md) for JavaScript;
- [references/tests.md](references/tests.md) for Pest and test strategy.

Apply the rules during implementation. During review, cite the exact rule or its consequence; an opinion unsupported by these references remains a preference.
