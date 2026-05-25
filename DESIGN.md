---
name: Emitfy
description: Professional invoicing for Brazilian freelancers and small businesses
colors:
  amber-operator: "oklch(0.62 0.12 80)"
  amber-operator-muted: "oklch(0.74 0.08 80)"
  slate-white: "oklch(0.97 0.01 265)"
  card-surface: "oklch(0.985 0.008 265)"
  muted-surface: "oklch(0.955 0.01 265)"
  slate-ink: "oklch(0.32 0.02 265)"
  muted-ink: "oklch(0.48 0.02 265)"
  border-subtle: "oklch(0.88 0.01 265)"
  dark-canvas: "oklch(0.14 0.02 265)"
  dark-card: "oklch(0.22 0.025 265)"
  success: "oklch(0.58 0.17 145)"
  warning: "oklch(0.75 0.16 85)"
  destructive: "oklch(0.62 0.22 29)"
  info: "oklch(0.6 0.15 240)"
typography:
  display:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2rem, 5vw, 3.5rem)"
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(1.25rem, 3vw, 1.75rem)"
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: "-0.01em"
  title:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: "0.01em"
rounded:
  sm: "4px"
  md: "6px"
  lg: "8px"
  xl: "12px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.amber-operator}"
    textColor: "oklch(0.98 0 0)"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-primary-hover:
    backgroundColor: "oklch(0.55 0.12 80)"
  button-outline:
    backgroundColor: "{colors.slate-white}"
    textColor: "{colors.slate-ink}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.slate-ink}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-destructive:
    backgroundColor: "{colors.destructive}"
    textColor: "oklch(0.98 0 0)"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  card:
    backgroundColor: "{colors.card-surface}"
    textColor: "{colors.slate-ink}"
    rounded: "{rounded.xl}"
    padding: "24px"
  input:
    backgroundColor: "transparent"
    textColor: "{colors.slate-ink}"
    rounded: "{rounded.md}"
    padding: "4px 12px"
    height: "36px"
---

# Design System: Emitfy

## 1. Overview

**Creative North Star: "The Amber Ledger"**

Emitfy's visual system is built around one central tension: the warmth of a trusted professional relationship against the precision of a financial tool. The amber accent, cool indigo-tinted neutrals, and Inter's geometric clarity combine into something that feels like a well-worn accountant's journal: functional, deliberate, warm enough to trust, sharp enough to respect.

This is not a dashboard trying to look futuristic. It is not a productivity tool trying to look delightful. It is a tool for people who need to get paid, and every design decision is made in service of that clarity. Density is calibrated: enough information density to feel capable, not so much that it overwhelms a freelancer checking in on their phone after a client call.

The system explicitly rejects: the generic SaaS-cream aesthetic (white cards, teal accent, rounded-everything), the ultra-minimal gray-on-white coldness of productivity-tool clones, and the dense enterprise chrome of traditional accounting software. This tool lives in the space between those failure modes: professional without being cold, warm without being soft, capable without being overwhelming.

**Key Characteristics:**
- Single amber accent used sparingly; rarity is the point
- Indigo-tinted neutrals (not pure gray) carry the blue of invoices and ledgers without literalism
- Full dark/light parity; neither mode is an afterthought
- Semantic color used for status only: never decorative, always informative
- Tonal layering as the primary depth signal; shadows reserved for state

## 2. Colors: The Amber Ledger Palette

One accent. Many neutrals. Four semantic roles. The amber appears on ≤10% of any given screen; its rarity is what makes it decisive.

### Primary
- **Amber Operator** (oklch(0.62 0.12 80)): The single action color. Used on primary buttons, active nav states, focus rings, checked form controls, and the app logo mark. When amber appears, it means "do this." Shifts to oklch(0.74 0.08 80) in dark mode for contrast parity.

### Neutral
- **Slate White** (oklch(0.97 0.01 265)): Page background in light mode. The indigo tint (hue 265) is subtle but keeps the surface from reading as clinical white.
- **Card Surface** (oklch(0.985 0.008 265)): Cards and popovers sit 0.015 lightness above the page background. Tonal separation without border dependence.
- **Muted Surface** (oklch(0.955 0.01 265)): Secondary surfaces: sidebar, muted sections, secondary buttons. Slightly darker than the page.
- **Slate Ink** (oklch(0.32 0.02 265)): Primary text. Dark enough for WCAG AA on all background tokens.
- **Muted Ink** (oklch(0.48 0.02 265)): Secondary text, descriptions, placeholder text, timestamps.
- **Border Subtle** (oklch(0.88 0.01 265)): Dividers and card borders. Quiet, never structural.
- **Dark Canvas** (oklch(0.14 0.02 265)): Dark-mode page background. Deep blue-gray, not black.
- **Dark Card** (oklch(0.22 0.025 265)): Dark-mode card surfaces. Elevated by tone above the canvas.

### Semantic
- **Invoice Green** (oklch(0.58 0.17 145)): Paid status, success states, positive amounts.
- **Caution Amber** (oklch(0.75 0.16 85)): Due-soon warnings, pending states. Distinct from Amber Operator in both lightness and chroma.
- **Alert Red** (oklch(0.62 0.22 29)): Overdue invoices, destructive actions, error states.
- **Reference Blue** (oklch(0.6 0.15 240)): Informational states, help text, info toasts.

### Named Rules
**The One Voice Rule.** Amber Operator appears on ≤10% of any given screen. It is used exclusively for primary actions and active states. Secondary or decorative use dilutes it.

**The No Pure Gray Rule.** Every neutral carries hue 265 (indigo) at chroma 0.005–0.02. Pure gray (`oklch(X 0 0)`) is prohibited anywhere in the UI.

**The Status Semantics Rule.** Green, amber-warning, red, and blue are reserved for invoice and transaction status. They do not appear as decorative accents, heading colors, or illustration fills.

## 3. Typography

**Font:** Inter (Google Fonts, variable weight 100–900, all weights)
**Fallback:** ui-sans-serif, system-ui, sans-serif

**Character:** A single humanist sans-serif system. Inter at high weights reads authoritative; at low weights it disappears into body text without friction. No display/body split: one family handles all roles through weight and size contrast alone.

### Hierarchy
- **Display** (700, clamp(2rem, 5vw, 3.5rem), line-height 1.1, tracking -0.02em): Hero headlines and marketing headings on the landing page. Almost never used in the authenticated app.
- **Headline** (600, clamp(1.25rem, 3vw, 1.75rem), line-height 1.25, tracking -0.01em): Section headings, page titles, dialog headers.
- **Title** (600, 1rem, line-height 1.4): Card titles, list group headers, sidebar section labels.
- **Body** (400, 0.875rem, line-height 1.6): All running text, table rows, form field values. Max line length 65ch.
- **Label** (500, 0.75rem, line-height 1.4, tracking 0.01em): Metadata, timestamps, badge text, input labels, stat card subtitles.

### Named Rules
**The Weight Contrast Rule.** Every level must be at least 100 weight-units apart from adjacent levels. Title (600) next to Body (400) is correct; Title (600) next to Headline (600) at the same size is not.

**The One Size Jump Rule.** Adjacent hierarchy steps must differ by at least 1.25x in computed size. A headline at 1.5rem next to a title at 1rem satisfies this; 1.25rem next to 1rem does not.

## 4. Elevation

Emitfy uses tonal layering as the primary depth signal. Cards are distinguished from the page background by a 0.015 lightness step in light mode and a 0.08 lightness step in dark mode — no shadow required. Shadows enter only when an element is in an elevated state: hover, focus, modal overlay, or active drag.

### Shadow Vocabulary
- **Rest** (`shadow-xs`, `0 1px 2px rgba(0,0,0,0.05)`): Inputs and checkboxes at rest. Barely perceptible; signals "this is interactive" without visual noise.
- **Card hover** (`shadow-md` with amber tint at 5% opacity): Cards on hover. The amber tint connects hover state to the Amber Operator without competing with it.
- **Lifted** (`shadow-lg`, `0 10px 15px rgba(0,0,0,0.1)`): Dialogs, sheets, command palettes. Clear separation from the page surface.
- **Hero** (`0 0 50px -12px rgba(0,0,0,0.3)`): Screenshot and hero imagery on the landing page. Cinematic, not functional.

### Named Rules
**The Flat-at-Rest Rule.** Cards, list items, stat blocks, and table rows carry no shadow at rest. Tonal contrast is enough. Shadows are responses to state, not permanent decoration.

**The Tinted Shadow Rule.** When using shadows on interactive elements, tint toward `amber-operator` at 5–20% opacity. This connects hover feedback to the primary action color without announcing itself.

## 5. Components

### Buttons
Warm and confident. Gently curved corners (6px) signal approachability without softness. Size-calibrated: the default (h-9, 36px) fits table rows and form contexts; lg (h-10, 40px) for primary CTAs.

- **Shape:** 6px radius (rounded-md) on all variants
- **Primary:** Amber Operator background, near-white text, padding 8px 16px. Hover: darkens to oklch(0.55 0.12 80).
- **Outline:** Background surface, Slate Ink text, 1px Border Subtle stroke. Hover: Muted Surface background.
- **Ghost:** No background, no border. Hover: Muted Surface tint. Used inside dense layouts (table actions, sidebar).
- **Destructive:** Alert Red background, white text. Focus ring in destructive/20.
- **Focus:** 3px ring in ring/50 (amber at 50% opacity). Visible, not garish.
- **Disabled:** 50% opacity, pointer-events none.

### Cards
- **Corner Style:** Gently rounded (12px, rounded-xl). Larger than buttons to signal containment.
- **Background:** Card Surface (oklch(0.985 0.008 265)) in light; Dark Card (oklch(0.22 0.025 265)) in dark.
- **Shadow Strategy:** None at rest. shadow-md with amber tint at 5% on hover.
- **Border:** 1px Border Subtle. Present, quiet.
- **Internal Padding:** 24px on all sides (py-6 px-6). Inner sections separated by gap-6.

### Inputs / Fields
- **Style:** Transparent background, 1px Border Subtle stroke, 6px radius, 36px height. Dark mode: input/30 background.
- **Focus:** 3px ring in ring/50, border shifts to Amber Operator. Immediate and precise.
- **Error:** ring-destructive/20, border-destructive. Always paired with error text; never color alone.
- **Disabled:** 50% opacity, not-allowed cursor.

### Navigation (Sidebar)
- **Background:** Muted Surface (oklch(0.955 0.01 265)), one tonal step darker than the page.
- **Default items:** Muted Ink text, no background.
- **Hover:** Accent surface tint (oklch(0.94 0.015 265)).
- **Active:** Amber Operator background, near-white text, 6px radius. The logo mark shares this treatment: the only two places where amber fills a non-button surface.
- **Typography:** Body (400) at rest, medium (500) on active.

### Invoice Status Badges (Signature Component)
The most semantically loaded component in the system. Each status maps to a color role paired with a text label so color-blind users are never excluded.

- **Draft:** Muted Surface background, Muted Ink text.
- **Sent:** Reference Blue at 15% opacity background, blue foreground.
- **Paid:** Invoice Green at 15% opacity background, green foreground.
- **Due Soon:** Caution Amber at 15% opacity background, amber foreground.
- **Overdue:** Alert Red at 15% opacity background, red foreground.
- **Shape:** Pill (rounded-full), px-2 py-0.5 padding, label typography (0.75rem, weight 500).

## 6. Do's and Don'ts

### Do:
- **Do** use Amber Operator exclusively for primary actions, active states, and focus rings. One color, one meaning.
- **Do** tint every neutral surface toward hue 265. Even near-white backgrounds carry chroma 0.005–0.01.
- **Do** use tonal separation as the first depth tool. Reach for shadows only when state changes demand it.
- **Do** pair every status color with a text label. Color alone never communicates invoice state.
- **Do** keep body text under 65ch line length. Invoice amounts and client names are scannable; prose is not the product.
- **Do** keep Inter weight at 600+ for titles and 400 for body. Adjacent hierarchy levels must differ by at least 100 weight-units and 1.25x size.
- **Do** reduce chroma as lightness approaches 0 or 100. High-chroma extremes look garish; pull chroma down as you pull lightness toward the edges.

### Don't:
- **Don't** use teal or blue-teal as an accent. This is the first SaaS reflex for invoice tools and the first thing this system is built to avoid.
- **Don't** build identical card grids: same-sized cards with icon, heading, and paragraph text in a repeating column layout. If content repeats, use a table or list.
- **Don't** use `border-left` greater than 1px as a colored stripe on cards, list items, or alerts. Rewrite with background tints, full borders, or nothing.
- **Don't** use gradient text (`background-clip: text`). Use weight, size, or solid color contrast instead.
- **Don't** use glassmorphism decoratively. Blurs and transparency have no place in this system.
- **Don't** build the hero-metric template: big number, small label, gradient accent, supporting stats. This is the SaaS dashboard cliche the system explicitly rejects.
- **Don't** use pure gray (`oklch(X 0 0)`) anywhere. Every neutral must carry the indigo tint (hue 265).
- **Don't** use Caution Amber as a decorative accent or positive highlight. It signals due-soon and pending states only.
- **Don't** design toward QuickBooks (dense enterprise chrome), Notion/Linear clones (cold gray-on-white), or generic SaaS-cream (white cards, rounded-everything, any teal accent).
