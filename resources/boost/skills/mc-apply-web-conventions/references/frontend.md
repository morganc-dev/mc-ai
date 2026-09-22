# Blade, Tailwind, and CSS Conventions

## Default Stack

- Blade and Tailwind CSS v4 are the `mc-harness` defaults for a new project area. An established stack always wins.
- No additional frontend framework or preprocessor without an explicit project decision.

## Styling Strategy

- Follow an established project strategy when it is clear and consistent.
- When the project has no established strategy, semantic BEM classes in Blade composed with Tailwind utilities through `@apply` inside `@layer components` are the `mc-harness` default.
- BEM classes use `block`, `block__element`, and `block--modifier`. Name them after the component's role, not its current visual appearance.
- Keep Blade focused on structure and meaning. In the default BEM mode, do not place presentation utilities, arbitrary values, colors, or dimensions in markup.
- Direct Tailwind utilities in Blade are a valid alternative only when the project already follows that strategy or the user explicitly chooses it.
- Do not mix BEM plus `@apply` and direct presentation utilities within the same component. Structural hooks required by JavaScript or accessibility do not define a styling strategy.

## Theme and Scale

- `app.css` is the single entry point: the theme, imports, and components must all be traceable from it.
- Shared design values live in `@theme` as semantic tokens: colors, radii, spacing, shadows, typography, and motion.
- Use Tailwind's spacing, sizing, typography, radius, and breakpoint scales before extending them.
- Extend `@theme` only when a repeated semantic value genuinely belongs to the project's design system. Do not create a token merely to preserve one measurement from a mockup.
- Never put raw hexadecimal, `rgb()`, `rgba()`, or other literal colors in markup.
- Avoid arbitrary values for ordinary layout and typography. If an irreducible technical effect needs a precise value, keep it in the component CSS; promote it to `@theme` only when it is reusable and semantic.

## Reference Designs

- Treat a mockup as a source of visual hierarchy, proportions, rhythm, composition, and intent, not as a pixel-perfect specification.
- Translate the reference to the closest coherent Tailwind scale values. Small differences from the mockup are preferable to a page filled with one-off measurements.
- Preserve the design's recognizable relationships across responsive states rather than copying fixed coordinates or dimensions.
- Do not mechanically convert every CSS declaration or measured pixel value from a mockup into an arbitrary Tailwind utility.

## Component CSS

- Define BEM component classes in `@layer components` and compose them primarily with `@apply` from Tailwind utilities and theme tokens.
- Use native CSS declarations only for behavior that Tailwind utilities or `@apply` cannot express cleanly, such as complex masks, `clip-path`, pseudo-elements, or specialized animations.
- Keep native declarations adjacent to the component they support and express their colors and reusable values through theme tokens or CSS variables.
- Import every separate CSS file from `app.css`.
- A component whose width depends on its insertion context uses container queries, not viewport breakpoints.

## Blade

- Name view and component files and folders in kebab-case.
- Reuse an existing component before adding duplicated markup.
- Keep business logic out of views.
