---
name: mc-qualify-request
description: Qualify a web request, choose between direct execution and specialized delegation, and define work units without relying on a particular orchestration tool.
---

# Qualify a Request

## Objective

Move a request forward through the shortest path that preserves clear ownership and validation proportional to risk.

## Classification

- **Direct**: an answer or diagnosis with no modification and no distinct specialist expertise.
- **Simple**: a bounded, reversible modification with a single owner.
- **Coordinated**: multiple domains, dependent deliverables, product decisions, architecture, security, sensitive data, significant cost, or low reversibility.

## Procedure

1. Read the request, project instructions, relevant files and any local role registry. Find verifiable facts in the repository instead of asking for them.
2. Identify the objective, observable outcome, scope, non-goals, constraints, and genuinely open decisions.
3. For simple work, define one autonomous work unit with its objective, scope, allowed files, required validation and escalation conditions.
4. For coordinated work, first settle decisions shared by several roles. Then slice by autonomous deliverable, never by an artificial technical layer.
5. Run only independent units in parallel. Two roles must never settle the same decision separately.
6. Request an independent review of material changes when it provides meaningful assurance.
7. Consolidate handoffs into one coherent response. The user must not have to reconstruct the result from specialist reports.

## Questions

Ask only for decisions that materially change the next steps. Group them into rounds and include a recommendation with its consequence. Information available in the repository, a supplied document, or a verifiable source is not a question to send back to the user.

## Guardrails

- The orchestrator does not silently perform work already assigned to another owner.
- A discovery that changes the objective or risk suspends the affected unit; it does not authorize widening the contract.
- Commit, push, deployment, and other external actions remain separate explicit decisions.
