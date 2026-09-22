---
name: mc-security-review
description: Review a Laravel or web change for security defects at authentication, authorization, input, data, browser, integration, file, and secret boundaries. Do not modify the audited target.
---

# Review Laravel Security

## Scope

Use this skill when reviewing a change that handles a request, identity, permission, model, query, upload, URL, webhook, external HTTP call, session, cookie, sensitive data, configuration, or deployment-facing behavior.

It provides a bounded engineering review. It does not certify compliance or replace a penetration test, legal assessment, or incident response process.

## Procedure

1. Define the protected asset, trust boundaries, actors, entry points, and intended authorization rule before looking for generic issues.
2. Read the changed code and the closest route, policy, request, model, view, test, configuration, and integration context needed to verify the path end to end.
3. Load [references/checklist.md](references/checklist.md) and apply only sections relevant to the change.
4. Verify the observable enforcement point: a policy or gate for object access, a Form Request or equivalent for input, a constrained query for data access, and a safe output or redirect boundary for browser behavior.
5. Prefer a targeted proof: an authorization test, invalid-input test, malicious URL or upload case, fake external client, or inspection of the exact configuration path. Do not claim an unrun check passed.
6. Report demonstrated defects separately from unverified risks. Each finding includes the location, attack precondition, evidence, consequence, and minimum acceptable outcome.

## Invariants

- Authentication does not establish authorization. Check ownership, tenant boundaries, abilities, and policy coverage independently.
- Never log, return, commit, or place a secret in an exception message, test fixture, client bundle, URL, or configuration outside the approved secret mechanism.
- Do not make a real external request in a test unless the project explicitly requires an integration test and its isolated credentials.
- A security improvement must preserve the intended product behavior; do not silently block a legitimate actor or workflow.
