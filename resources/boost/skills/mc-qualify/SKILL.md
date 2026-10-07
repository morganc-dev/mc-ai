---
name: mc-qualify
description: Qualify a user request on a web project, choose between direct execution and specialized delegation, and define work units without relying on a particular orchestration tool.
---

# Qualify a Request

## Objective

Move a request forward through the shortest path that preserves clear ownership and validation proportional to risk.

## Classification

- **Direct**: an answer or diagnosis with no modification and no distinct specialist expertise.
- **Simple**: a bounded, reversible, low-risk modification with a single owner and no unresolved product, data, security, or architectural decision.
- **Coordinated**: multiple domains, dependent deliverables, product decisions, architecture, security, sensitive data, significant cost, or low reversibility.

## Procedure

1. Read the request, project instructions and relevant files. Find verifiable facts in the repository instead of asking for them.
2. Collect only the context needed to classify the request and avoid asking for facts the repository can establish. Inspect neighboring code and tests, local conventions, existing briefs or audits, the database schema when data is affected, and primary documentation for an uncertain or versioned API.
3. Identify the objective, observable outcome, scope, non-goals, constraints, and genuinely open decisions.
4. Ask only the questions whose answers materially change the behavior, scope, data, security boundary, architecture, or implementation plan. Do not ask for facts that the collected context can establish.
5. For a direct request, answer or diagnose without producing an implementation plan.
6. For a simple implementation request, state the intended outcome, affected area, and validation in a concise change proposal. Do not implement it until the user explicitly approves it.
7. For a coordinated request, turn the settled facts and decisions into a concise plan: outcome, affected areas or files, ordered implementation steps, validation, risks, and remaining questions. Describe what will change, not code-level mechanics. Do not implement it until the user explicitly approves it.
8. Decide whether delegation provides a clear net benefit. Direct execution remains valid for simple and coordinated work.
9. When delegating, settle shared decisions first and define independent work units with [contracts/work-unit.md](contracts/work-unit.md), an owner, scope, inputs, files allowed to modify, and validation.
10. Run only independent units in parallel. Keep sequential or tightly coupled work with the main agent.
11. Request an independent review when risk, the unit contract, or the user asks for it. The reviewer reports with [contracts/review.md](contracts/review.md).
12. Consolidate delegated handoffs, each written with [contracts/handoff.md](contracts/handoff.md), into one coherent response. The user must not have to reconstruct the result from specialist reports.

## Delegation Decision

Delegation is optional. The main agent may complete the entire request, including all modifications.

Delegate only when:

- the work can be split into independent, well-defined units;
- those units can run in parallel without sharing unresolved decisions or overlapping files;
- specialized expertise materially improves the result; or
- the expected gain in elapsed time or focused context outweighs the coordination cost.

Do not delegate work that is small, sequential, tightly coupled, or cheaper for the main agent to complete directly.

The main agent retains ownership of shared decisions, integration, validation, and the final response. Complexity alone does not require delegation.

## Roles

The main agent owns orchestration. A role is a set of instructions that the main agent either applies itself or hands to a subagent when the tool supports one. Roles never reorganize the work. They report any discovery that changes scope, risk, or objective.

| Role | Purpose | Instructions | Skills |
|---|---|---|---|
| mc-laravel-developer | Implement bounded, lean, and verified Laravel and web changes. | [roles/mc-laravel-developer.md](roles/mc-laravel-developer.md) | mc-dev, mc-conventions |
| mc-digital-designer | Define a visual direction and produce implementation-ready design deliverables. | [roles/mc-digital-designer.md](roles/mc-digital-designer.md) | mc-design, mc-conventions for Blade or Tailwind |
| mc-content-strategist | Turn verified facts into useful, discoverable, conversion-oriented content. | [roles/mc-content-strategist.md](roles/mc-content-strategist.md) | mc-content |
| mc-reviewer | Independently verify a delivery and return an evidence-based verdict. | [roles/mc-reviewer.md](roles/mc-reviewer.md) | mc-review, mc-security when a security boundary is involved, mc-conventions |

## Questions

Ask only for decisions that materially change the behavior, scope, data, security boundary, architecture, or plan. Group them into rounds and include a recommendation with its consequence. Information available in the repository, a supplied document, or a verifiable source is not a question to send back to the user.

When many shared decisions remain open, suggest that the user run `/mc-challenge` instead of chaining rounds here. A `challenge-<subject>.md` record, when one exists, is the starting point of the qualification.

## Plan Format

For a coordinated request, keep the plan concise and use this structure:

1. **Summary**: the understood outcome in one or two sentences.
2. **Affected areas**: files or components to create or modify, with their purpose when known.
3. **Implementation steps**: ordered by dependency and expressed as observable changes, without pseudo-code, method signatures, or snippets.
4. **Validation**: the checks that will demonstrate the behavior.
5. **Attention points**: one sentence each for material risks, edge cases, or non-regression constraints.
6. **Open questions**: only if a decision still prevents a reliable plan.

## Guardrails

- The main agent does not silently perform work already assigned to another owner.
- A discovery that changes the objective or risk suspends the affected unit; it does not authorize widening the contract.
- Commit, push, deployment, and other external actions remain separate explicit decisions.
