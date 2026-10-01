# Laravel Security Review Checklist

Load only sections relevant to the reviewed change.

## Authentication and Authorization

- Verify the route middleware and the actual policy, gate, ability, or ownership check. Middleware alone does not prove object-level authorization.
- Check every model lookup and nested relation for cross-user or cross-tenant access.
- Confirm privileged actions cannot be reached by changing an identifier, route parameter, request field, queue payload, or client-side state.
- Ensure a failed authorization returns the intended response without disclosing whether a protected resource exists.

## Input, Query, and Output Boundaries

- Use a Form Request or equivalent validation at HTTP boundaries. Validate types, formats, ranges, enumerations, array keys, and relationships that carry security meaning.
- Treat redirects, URLs, headers, rendered HTML, and client-side JSON as output boundaries. Do not reflect untrusted input into executable or navigational contexts.
- Prefer Eloquent, the query builder, bindings, and allow-listed column or sort names. Dynamic identifiers are not protected by value binding.
- Check mass assignment, casts, hidden fields, serialization, API resources, and eager-loaded relations for unintended data exposure.

## Browser and Session Boundaries

- Preserve Laravel's CSRF protection for state-changing browser requests unless a documented alternative contract applies.
- Review cookie attributes, session invalidation after sensitive changes, and re-authentication requirements for high-impact actions.
- Ensure client-visible state does not become the authorization source of truth.
- Consider rate limiting, idempotency, replay prevention, and abuse cost for public or credentialed endpoints.

## Files, URLs, Webhooks, and External HTTP

- For uploads, validate MIME type and size, generate server-side names, keep untrusted files outside executable paths, and verify download authorization.
- For URLs fetched by the server, constrain scheme, host, redirects, IP ranges, and response size to avoid SSRF and resource exhaustion.
- Verify webhook signatures before parsing or acting on payloads; use constant-time comparison where the framework does not already do so.
- Define timeouts, retries, error handling, and request fakes. Do not include credentials in URLs or logs.

## Secrets, Configuration, and Observability

- Read environment values only through configuration. Do not commit populated environment files, private keys, tokens, or credentials.
- Do not place secrets, access tokens, personal data, or full request bodies in logs, exceptions, analytics, or test snapshots.
- Check production-safe error handling and response messages. Detailed diagnostics belong in controlled logs, suitably redacted.
- Review cache keys, queue payloads, broadcasts, notifications, and generated URLs for cross-user data leakage.
