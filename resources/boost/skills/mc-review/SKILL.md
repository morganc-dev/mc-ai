---
name: mc-review
description: Independently audit code, an interface, or another deliverable with evidence and a bounded verdict. Never use this skill to fix the audited target.
---

# Review a Delivery

## Baseline

Evaluate in this order: explicit objective, project sources of truth, local conventions, framework or dependency contracts, then general heuristics. A project-specific rule wins when it conflicts with a generic rule.

## Procedure

1. Define the target, scope, expected outcome, and applicable criteria. If no baseline exists, state a narrow and reasonable one before judging.
2. Read the complete target and its useful context. For code, load relevant references from `mc-conventions`; read [references/code-smells.md](references/code-smells.md) only when maintainability is part of the review.
3. Verify the author's claims. Start with the cheapest targeted check and broaden only when the result can change the verdict.
4. For an interface, inspect the render, relevant widths and states, observable accessibility, console, and behavior without JavaScript when that is a product contract.
5. Classify each point as a demonstrated defect, credible but unproven risk, optional improvement, or user-owned decision.
6. Report with the review contract in [../mc-qualify/contracts/review.md](../mc-qualify/contracts/review.md). Order findings by consequence.

## Invariants

- A passing tool does not by itself prove that the objective was met.
- Every actionable finding includes a location, evidence, consequence, and minimum acceptable outcome.
- An unavailable check is a limitation, never a success.
- The audited target must remain unchanged.
