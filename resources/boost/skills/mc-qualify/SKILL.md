---
name: mc-qualify
description: Qualify a user request on a web project, choose between direct execution and specialized delegation, and define work units without relying on a particular orchestration tool.
---

# Qualify a Request

## Objective

Move a request forward through the shortest path that preserves clear ownership and validation proportional to risk.

## Classification

- **Direct**: an answer or diagnosis with no modification and no distinct specialist expertise.
- **Simple**: a bounded, reversible modification with a single owner.
- **Coordinated**: multiple domains, dependent deliverables, product decisions, architecture, security, sensitive data, significant cost, or low reversibility.

## Procedure

1. Read the request, project instructions and relevant files. Find verifiable facts in the repository instead of asking for them.
2. Identify the objective, observable outcome, scope, non-goals, constraints, and genuinely open decisions.
3. For simple work, define one autonomous work unit with [contracts/work-unit.md](contracts/work-unit.md) and assign it to one role from the table below.
4. For coordinated work, first settle decisions shared by several roles. Then slice by autonomous deliverable, never by an artificial technical layer, and assign each unit to one role from the table below.
5. Run only independent units in parallel. Two roles must never settle the same decision separately.
6. Request an independent review when risk, the unit contract, or the user asks for it. The reviewer reports with [contracts/review.md](contracts/review.md).
7. Consolidate the handoffs, each written with [contracts/handoff.md](contracts/handoff.md), into one coherent response. The user must not have to reconstruct the result from specialist reports.

## Roles

The main agent owns orchestration. A role is a set of instructions that the main agent either applies itself or hands to a subagent when the tool supports one. Roles never reorganize the work. They report any discovery that changes scope, risk, or objective.

| Role | Purpose | Instructions | Skills |
|---|---|---|---|
| mc-laravel-developer | Implement bounded, lean, and verified Laravel and web changes. | [roles/mc-laravel-developer.md](roles/mc-laravel-developer.md) | mc-dev, mc-conventions |
| mc-digital-designer | Define a visual direction and produce implementation-ready design deliverables. | [roles/mc-digital-designer.md](roles/mc-digital-designer.md) | mc-design, mc-conventions for Blade or Tailwind |
| mc-content-strategist | Turn verified facts into useful, discoverable, conversion-oriented content. | [roles/mc-content-strategist.md](roles/mc-content-strategist.md) | mc-content |
| mc-reviewer | Independently verify a delivery and return an evidence-based verdict. | [roles/mc-reviewer.md](roles/mc-reviewer.md) | mc-review, mc-security when a security boundary is involved, mc-conventions |

## Questions

Ask only for decisions that materially change the next steps. Group them into rounds and include a recommendation with its consequence. Information available in the repository, a supplied document, or a verifiable source is not a question to send back to the user.

## Guardrails

- The main agent does not silently perform work already assigned to another owner.
- A discovery that changes the objective or risk suspends the affected unit; it does not authorize widening the contract.
- Commit, push, deployment, and other external actions remain separate explicit decisions.
