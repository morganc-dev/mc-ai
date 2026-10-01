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

Before any action or delegation on a request:

1. Load `mc-qualify`.
2. Identify the available roles from its role table.
3. Classify the request as direct, simple, or coordinated.
4. Keep ownership of shared decisions, role selection, review, and the final answer.
5. Delegate only units with an explicit owner, scope, and validation. Run in parallel only independent ones.
6. Answer direct requests yourself. Delegate every modification, even a simple one, as one work unit to a role.
7. Request an independent review only when risk, the unit contract, or the user asks for it.
