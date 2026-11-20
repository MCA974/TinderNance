---
name: Altern Dynamic Match
colors:
  surface: '#faf8ff'
  surface-dim: '#d2d9f4'
  surface-bright: '#faf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f3ff'
  surface-container: '#eaedff'
  surface-container-high: '#e2e7ff'
  surface-container-highest: '#dae2fd'
  on-surface: '#131b2e'
  on-surface-variant: '#464554'
  inverse-surface: '#283044'
  inverse-on-surface: '#eef0ff'
  outline: '#767586'
  outline-variant: '#c7c4d7'
  surface-tint: '#494bd6'
  primary: '#4648d4'
  on-primary: '#ffffff'
  primary-container: '#6063ee'
  on-primary-container: '#fffbff'
  inverse-primary: '#c0c1ff'
  secondary: '#b90538'
  on-secondary: '#ffffff'
  secondary-container: '#dc2c4f'
  on-secondary-container: '#fffbff'
  tertiary: '#825100'
  on-tertiary: '#ffffff'
  tertiary-container: '#a36700'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e1e0ff'
  primary-fixed-dim: '#c0c1ff'
  on-primary-fixed: '#07006c'
  on-primary-fixed-variant: '#2f2ebe'
  secondary-fixed: '#ffdadb'
  secondary-fixed-dim: '#ffb2b7'
  on-secondary-fixed: '#40000d'
  on-secondary-fixed-variant: '#92002a'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#faf8ff'
  on-background: '#131b2e'
  surface-variant: '#dae2fd'
typography:
  display-hero:
    fontFamily: Plus Jakarta Sans
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 56px
    letterSpacing: -0.03em
  display-hero-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 30px
    letterSpacing: -0.015em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
    letterSpacing: -0.01em
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 10px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.04em
rounded:
  sm: 0.5rem
  DEFAULT: 1rem
  md: 1.5rem
  lg: 2rem
  xl: 3rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 1rem
  margin: 2rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system targets Gen Z students, career switchers, and forward-thinking corporate recruiters seeking apprenticeship (alternance) partnerships. The aesthetic bridges high-energy consumer matching mechanics with corporate credibility. The tone is optimistic, agile, and structured—transforming an often bureaucratic hiring process into an engaging, milestone-driven experience.

The design movement synthesizes **Glassmorphism** with **Modern Tactile UI**:
- Translucent floating layers with high backdrop-filter blurs mimic modern OS interfaces, lending depth to candidate dossiers and enterprise pitches.
- Subtle inner glows and hairline borders create a crisp, tactile presence without heavy physical skeumorphism.
- Micro-interactions, soft pill-shaped interactive anchors, and rich metric tags provide instant rhythm and clarity across matching stages, interview tracks, and apprenticeship schedules.

## Colors

The color palette establishes an energetic visual balance between authority and enthusiasm:
- **Primary (`#6366F1` / `#4F46E5`):** The signature electric indigo/violet. Represents digital fluency, progress, and trust. Used for primary calls-to-action, navigation active states, profile progress, and brand anchors.
- **Secondary (`#F43F5E`):** Vibrant coral/rose. Dedicated to high-engagement matching verbs, including the "SuperMatch" trigger, mutual interest pulses, urgent application calls, and high-affinity score badges.
- **Tertiary (`#F59E0B`):** Warm amber. Reserved for rhythm badges (e.g., CFA cadence ratios), pending validation statuses, contract perk highlights, and spotlight alerts.
- **Neutral Palette:** Anchored by Deep Slate (`#0F172A`) for high-contrast legible typography and structural framing, down to clean Canvas White (`#FFFFFF`) and Soft Slate (`#F8FAFC`) surfaces.
- **Semantic Accents:** Success emerald (`#10B981`) for completed matches and confirmed signatures; subtle violet tints (`#EEF2FF`) for interactive hover rings and glass backdrop tinting.

## Typography

The typography pairing unifies expressiveness and utility:
- **Display & Headlines (Plus Jakarta Sans):** Brings a contemporary, optimistic geometric voice with open counters and soft curves. Used across onboarding claims, company headlines, candidate names, and match percentages.
- **Body & Data (Inter):** Maximizes readability in dense operational contexts such as multi-paragraph apprenticeship mission breakdowns, CV timelines, and legal contract specs.
- **Labels & Micro-copy (Plus Jakarta Sans):** Applied in uppercase or semi-bold weights for badges, rhythmic pills (`3j Entr. / 2j CFA`), benefit chips, and matching action prompts.

## Layout & Spacing

The layout is built on a 12-column responsive fluid grid designed to toggle cleanly between rapid browsing decks and enterprise dashboard views:

- **Desktop (1280px+):** Max layout container capped at 1280px with 32px (`2rem`) side margins and 24px (`1.5rem`) gutters. The matching interface splits into a 5-column persistent context list and a 7-column interactive preview card deck.
- **Tablet (768px - 1024px):** 8-column layout. Candidate cards shift to full-width stacked views, with filters collapsible into bottom drawers.
- **Mobile (< 768px):** 4-column layout with 16px (`1rem`) side margins. Actions transition to anchored bottom pill bars within natural thumb reach.

Spacing within cards follows an 8pt modular rhythm: tightly grouped metadata (tags, salary, rhythm) uses `space-xs` and `space-sm`, while semantic sectioning relies on `space-md` through `space-xl` to prevent dense information clutter.

## Elevation & Depth

Visual hierarchy leverages stacked frosted glass surfaces and multi-layered, tinted drop shadows to create a buoyant, frictionless UI:

- **Layer 0 (Canvas):** `#F8FAFC` base with faint ambient radial gradients (`#6366F1` at 4% opacity and `#F43F5E` at 3% opacity) positioned in the upper corners to give backdrops depth.
- **Layer 1 (Standard Surface):** Pure White (`#FFFFFF`) with 1px solid border in `#E2E8F0` and subtle shadow: `0 4px 12px -2px rgba(15, 23, 42, 0.05)`.
- **Layer 2 (Floating Match Cards & Modals):** Frosted glass styling utilizing `rgba(255, 255, 255, 0.88)` with `backdrop-filter: blur(16px)`, surrounded by a 1px border of `rgba(255, 255, 255, 0.6)` and elevated by: `0 20px 30px -8px rgba(99, 102, 241, 0.12), 0 8px 12px -4px rgba(15, 23, 42, 0.04)`.
- **Layer 3 (Floating Controls & Popovers):** Highest depth tier. Pure white surfaces with directional drop shadows tinted with the primary hue: `0 24px 38px -6px rgba(15, 23, 42, 0.16), 0 10px 16px -4px rgba(99, 102, 241, 0.08)`.

## Shapes

The design system adopts a **Pill-shaped (Level 3)** roundedness strategy to reinforce friendliness, speed, and tactile ergonomics:
- **Buttons, Action Chips, and Micro-badges:** Full circular pill radius (`9999px` or `1rem` on base modules) to encourage touch and gesture interaction.
- **Content Cards & Glass Panels:** Rounded with `2rem` (`rounded-lg`) to soften structured data and frame candidate imagery seamlessly.
- **Modal Sheets & Overlays:** Crowned with `3rem` (`rounded-xl`) on top edges for native-like sheet appearances on touch displays.
- **Avatars & Logo Nodes:** Full concentric rounds (`rounded-full`) framed with 2px borders tinted to match applicant status.

## Components

### Buttons & Interactive Anchors
- **Primary Action (Match / Apply):** Pill-shaped, gradient-infused indigo (`linear-gradient(135deg, #6366F1, #4F46E5)`), white text, with an inset top highlight (`inset 0 1px 0 rgba(255,255,255,0.3)`) and subtle purple shadow.
- **Secondary Action (SuperMatch / Priority Like):** Pill-shaped, vibrant coral (`#F43F5E`), white text, accompanied by a dynamic flame or star icon. Scales up 2% on hover with an expanded coral halo shadow.
- **Ghost / Glass Buttons:** Transparent background with frosted glass overlay (`rgba(255, 255, 255, 0.6)`), 1px slate-200 border, dark slate text.

### Micro-Badges & Cadence Pills
- **Rhythm Badges (e.g., '3j Entr. / 2j CFA'):** High-contrast mini-pills featuring an amber-tinted surface (`#FEF3C7`), amber text (`#B45309`), and a crisp left-side mini calendar glyph.
- **Perk & Benefit Chips ('Télétravail', 'Tickets Resto', 'Mutuelle 100%'):** Light slate pills (`#F1F5F9`) with slate-700 text. Active or guaranteed perks shift to subtle emerald tints (`#ECFDF5`) with green typography (`#047857`).

### Floating Recruitment Cards
- Card headers host full-bleed cover imagery or company atmosphere reels, blending into card body through an interior gradient scrim.
- Bottom actions float over the card canvas with a frosted navigation capsule containing Decline, Bookmark, Message, and Match triggers.

### Form Inputs & Selectors
- Pill-curved form fields with subtle off-white backgrounds (`#F8FAFC`), transitioning to a crisp 1.5px `#6366F1` stroke and 4px ambient primary glow (`rgba(99, 102, 241, 0.15)`) on focus.

### Recruitment Pipeline Steppers
- A segmented pipeline tracking stage progression: *Candidature envoyée*, *Match validé*, *Entretien programmé*, *Contrat signé*.
- Completed steps display solid `#10B981` nodes with connecting active bars; pending steps use pulsating `#6366F1` halos; future stages remain light muted slate.