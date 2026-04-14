# Design System Document: The Intellectual Atelier

## 1. Overview & Creative North Star
**Creative North Star: "The Academic Curator"**

This design system moves beyond the utility of a standard library database to create an environment that feels like a high-end, curated digital archive. While the foundation is inspired by the reliability of Laravel Breeze, the execution is "High-End Editorial." We achieve this by replacing rigid, boxed-in layouts with **Atmospheric Layering** and **Intentional Asymmetry**. 

The goal for SMKN 1 Purwokerto is to provide students with a space that feels authoritative yet breathable—where information isn't just "stored," but "showcased." We emphasize tonal depth over structural lines, creating a professional vocational atmosphere that commands respect.

---

## 2. Colors & Surface Philosophy
The palette is rooted in `primary` (#00236f), a deep, scholarly blue. However, the sophistication lies in how we treat the "whites" and "grays."

### The "No-Line" Rule
To achieve a premium feel, **1px solid borders are strictly prohibited for sectioning.** Boundaries must be defined solely through background color shifts.
*   **Application:** A sidebar uses `surface_container_low`, the main content area uses `surface`, and a featured book panel uses `surface_container_highest`. This creates a "molded" look rather than a "sketched" look.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers.
*   **Base Layer:** `surface` (#f7f9fb)
*   **Secondary Sections:** `surface_container_low` (#f2f4f6)
*   **Interactive Cards:** `surface_container_lowest` (#ffffff)
*   **Prominent Modals/Popovers:** `surface_bright` (#f7f9fb)

### The "Glass & Gradient" Rule
Standard flat colors feel static. Use the following for visual "soul":
*   **Signature Gradients:** For primary CTAs and Hero sections, use a subtle linear gradient: `primary` (#00236f) to `primary_container` (#1e3a8a) at a 135-degree angle.
*   **Glassmorphism:** For floating navigation or "Quick View" book overlays, use `surface_container_lowest` at 80% opacity with a `20px` backdrop-blur.

---

## 3. Typography: Editorial Authority
We utilize **Inter** not just for legibility, but as a structural element. 

*   **Display & Headlines:** Use `display-md` and `headline-lg` with tight letter-spacing (-0.02em) to create a bold, editorial impact for library categories (e.g., "Engineering & Tech").
*   **Body & Labels:** `body-md` is your workhorse. Use `on_surface_variant` (#444651) for body text to reduce eye strain, reserving `on_surface` (#191c1e) for high-importance titles.
*   **The Tonal Scale:** Use `label-md` in uppercase with `0.05em` tracking for metadata (e.g., ISBN, Call Numbers) to give it a "cataloged" professional feel.

---

## 4. Elevation & Depth
Depth is achieved through **Tonal Layering** rather than traditional drop shadows.

*   **The Layering Principle:** A `surface_container_lowest` card sitting on a `surface_container_low` background creates a natural lift. This is our primary method of containment.
*   **Ambient Shadows:** For floating elements (like a book detail modal), use a shadow tinted with the primary brand color: `0 20px 40px rgba(0, 35, 111, 0.06)`. This feels like natural, ambient light in a bright room.
*   **The "Ghost Border" Fallback:** If a border is required for accessibility (e.g., input fields), use `outline_variant` at **20% opacity**. Never use 100% opaque lines.

---

## 5. Components

### Sidebar Navigation
*   **Structure:** No vertical divider. Use a `surface_container_low` background. 
*   **Active State:** Instead of a box, use a "pill" shape (`rounded-full`) with a `primary_fixed` (#dce1ff) background and `on_primary_fixed` (#00164e) text.

### Book Cards
*   **Container:** `surface_container_lowest` with a `lg` (0.5rem) corner radius.
*   **Visuals:** The book cover should have a subtle `0.125rem` (sm) radius.
*   **Metadata:** Forbid dividers. Use `body-sm` text with `1.5rem` vertical spacing to separate the author from the availability status.

### Status Badges (The "Tonal Badge")
Avoid heavy, saturated background colors. Use the "Fixed" color tokens:
*   **Borrowed (Pending):** `tertiary_fixed` (#ffdbcb) background with `on_tertiary_fixed` (#341100) text.
*   **Returned:** `secondary_fixed` (#d3e4fe) background with `on_secondary_fixed` (#0b1c30) text.
*   **Overdue:** `error_container` (#ffdad6) background with `on_error_container` (#93000a) text.

### Clean Data Tables
*   **Header:** `surface_container_high` with `label-md` typography.
*   **Rows:** Alternating rows are forbidden. Use whitespace and a `1px` Ghost Border (10% opacity) only at the bottom of each row.
*   **Interaction:** On hover, the row background shifts to `surface_container_low`.

### Input Fields
*   **Style:** Minimalist. No background color (transparent). Only a bottom "Ghost Border" that transforms into a `2px` `primary` line on focus.

---

## 6. Do's and Don'ts

### Do:
*   **Do** use asymmetrical margins. For example, give the page title more "top-room" than "side-room" to create a modern, magazine-like feel.
*   **Do** use `primary_container` for secondary buttons to keep the brand's blue DNA present without the weight of the deep blue.
*   **Do** prioritize white space. If you think there is enough space, add 8px more.

### Don't:
*   **Don't** use pure black (#000000) for text. Always use `on_surface`.
*   **Don't** use standard Laravel Breeze "gray" borders. They make the high-end blue look like a template. 
*   **Don't** use sharp corners. Every component must follow the `Roundedness Scale`, specifically `lg` (0.5rem) for cards and `md` (0.375rem) for buttons.