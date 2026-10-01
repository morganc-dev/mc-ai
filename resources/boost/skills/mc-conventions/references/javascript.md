# JavaScript Conventions

- Use native ES6 JavaScript; no framework without an explicit project decision.
- Keep modules simple, with one identifiable responsibility each.
- Each module exports an initialization function or class and has no automatic side effect on import.
- Keep `app.js` as the entry point that imports and initializes modules.
- Do not introduce an abstraction layer or dependency when the browser API is sufficient.
- Respect `prefers-reduced-motion` for all non-essential motion.
