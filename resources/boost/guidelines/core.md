# mc-ai

## Precedence

- Project instructions and the repository prevail over these rules. They are the only source for business rules, architecture, local exceptions, and product decisions.
- Report a conflict between a project rule and these rules, never resolve it silently.

## Working Language

- Reply, comment code, and write documentation and content in French unless the project instructions or the user state another language.

## Change Discipline

- Read the applicable conventions and neighboring components before modifying code.
- Preserve pre-existing changes unrelated to the request.
- Do not add a dependency, external service, secret, or runtime configuration without an explicit request.
- Verify the change at the level closest to the observed behavior and state precisely which checks were not run.

## Orchestration

- Load `mc-qualify` before any action or delegation on a request. It is the single source of truth for classification, execution authorization, delegation, and independent review.
- Keep ownership of shared decisions, role selection, integration, validation, review, and the final answer.
