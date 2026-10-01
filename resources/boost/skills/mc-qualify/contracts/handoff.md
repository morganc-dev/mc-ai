# Handoff Contract

Common format for every specialized role except the reviewer, who uses [review.md](review.md):

```text
Status: completed | blocked | decision_required
Result:
Files created or modified:
Evidence and validation:
Decisions and assumptions:
Deviations or residual risks:
Unverified items:
Next useful action:
```

Keep empty fields and set them to `none` or `not verified`. A successful command proves only what it actually checks. Never include secrets or sensitive raw logs in a handoff.
