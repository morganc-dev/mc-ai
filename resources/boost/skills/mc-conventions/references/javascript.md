# JavaScript Conventions

- Use native JavaScript with ES modules.
- Keep modules simple, with one identifiable responsibility each.
- Do not introduce an abstraction layer or dependency when the browser API is sufficient.
- Respect `prefers-reduced-motion` for all non-essential motion.

## Vanilla Modules

In a project without a frontend framework:

- Each module exports an initialization function or class and has no automatic side effect on import.
- Keep `app.js` as the entry point that imports and initializes modules.
