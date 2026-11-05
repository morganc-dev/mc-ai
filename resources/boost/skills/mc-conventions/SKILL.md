---
name: mc-conventions
description: Apply shared conventions when writing or reviewing PHP, Laravel, Blade, Vue, TypeScript, JavaScript, Tailwind, a native mobile shell, or tests. Explicit project rules remain authoritative.
---

# Apply Web Conventions

These conventions are personal cross-project defaults, not universal rules. They apply only when the project has no clear conflicting convention. Never refactor an existing project merely to align it with this skill. Before creating an abstraction, component, or module, search the repository for an existing equivalent.

## Precedence

1. The explicit request contract and local project rules.
2. This skill's references.
3. Package documentation and framework guidance.
4. Language defaults.

Report conflicts; never resolve them silently.

## Identifiers

- Write every technical identifier in English: classes, methods, functions, variables, constants, props, events, composables, types, routes, configuration keys, tables, and columns.
- This rule does not cover user-facing text, translations, content, or public URLs, whose language belongs to the project.

## Stack Identification

Before any frontend work, establish the technologies the project actually uses, in this order: project instructions, configuration files and installed dependencies, existing components and conventions, choices the user states explicitly.

- Do not infer Blade from the presence of Laravel, nor TypeScript, Tailwind, or a native shell from the presence of Vue.
- There is no default frontend stack. When a stack is identifiable, continue with it and ask nothing.
- Ask for a decision, with a reasoned recommendation, only when a new area genuinely requires a technical choice that no project convention settles.

## Usage

Load only the references matching the affected files and the identified stack:

| Reference | Load for |
|---|---|
| [php.md](references/php.md) | PHP and Laravel. |
| [blade.md](references/blade.md) | Blade views and components. |
| [vue.md](references/vue.md) | `.vue` components, composables, and component libraries. |
| [typescript.md](references/typescript.md) | `.ts` files and `<script setup lang="ts">`. |
| [javascript.md](references/javascript.md) | JavaScript without TypeScript, and vanilla modules of a project without a frontend framework. |
| [tailwind.md](references/tailwind.md) | Any styling: Tailwind, CSS, theme tokens, and the styling strategy. |
| [mobile.md](references/mobile.md) | Only when the project ships a native shell such as Capacitor and the task touches its specifics. |
| [tests.md](references/tests.md) | Test strategy, Pest, and frontend tests. |

Apply the rules during implementation. During review, cite the exact rule or its consequence; an opinion unsupported by these references remains a preference.
