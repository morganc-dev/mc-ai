# Accessibility by Design

- Text contrast: 4.5:1, or 3:1 for large text; interface components and focus indicators: 3:1 against adjacent colors.
- Color is never the only carrier of information.
- Body text is at least `1rem`, line height is close to `1.5`, line length is 45 to 75 characters, and text is not justified.
- Use a coherent heading hierarchy, landmarks, and a reading order matching the visual order.
- Interactive targets are about 44 × 44 px; no interaction is available only on hover.
- Every interactive element has a visible focus style distinct from hover.
- Use visible labels, identify required fields, place errors near their field, and state the corrective action.
- Provide a `prefers-reduced-motion` alternative; avoid dangerous flashing and media that cannot be stopped.
- Define alt-text intent for informative images; mark decorative images accordingly; provide transcripts or captions for relevant media.
- Use direct language, explain jargon, and write link labels that make sense out of context.

Fix the source token or component whenever possible instead of patching each occurrence.
