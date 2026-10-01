---
name: mc-challenge
description: Challenge a loose idea, need, or plan through rounds of questions until every decision is settled, and keep a record that mc-qualify can reuse. Use only when the user explicitly asks for it, for example with /mc-challenge.
disable-model-invocation: true
---

# Challenge a Need

## Objective

Turn a loose idea into explicit decisions that the user can defend and that `mc-qualify` can reuse without asking again. Nothing is executed before the user confirms the shared understanding.

## Design Tree

Model the need as a tree in which every decision opens the decisions that depend on it. The **frontier** is the set of open decisions whose prerequisites are already settled. Asking only the frontier avoids questions built on answers the user has not given yet.

Seed the first branches from the references that already list what a domain must settle:

- design: "Inputs to Settle" in [intake.md](../mc-design/references/intake.md);
- content: "Required Content" in [page-brief.md](../mc-content/references/page-brief.md), or the sections of [product.md](../mc-content/references/product.md);
- development: the fields of [work-unit.md](../mc-qualify/contracts/work-unit.md).

Keep only the branches that matter to the subject. A seed list is a starting point, not a form to fill.

## Procedure

1. Look for the record of this subject. If it exists, read it and resume from its open decisions instead of asking again what it settles.
2. Read the project instructions and relevant files. Finding facts is your job, either yourself or through a subagent when the tool supports one. Never ask the user for a fact that the repository or a supplied source can answer.
3. Ask the whole frontier in one round, then wait for the answers. In the first round, propose the subject name used for the record.
4. After each round, update the record, recompute the frontier and ask the next round. A pending lookup is an unsettled prerequisite: only the questions that depend on it wait.
5. Stop when the frontier is empty, every branch visited and nothing silently assumed. Summarize the settled decisions and ask the user to confirm the shared understanding.
6. Offer to continue with `mc-qualify`, and start it only with explicit consent.

## Round Format

Number questions continuously across rounds so the user can answer by number:

```markdown
❓ **Q1 – <title>**: <question, with the options when there are several>

➡️ <recommended answer and its consequence>

---
```

## What the Session Does Not Settle

- A question that needs something to look at, such as a mockup or a render, is not settled by rephrasing it. Record it as deferred and name the role that will settle it, for example `mc-digital-designer` for a mockup.
- When the subject crosses a security boundary, record it so that `mc-qualify` plans a review with `mc-security`.
- The session never produces a specialist deliverable. It prepares the work units of the roles listed in `mc-qualify`.

## Record

Write `challenge-<subject>.md` at the project root unless the project declares another location. Name it in kebab-case and write it in the working language, headings included. Create it after the first round and update it after every round, so that an interrupted session can resume in a new conversation.

```markdown
# Challenge: <subject>

## Objective and Observable Outcome
## Scope
## Out of Scope
## Constraints
## Settled Decisions
| # | Decision | Reason |
## Verified Facts
| Fact | Source |
## Deferred Decisions
| Decision | Owner role | Reason |
## Reviews to Plan
## Open Decisions
```

## Guardrails

- Decisions belong to the user, even when the recommendation looks obvious. "I don't know" is a valid answer: record the decision as open or deferred instead of guessing.
- The record is the only file the session modifies. Commit, push and other external actions remain separate explicit decisions.
- When the frontier keeps growing round after round, propose splitting the subject into several challenges, each with its own record.
