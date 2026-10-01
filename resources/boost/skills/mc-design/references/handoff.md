# Design Deliverables

Implementation must be able to start from the files without depending on the conversation.

## System

When a project defines `DESIGN.md` or equivalent design documentation, it contains normative tokens and their rationale. Names are semantic and align with the framework's namespaces. For Tailwind v4:

| Concept | Variable |
| --- | --- |
| role-based color | `--color-<role>` |
| font family | `--font-<role>` |
| spacing | `--spacing-<step>` |
| radius | `--radius-<size>` |

Keep the theme and component classes in the project's existing CSS entry point or documented equivalent. Do not create a second naming system.

## Page Specification

Create a specification only when it is requested, using the project's established location. Include:

1. goal and primary action;
2. content in reading order;
3. structure, headings, and first viewport;
4. behavior in narrow, medium, and wide containers;
5. components and states: default, hover, focus, active, disabled, loading, empty, error, and success;
6. motion and reduced-motion behavior;
7. assets, source, format, sizes, and alt-text intent;
8. interface copy not covered by the content brief;
9. expected visual and functional checks.

## Content

- Use approved content unchanged.
- Keep `[TO PROVIDE: ...]` markers visible.
- Mark non-factual copy added only to test rhythm as `[PROVISIONAL]`.
- If content does not fit, request a rewrite with the exact constraint; never rewrite it silently.
