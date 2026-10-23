---
name: figma-design-sync
description: >
  Sync design tokens and component properties from a Figma file to a Moose
  WordPress theme codebase. Extracts colors, typography, spacing, layout,
  buttons, and border radius with full variable mode support (White, Light,
  Dark, Brand themes). Use when the user asks to sync, update, compare, or
  extract a design system from Figma, or mentions design tokens, Figma
  variables, or theme token sync.
---

# Figma Design System Sync

Sync design tokens from a Figma design system to a Moose WordPress theme.

## Before Starting

**Read the full sync guide before executing any sync operation:**
[design-system-sync.md](design-system-sync.md)

The guide contains complete variable-to-code mapping tables, step-by-step
extraction procedures, transformation lookup tables, and button token handling
instructions that are essential for accurate execution. Do not attempt a sync
without reading it first.

## Baseline

The baseline JSON (last-known state of all Figma variables with mode support)
lives at: `~/.moose-knowledge/figma-to-code/figma-baseline.json`

Update this file after every successful sync.

## Critical Rules

1. **Read-only Figma access** — never modify Figma files
2. **Use BOTH extraction tools** — `get_variable_defs` for design tokens AND
   `get_design_context` for component properties (border-radius, icons,
   padding). Tokens-only extraction misses component-level changes.
3. **Human approval required** — present change report before any code
   modifications
4. **Use transformation tables** — do NOT copy Figma px values directly for
   font sizes or spacing
5. **Skip aliases** — ignore all `secondary/*` variables (aliases to
   `neutral/*`)
6. **Skip orphaned variables** — ignore `font-size/30`, `type/step8`,
   `letter-spacing/s` (unclear purpose)
7. **Skip alert light/dark variants** — code only has `/default` values
8. **Skip box shadows and border-radius s/m** — not yet in Figma
9. **White = Light for colors** — identical values, both map to
   `:root, :--light-themes`
10. **Border radius full is always 9999px** — ignore Figma's 1920px value

## Workflow (7 Steps)

1. **Discovery**: Extract `fileKey` from Figma URL, verify file structure with
   `get_metadata`
2. **Extraction**: Use `get_variable_defs` on relevant frames for design
   tokens, then `get_design_context` on button component instances for
   component-level properties (border-radius, icons, padding, gap)
3. **Comparison**: Compare against baseline JSON (tokens) and current PCSS
   (component properties) to identify NEW / CHANGED / REMOVED / UNCHANGED
4. **Report**: Present structured change report covering both token and
   component property changes. **Wait for user approval.**
5. **Code Updates**: Update `theme.json` first (Layer 1), then PCSS variable
   files (Layer 2), then search codebase for hard-coded hex values
6. **Verification**: Run lint, build, and visual checks
7. **Baseline Update**: Write new values to `figma-baseline.json`

## Figma MCP Tools

| Tool | Returns | Use For |
|------|---------|---------|
| `get_variable_defs` | Design token variables | Color hex values, spacing numbers, font sizes, any Figma Variable value |
| `get_design_context` | Full component structure as code | Border-radius, icons, padding, gap, layout — properties NOT stored as Figma Variables |
| `get_metadata` | Node tree structure | Discovering file structure and finding node IDs |
| `get_screenshot` | Visual screenshot | Visual confirmation of component appearance |

**Why both tools**: `get_variable_defs` only returns Figma Variable values
(tokens in the Variables panel). Component-level styling like border-radius,
icon choices, layout direction, gap, or other properties set directly on
component instances require `get_design_context`, which returns the full
rendered component with all visual properties.

## Known Figma File Structure

| Frame | Node ID | Purpose |
|-------|---------|---------|
| Style Guide with Core Blocks | `7002:721` | Root — all variables in default mode |
| Color Theme (White) | `13276:16354` | White mode semantic colors |
| Color Theme (Light) | `13280:1219` | Light mode semantic colors |
| Color Theme (Dark) | `13280:1243` | Dark mode semantic colors |
| Color Theme (Brand) | `13280:1267` | Brand mode semantic colors |
| Buttons (White) | `7007:1847` | White mode button tokens with hover |
| Buttons (Dark) | `13280:14249` | Dark mode button tokens with hover |
| Buttons (Brand) | `13280:14304` | Brand mode button tokens with hover |
| Typography | `4966:8853` | Font stack and scale table |
| Layout | `4956:3347` | Grid system, block widths |
| Spacing Scale | `12356:1382` | Spacing scale table |

If node IDs have changed, use `get_metadata` on root (`0:1`) to discover new
structure. Look for frames named "Color Theme", "Buttons", "Typography", etc.

## Mode Handling

4 Figma modes map to CSS theme-tier selectors:

| Mode(s) | CSS Selector | Notes |
|---------|-------------|-------|
| White, Light | `:root, :--light-themes` | Functionally identical for colors |
| Dark | `:--dark-themes`, `:--dark-theme` | Distinct dark styling |
| Brand | `:--brand-theme` | Distinct brand styling |

Custom selectors defined in `custom-selectors/_variables.pcss`:

```css
@custom-selector :--light-themes .is-style-light, .is-style-white;
@custom-selector :--dark-themes .is-style-dark, .is-style-brand;
@custom-selector :--white-theme .is-style-white;
@custom-selector :--light-theme .is-style-light;
@custom-selector :--dark-theme .is-style-dark;
@custom-selector :--brand-theme .is-style-brand;
```

For variables with modes (semantic colors, button tokens):
- Extract values from multiple Figma node contexts (White, Dark, Brand)
- Organize by mode
- Map to CSS theme-tier selectors in `color/_variables.pcss` and
  `actions/_variables.pcss`
- Brand mode typically only overrides where values differ from Dark

### Missing Mode Data

- **Light mode missing**: Use White mode value (functionally identical)
- **Brand mode incomplete**: Inherit from Dark unless explicitly different
- **Any mode missing**: Preserve existing code value and flag in change report

## Transformation Rules

| Input | Output | Rule |
|-------|--------|------|
| `spacing/xx-small` → `spacing/xx-large` | `--spacer-10` → `--spacer-70` | Name→number lookup table in guide |
| `font-size/type-XX` (Min/Max px) | `clamp(...)` | Min/Max→clamp() lookup table in guide |
| `#ffffff00` | `transparent` | Hex alpha → transparent keyword |
| `#3050e6` | `var(--color-blue)` | Hex → var() if matches palette |
| `#000000` | `var(--color-black)` | Hex → var() if matches palette |
| `#ffffff` | `var(--color-white)` | Hex → var() if matches palette |
| `Bold` | `700` | Font style name → numeric weight |
| `Regular` | `400` | Font style name → numeric weight |
| `border-radius/radius-full` | `9999px` | Hard-coded exception |

The full transformation tables (spacing name→number, font size Min/Max→clamp,
color hex→variable, button token matrices) are in
[design-system-sync.md](design-system-sync.md) sections 6, 9, and 10.

## File Targets

### Layer 1: theme.json

`wp-content/themes/core/theme.json` — WordPress source of truth.

Update: color palette (slugs), font families, font sizes (clamp values),
font weights, spacing sizes (clamp values), layout widths, grid gutter/margin,
block-level typography styles.

### Layer 2: PCSS Variables

All paths relative to `wp-content/themes/core/assets/pcss/`:

| File | Contents |
|------|----------|
| `color/_variables.pcss` | Brand, neutrals, base, alert, semantic colors (with modes) |
| `actions/_variables.pcss` | Button tokens, link colors, focus rings (with modes) |
| `typography/_variables.pcss` | Font families, sizes, weights |
| `spacing/_variables.pcss` | Spacers, grid gutter/margin |
| `layout/_variables.pcss` | Grid widths |
| `global/_variables.pcss` | Border radius |

### Update Order

1. `theme.json` (Layer 1)
2. `color/_variables.pcss`
3. `actions/_variables.pcss`
4. `typography/_variables.pcss`
5. `spacing/_variables.pcss`
6. `layout/_variables.pcss`
7. `global/_variables.pcss`
8. Codebase search for hard-coded hex values that should reference variables

### What Goes Where

- **theme.json**: Anything WordPress needs (block editor UI, preset dropdowns,
  font size/spacing presets, layout widths, block-level typography)
- **CSS-only**: Purely front-end styling (theme tiers, button states, derived
  colors, alert colors, border radius, transitions, icons, opacity variants)

## Post-Sync Checklist

- [ ] `npm run lint:css` passes with no errors
- [ ] `npm run build` completes successfully
- [ ] Visual check: all button types across all theme tiers (White, Light,
      Dark, Brand)
- [ ] Visual check: spacing, typography sizes/weights, colors match Figma
- [ ] Git diff review: no unintended changes in `theme.json` or PCSS files
- [ ] Baseline JSON updated with new values
- [ ] Change report presented and approved by user
- [ ] Codebase search completed for hard-coded hex values

## Troubleshooting

### Node IDs have changed
Use `get_metadata` on root (`0:1`) to discover new structure. Look for frames
named "Color Theme", "Buttons", "Typography", "Layout". Update node ID
references.

### Extraction returns no variables
Try different node IDs — variables are file-scoped, not frame-scoped. Verify
the file key is correct (branch vs. main file).

### Mode values are missing
- Light mode buttons: use White mode values (functionally identical)
- Brand mode properties: use Dark mode values if not explicitly different
- Flag missing data in the change report

### Transformations are unclear
Refer to the mapping tables in [design-system-sync.md](design-system-sync.md).
Present unclear mappings to the user for decision.

## Success Criteria

- 90%+ of variables synced correctly
- No linting or build errors
- Visual check passes across all theme tiers
- User approved all changes before code modification
- Baseline JSON reflects current Figma state
- Change report clearly documents what changed and why
