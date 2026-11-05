# Tailwind and CSS Conventions

These rules target Tailwind CSS v4. An established project strategy always wins.

## Styling Strategies

Three strategies are admitted. Follow the one the project already uses; a new area adopts the strategy of its neighbors.

- **Utilities in templates**: Tailwind utilities written directly in Vue or Blade templates. This is the `mc-ai` default for a Vue project without an established strategy.
- **BEM with `@apply`**: semantic `block`, `block__element`, and `block--modifier` classes named after the component's role, defined in `@layer components` and composed with `@apply`. Markup carries no presentation utility, arbitrary value, color, or dimension. This is the `mc-ai` default for a Blade project without an established strategy.
- **Library components**: components from a library such as shadcn-vue keep their own styling and variant API. Extend them through that API and the theme tokens; do not rewrite their classes or reimplement them.

Do not mix strategies within one component. Structural hooks required by JavaScript or accessibility do not define a styling strategy. Never impose BEM on Vue components, and never forbid utilities in templates where the project uses them.

## Theme and Scale

- The CSS entry point, `app.css` or the project's equivalent, is unique: the theme, imports, and components must all be traceable from it.
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

- In the BEM strategy, define component classes in `@layer components` and compose them primarily with `@apply` from Tailwind utilities and theme tokens.
- Use native CSS declarations only for behavior that Tailwind utilities or `@apply` cannot express cleanly, such as complex masks, `clip-path`, pseudo-elements, or specialized animations.
- Keep native declarations adjacent to the component they support and express their colors and reusable values through theme tokens or CSS variables.
- Import every separate CSS file from the CSS entry point.
- A component whose width depends on its insertion context uses container queries, not viewport breakpoints.
