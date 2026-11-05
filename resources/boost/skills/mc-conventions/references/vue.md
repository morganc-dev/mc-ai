# Vue 3 Conventions

## Components

- Write Single File Components with `<script setup>` and the Composition API unless the project follows another style.
- Name component files in PascalCase and composables `useX`, unless the project has another convention. This is an `mc-ai` default.
- Declare every prop and every emitted event explicitly, with types when TypeScript is present. A component's interface must be readable from its declarations alone.
- Keep a component to one identifiable responsibility; split by meaning, not by size.
- Handle loading, error, and empty states explicitly in every component that fetches or receives data.
- Preserve native semantics and accessibility: keyboard, focus, labels, and announced states.

## Composables and Logic

- Extract a composable only for stateful logic reused across components, or when it makes a large component readable. Logic that stays clearer inside the component stays there.
- Keep business logic and API calls in services or composables, never in templates or router guards.
- Do not introduce Pinia, a form library, or another abstraction without an explicit project decision. Local reactive state, or state shared through a composable, is enough until proven otherwise.

## Component Libraries

- Search the installed library before creating a component; do not reimplement what it provides.
- Respect the library's API, composition patterns, and conventions; extend through its variants and theme tokens.
- Preserve the accessibility and behaviors the library ships.
