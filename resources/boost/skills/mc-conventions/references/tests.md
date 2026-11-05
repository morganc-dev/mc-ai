# Test Conventions

- For PHP tests, use Pest without `describe` blocks only when the project has no established alternative. This is an `mc-ai` default.
- Use feature tests for user-facing behavior and unit tests for genuinely isolated business logic.
- Test logic and observable contracts, not guarantees already provided by the framework.
- Never assert exact UI labels or copy when wording is not the contract.
- Name tests after observable behavior, not implementation.
- Use existing factories and states before constructing data manually.
- Run targeted tests during development and broaden the suite in proportion to the change before delivery.

## Frontend

- Use the tools the project already installs, such as Vitest, Vue Test Utils, or Playwright. None of them is a required dependency.
- Test observable behavior: rendered output, emitted events, navigation, and user interactions, not internal state or implementation details.
- Validate interface changes visually and functionally in the browser when the available tools allow it, and state what was not run.
