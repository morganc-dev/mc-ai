# TypeScript Conventions

- Rely on the types shipped by libraries and frameworks before writing your own.
- Type business contracts and every external datum explicitly: API responses, route params, storage, and messages from a native shell.
- Distinguish an API payload from the internal model only when their shapes or lifecycles differ; otherwise reuse one type.
- Avoid `any` whenever a more precise type exists; use `unknown` and narrow it for genuinely untyped input.
- Do not write a type assertion (`as`, `!`) without a reason the reader can verify in the surrounding code.
- Reuse and derive existing types instead of duplicating shapes.
- Do not create an interface for every object or an architecture of generic abstractions; rely on inference where it is clear.
- Never loosen the project's compiler options to make a change pass.
