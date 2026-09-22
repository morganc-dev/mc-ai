# Maintainability Heuristics

A code smell is a design signal, not proof of a defect. It blocks a delivery only when it violates an explicit criterion or causes a demonstrated correctness, security, performance, or maintenance consequence.

- **Mysterious name**: a name conceals the actual role of a value or responsibility.
- **Duplicated knowledge**: the same rule appears in several places that may diverge. Extract only knowledge that genuinely shares a reason to change.
- **Feature envy**: a function depends more on another object's data than its own.
- **Primitive obsession**: a primitive represents a concept with invariants that would materially benefit from a dedicated type.
- **Repeated conditionals**: the same business decision is copied across several branches.
- **Shotgun surgery**: one conceptual change requires unnecessarily scattered edits.
- **Divergent responsibilities**: one module changes for several unrelated reasons.
- **Speculative generality**: an abstraction, option, or extension point has no demonstrated use.
- **Message chain**: a caller traverses the internal details of several objects.
- **Empty middleman**: a layer delegates without adding a useful boundary, rule, or policy.

Do not manually report what a formatter, linter, or static analyzer already describes precisely. Generated code, migrations, schemas, compatibility adapters, fluent APIs, and some test repetition may legitimately resemble these signals: judge the consequence, not the shape.
