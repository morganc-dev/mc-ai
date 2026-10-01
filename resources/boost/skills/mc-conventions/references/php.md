# PHP and Laravel Conventions

## Naming

- Use English for every code identifier: classes, interfaces, traits, enums, methods, functions, properties, variables, constants, routes, events, jobs, configuration keys, tables, columns, foreign keys, and pivot tables.
- Follow Laravel's English naming conventions so framework defaults apply without manual overrides. Examples: `getActiveReservations()`, `isInvoicePaid()`, `ReservationFactory`, `StoreOrderRequest`.
- Prefix private methods with `_` only when the project already follows this convention or when creating a new isolated area. This is an `mc-ai` default, not a Laravel convention; never retrofit it into an established codebase.

## Structure

- Prefer early returns and avoid `else`.
- Never nest ternaries; use `match`, `if`/`elseif`, or early returns.
- Use a Form Request for HTTP input validation.
- Use an invokable controller when it has a single responsibility.
- Add explicit parameter and return types to every method.
- Never write SQL directly in a controller.

## Restraint

- Implement only what is needed and prefer idiomatic code.
- Do not add a fallback, polyfill, or defensive branch for an undemonstrated case; verify actual support.
- Do not create an abstraction that adds more complexity than clarity.

## Comments and Formatting

- Write comments in the working language, sparingly, and only to explain why: intent, constraint, or trap.
- Never paraphrase a method name, signature, or obvious file content.
- Use Pint as the sole source of truth for PSR-12 formatting.
