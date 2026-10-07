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
2. Classify the request as direct, simple, or coordinated.
3. Decide whether delegation provides a clear net benefit. It is optional, including for complex or coordinated requests.
4. When delegation is justified, identify the relevant available roles from its role table and define only well-defined, independent units. Keep small, sequential, tightly coupled, or shared-decision work with the main agent.
5. Keep ownership of shared decisions, role selection, integration, validation, review, and the final answer.
6. Request an independent review only when risk, the unit contract, or the user asks for it.
