# Test Conventions

- Use Pest without `describe` blocks only when the project has no established alternative. This is an `mc-ai` default.
- Use feature tests for user-facing behavior and unit tests for genuinely isolated business logic.
- Test logic and observable contracts, not guarantees already provided by the framework.
- Never assert exact UI labels or copy when wording is not the contract.
- Name tests after observable behavior, not implementation.
- Use existing factories and states before constructing data manually.
- Run targeted tests during development and broaden the suite in proportion to the change before delivery.
