---
name: modernpress-figma-sync
description: Syncs the codebase (CSS variables, theme.json, and PostCSS files) to match Figma design tokens and styles. Use whenever a user asks about syncing, updating, or pulling from a Figma design.
---

# ModernPress Figma Sync
Detailed impementation steps for syncing the codebase to match the Figma design for a ModernPress project. Follow all steps and critical rules carefully to ensure a successful sync. If you have any questions about the steps or rules, ask for clarification before proceeding.

## Required Figma Frames
Ask the user for links to Figma frames listed by name below. Do not continue until you have all of these links.

1. Foundations - The foundations frame in Figma contains the base variables which should be mapped to the root design system variables in this project.
2. Typography - Contains two selections with key typography information. Use to determine the font families used in this project, base font scales, and how to apply typography to core WordPress blocks.
3. Core Blocks Styling - WordPress core block styles
4. Styleguide Example - Examples of how to apply the design system styles to various components and blocks. This is key for comparing to a demo site and ensuring the styles are applied correctly.
5. Buttons and Links - Contains button variations, inline link styles, and accessibility styles for each color mode
6. Color Themes - Defines color themes available for the project along with core blocks that should have color adjusted for each color mode.
7. Core Block Settings - Settings for the universal color picker, image block setings, and other core block admin settings defined in Figma.

Once you have all of the links, proceed to the implementation steps below. Follow all critical rules during implementation.

## Implementation Steps
Follow the implementation steps below in order. Each step should be completed fully before moving on to the next step. After each step, summarize the updates that were made in a message to the user before proceeding to the next step. This will ensure the user is informed and has context for each phase of the implementation.

### Step 1: 
Use a senior-frontend-engineer agent to implement the root design system elements in the codebase. This includes mapping the CSS custom properties and theme.json values to match the styles defined in the Figma frames.

Figma frames containing these variables: foundations, core block settings; typography; buttons and links; color themes.

### Step 2: Style core blocks
Use a senior-frontend-engineer agent to identify core block styles in the Figma design and update the codebase. All core blocks in the Figma design must be styled in the codebase.

Figma frames containing these variables: core block styling.

### Step 3: Review and QA
Use a frontend-qa-analyst agent to compare the Figma design to a demo site with the new styles applied. The QA must check **every color theme** (not just the default) and must verify hover/focus states in each theme. Identify any discrepancies or issues and report them back for resolution.

### Step 4: Iterate on any issues found during QA and finalize the implementation. Ensure all styles are correctly applied and the codebase is clean with no unused variables or styles.
Use a senior-frontend-engineer agent to resolve any issues found during QA and finalize the implementation. This may involve removing unused variables, fixing discrepancies, and ensuring the codebase is clean and maintainable.

## Critical Rules

### MCP Tool Usage
Never use the get_design_context tool on frames with the names listed below or any of their children.
- Foundations

### Removal rule
**If a CSS custom property or theme.json value exists in the codebase but has NO corresponding token or style in the Figma design, it must be removed.** Do not keep, rename, or update it with a placeholder value just because it exists. Existence in the codebase is not a reason to preserve it. Grep for all references to the removed token across custom selectors, variable files, block PostCSS files, and JSON variants. Handle each reference.

### Completeness rule
**Every style defined in the Figma design must be accounted for in the codebase.** Do not skip a style because it seems minor or redundant. Examples of styles that must be captured:
- List item marker styles - these may be separate tokens in Figma but should be implemented as part of the list block styles in the codebase
- Focus ring / outline styles
- Letter spacing, line height, font variant settings
- Border radii on individual corners
- Box shadows
- Transition / animation tokens
- Any layer-level effect

### Color Picker values
Only colors in the "Universal Color Picker" section should be added to the theme.json color palette. Other colors should be added as CSS custom properties.

### No raw hex values outside variable definitions
**Never use a hex color value in PostCSS rules, theme.json styles, or block style variant JSON files if a CSS custom property already exists for that color.** Hex values belong only in variable definition files (`color/_variables.pcss`, `actions/_variables.pcss`, etc.) where custom properties are declared. Everywhere else — including `styles/blocks/*.json`, `theme.json` styles, and `.pcss` rule declarations — must reference the custom property (e.g. `var(--btn-blue)` not `#366dd9`). After writing any color value, verify it doesn't duplicate an existing custom property definition.

### Font Files
Only add the font family names and placeholder paths from Figma to the codebase. Do not add actual font files or attempt to find them online. Font files should be added manually by the user after the sync process.

### Figma Design Context
Always get Figma design context as plain HTML + CSS.

### No inline CSS in theme.json
**Never use the `css` property in theme.json block styles.** All block CSS must go in PostCSS partials (`blocks/core/{block}/style.pcss`). Look first for an existing partial that matches the block being styled. If one does not exist, create a new partial following the same conventions as existing ones.

### WordPress cascade specificity
In WordPress theme.json, `styles.blocks.{block}.typography` has **higher specificity** than `styles.elements.{h1, h2, etc.}`. Never set a default property (like fontWeight) at the block level if it should vary per element — it will override all element-level values.

### Theme-aware hover states
When implementing hover/focus states, every visual color property (text, background, border, pseudo-element backgrounds) must be **explicitly set**. A color that works on the default theme may be invisible on another. For example, a white arrow on a white hover background in dark theme. Verify each interactive state against every color theme defined in the design.

### Build steps
Don't run frontend build steps or generate compiled CSS. Just update the source files (e.g. theme.json, CSS variable definitions, PostCSS files) to match the Figma design.

## Fluid Clamp Formula
When converting min/max pixel values from a design system to fluid `clamp()` expressions, use a **320px → 1920px viewport range**:

```
Given: min (px), max (px)

slope = (max - min) / (1920 - 320) × 100 → vw value
intercept = min - (slope / 100 × 320) → px, convert to rem (÷ 16)
min_rem = min ÷ 16
max_rem = max ÷ 16

Result: clamp({min_rem}rem, {intercept_rem}rem + {slope}vw, {max_rem}rem)
```

**Example**: min 16px, max 24px

```
slope = (24 - 16) / 1600 × 100 = 0.5vw
intercept = 16 - (0.005 × 320) = 14.4px = 0.9rem

→ clamp(1rem, 0.9rem + 0.5vw, 1.5rem)
```

This formula applies to font sizes, spacing values, and any other fluid property.
