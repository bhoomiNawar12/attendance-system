---
name: Academic Excellence System
colors:
  surface: '#fbf8ff'
  surface-dim: '#dad9e3'
  surface-bright: '#fbf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f2fc'
  surface-container: '#eeedf7'
  surface-container-high: '#e8e7f1'
  surface-container-highest: '#e3e1eb'
  on-surface: '#1a1b22'
  on-surface-variant: '#444653'
  inverse-surface: '#2f3037'
  inverse-on-surface: '#f1f0fa'
  outline: '#757684'
  outline-variant: '#c4c5d5'
  surface-tint: '#3755c3'
  primary: '#00288e'
  on-primary: '#ffffff'
  primary-container: '#1e40af'
  on-primary-container: '#a8b8ff'
  inverse-primary: '#b8c4ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#611e00'
  on-tertiary: '#ffffff'
  tertiary-container: '#872d00'
  on-tertiary-container: '#ffa583'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde1ff'
  primary-fixed-dim: '#b8c4ff'
  on-primary-fixed: '#001453'
  on-primary-fixed-variant: '#173bab'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#ffdbce'
  tertiary-fixed-dim: '#ffb59a'
  on-tertiary-fixed: '#380d00'
  on-tertiary-fixed-variant: '#802a00'
  background: '#fbf8ff'
  on-background: '#1a1b22'
  surface-variant: '#e3e1eb'
typography:
  display:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  h1:
    fontFamily: Inter
    fontSize: 30px
    fontWeight: '600'
    lineHeight: 38px
    letterSpacing: -0.01em
  h2:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  h3:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  container-max: 1440px
  sidebar-width: 280px
---

## Brand & Style
The design system is built upon a foundation of **Institutional Minimalism**. It targets university administrators, faculty, and students who require a high-utility environment that minimizes cognitive load. The aesthetic is strictly professional, utilizing a "Corporate Modern" approach that favors clarity, structural integrity, and disciplined white space over decorative elements.

The emotional response should be one of reliability and focus. By stripping away gradients and neon accents, the system directs the user's attention entirely toward academic data and administrative tasks. The interface feels established and authoritative, reflecting the prestige of a higher education institution while maintaining the efficiency of a modern SaaS platform.

## Colors
This design system employs a high-contrast, limited palette to ensure maximum legibility and professional rigor. 

- **Primary Blue (#1e40af):** Reserved for primary actions, active navigation states, and key data highlights. It represents the "Institutional" voice.
- **Neutrals:** A range of Cool Grays are used to create hierarchy. `#ffffff` is the absolute background, while `#f8fafc` (Slate 50) is used for secondary surface areas like sidebars or card backgrounds.
- **Typography:** Text must adhere to high-contrast ratios. Primary headings use `#0f172a` (Slate 900) to ensure they anchor the page layout effectively.
- **Status:** Success, Warning, and Error states should use flat, desaturated versions of green, amber, and red to remain consistent with the professional tone.

## Typography
The system utilizes **Inter** for all roles to maintain a systematic, utilitarian appearance. The typeface was chosen for its exceptional legibility in data-heavy environments.

Headlines use a tighter letter-spacing and heavier weights to provide clear entry points for the eye. Body text maintains a standard 1.5x line-height ratio to ensure long-form academic content remains readable. For information density, `body-sm` is the workhorse for table data and metadata, while `label-sm` in uppercase is reserved for small categorizations and table headers.

## Layout & Spacing
The layout follows a **Fixed-Fluid Hybrid** model. On desktop, a persistent sidebar (280px) sits on the left, while the main content area occupies the remaining width up to a 1440px maximum container. 

The spacing rhythm is built on a 4px baseline grid. 
- **Margins:** 32px (xl) for outer page margins on desktop; 16px (md) for mobile.
- **Gutters:** 24px (lg) between main dashboard widgets and cards.
- **Internal Padding:** Cards and containers use 24px (lg) padding to ensure content feels breathable and premium.
- **Responsive Behavior:** On tablet, the sidebar collapses into a hamburger menu. Content stacks vertically on mobile devices with reduced horizontal padding.

## Elevation & Depth
This design system avoids heavy shadows and multiple light sources. Depth is communicated through **Subtle Ambient Shadows** and **Tonal Layering**.

1.  **Level 0 (Base):** Solid white (`#ffffff`) background.
2.  **Level 1 (Surface):** Secondary sections like the sidebar use a subtle tint (`#f8fafc`) with a 1px border (`#e2e8f0`) and no shadow.
3.  **Level 2 (Cards/Widgets):** Main content containers use a white background with a very soft, diffused shadow: `0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)`.
4.  **Level 3 (Interactive):** Elements like dropdowns or active modals use a slightly more pronounced shadow to indicate they are "floating" above the interface.

Avoid any use of blurs, glassmorphism, or colored shadows. All depth must feel "grounded" and physical.

## Shapes
The shape language is defined by a consistent 8px (`0.5rem`) corner radius. This "Rounded" setting provides a modern feel that is friendlier than sharp corners but more professional than pill-shaped elements.

- **Primary Components:** Buttons, Input Fields, and Cards all use the 8px radius.
- **Large Containers:** Modals or prominent dashboard panels may scale up to `rounded-lg` (16px) to soften the visual impact of large blocks.
- **Small Elements:** Checkboxes use a 4px radius for a crisper appearance within small scales.
- **Icons:** Should follow a similar path; avoid ultra-thin or ultra-bold icon strokes. A 2px stroke width is preferred to match the typography's visual weight.

## Components

### Buttons
- **Primary:** Solid `#1e40af` background with white text. No gradients.
- **Secondary:** White background with `#e2e8f0` border and `#475569` text.
- **State:** Hover states should simply darken the background color by 5-10% (e.g., `#1e3a8a`).

### Input Fields
- **Default:** 1px border (`#e2e8f0`), 8px radius, and `body-sm` text.
- **Focus:** 1px solid `#1e40af` border with a subtle 2px blue outer glow (halo) at 10% opacity.

### Cards
- White background, 8px radius, 1px border (`#f1f5f9`), and the Level 2 ambient shadow.
- Header sections within cards should be separated by a light horizontal rule.

### Data Tables
- Header row: `#f8fafc` background, `label-sm` bold text, all caps.
- Rows: 1px bottom border (`#f1f5f9`), 48px minimum height for vertical centering.
- Active/Selected Row: Very subtle blue tint (`#eff6ff`).

### Navigation Sidebar
- High-contrast text on a light gray background (`#f8fafc`).
- Active link: Primary blue text with a 4px vertical "indicator bar" on the left edge.
- Icons: Monochromatic Slate (`#64748b`), switching to Primary Blue on active.