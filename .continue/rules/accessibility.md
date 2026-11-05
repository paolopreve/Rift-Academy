# Frontend Web Accessibility (WCAG 2.2 AA) Rules & Guidelines

Use these rules to generate, rewrite, or audit all HTML, CSS, JavaScript, and UI components in this workspace.

Rules marked **(WCAG)** map to a success criterion and are failures if violated. Rules marked **(Best practice)** are strongly recommended but are not WCAG failures on their own.

---

## 1. Semantic HTML Structure

- **Landmarks (WCAG 1.3.1, 2.4.1):** Use native tags (`<header>`, `<nav>`, `<main>`, `<aside>`, `<footer>`) instead of generic `<div class="...">` containers. Use exactly one `<main>` per page.
- **Page language and title (WCAG 3.1.1, 2.4.2):** Set `lang` on `<html>` (e.g. `<html lang="en">`) and give every page a unique, meaningful `<title>`.
- **Skip link (WCAG 2.4.1):** Provide a "Skip to main content" link as the first focusable element. It may be visually hidden until focused, but must become visible on focus.
- **Heading hierarchy (Best practice):** Keep a logical order (`<h1>` through `<h6>`), do not skip levels, and use one `<h1>` per page. Headings must describe the content that follows (WCAG 2.4.6).
- **Buttons vs. links (WCAG 4.1.2):**
  - Use `<a href="...">` only for navigation that changes the URL.
  - Use `<button type="button">` for actions, toggles, and script-triggered events.
- **Lists and tables (WCAG 1.3.1):** Use `<ul>`/`<ol>` for lists. Use `<table>` only for tabular data, with `<th scope>` headers and a `<caption>` where helpful.

---

## 2. Forms & Inputs

- **Explicit labeling (WCAG 1.3.1, 3.3.2, 4.1.2):** Every input needs a visible `<label>` linked via `for`/`id`. Placeholders are not labels.
  - Bad: `<input type="text" placeholder="Search...">`
  - Good: `<label for="search-input">Search</label><input id="search-input" type="text">`
- **Label in Name (WCAG 2.5.3):** The accessible name must contain the visible label text. Do not set an `aria-label` that differs from the visible text.
- **Required fields (WCAG 3.3.2):** Use the native `required` attribute alone. Do not add `aria-required="true"` to native inputs; it is redundant. Also indicate required fields visibly (e.g. "(required)"), not by color alone.
- **Error states (WCAG 3.3.1, 3.3.3):**
  - Set `aria-invalid="true"` on inputs that fail validation.
  - Connect the error text with `aria-describedby="error-id"`.
  - Describe the problem in text and suggest how to fix it.
  - Move focus to the first invalid field, or to an error summary, after a failed submit.
- **Autocomplete (WCAG 1.3.5):** Use appropriate `autocomplete` tokens on personal-data fields (`name`, `email`, `tel`, `street-address`, `current-password`, `new-password`, etc.).
- **Redundant entry (WCAG 3.3.7):** Do not ask users to re-enter information they already provided in the same process, unless essential or for security.
- **Accessible authentication (WCAG 3.3.8):** Do not block paste or password managers, and do not require a cognitive function test (memorizing, transcribing, puzzles) without an alternative.
- **Group related controls (WCAG 1.3.1):** Wrap radio and checkbox groups in `<fieldset>` with a `<legend>`.

---

## 3. Keyboard Navigation & Focus Management

- **Keyboard access (WCAG 2.1.1, 2.1.2):** Every interactive element must be operable by keyboard alone, with no keyboard traps.
- **Tab sequence (WCAG 2.4.3):** Use native focusable elements in DOM order. Never use `tabindex` values above 0. Use `tabindex="-1"` only for programmatic focus targets.
- **Focus indicators (WCAG 2.4.7, 1.4.11):** Never remove outlines (`outline: none` / `outline: 0`) without a replacement. The indicator needs at least 3:1 contrast against adjacent colors.

  ```css
  :focus-visible {
    outline: 3px solid #005fcc;
    outline-offset: 2px;
  }

  /* Two-tone ring so the indicator works on both light and dark backgrounds */
  .dark-surface :focus-visible {
    outline: 3px solid #ffffff;
    box-shadow: 0 0 0 5px #000000;
  }
  ```

- **Focus not obscured (WCAG 2.4.11):** A focused element must not be fully hidden by sticky headers, footers, cookie banners, or chat widgets. Use `scroll-padding-top` / `scroll-margin` to keep focus targets visible.
- **Custom interactive elements (Best practice):** Prefer a native `<button>` or `<a>`. If a custom element is unavoidable (e.g. `<div role="button">`), it must have `tabindex="0"`, an accessible name, Enter activation on `keydown`, and Space activation on `keyup` with `preventDefault()` to stop page scroll.
- **Target size (WCAG 2.5.8):** Pointer targets must be at least 24×24 CSS px, or have enough spacing so a 24px circle centered on each target does not overlap another target. Inline links in text are exempt.
- **Dragging (WCAG 2.5.7):** Any drag-and-drop or drag-based interaction needs a single-pointer alternative (buttons, menus, tap-to-select).
- **Pointer cancellation (WCAG 2.5.2):** Trigger actions on `click` / pointer-up, not pointer-down.

---

## 4. ARIA Roles, States & Attributes

- **First rule of ARIA:** Do not use ARIA if a native HTML element provides the same semantics. No ARIA is better than bad ARIA.
- **Names and roles (WCAG 4.1.2):** Every interactive element needs an accessible name, a role, and exposed state (checked, expanded, selected, pressed).
- **Disclosure / accordion:** Use a `<button>` with `aria-expanded="true|false"` and `aria-controls="panel-id"`. Update `aria-expanded` whenever the panel toggles.
- **Modals and dialogs:**
  - Prefer native `<dialog>` opened with `showModal()`. It provides focus trapping, an inert background, and Escape-to-close.
  - If using a custom `role="dialog"` container, you must also set `aria-modal="true"` and `aria-labelledby="dialog-title-id"`, move focus into the dialog on open, trap focus inside it, close on Escape, make the rest of the page inert, and return focus to the trigger on close.
- **Live regions and status messages (WCAG 4.1.3):**
  - The live region element must exist in the DOM before its content changes, or announcements may be missed.
  - Prefer `role="status"` (polite) for non-critical updates such as cart counts or "saved".
  - Prefer `role="alert"` (assertive) only for critical errors.
  - Use raw `aria-live` only when neither role fits.
- **Hidden content:** `aria-hidden="true"` must never be placed on or inside focusable elements. Use `hidden` or `inert` to remove content from both the visual and accessibility trees.

---

## 5. Visuals, Color & Media

- **Image alt text (WCAG 1.1.1):**
  - Informative images: concise alt text describing the content or function.
  - Decorative images: `alt=""` (or CSS background images) so screen readers skip them.
  - Complex images (charts, diagrams): short alt text plus a longer description or data table nearby.
  - Images of text: avoid, except logos.
- **Icon-only buttons (WCAG 4.1.2, 2.5.3):** Provide an accessible name via `aria-label` or visually hidden text. Mark the icon itself decorative (`aria-hidden="true"`).
- **Color contrast (WCAG 1.4.3, 1.4.11):**
  - Normal text: at least 4.5:1.
  - Large text (18pt+ or 14pt bold+): at least 3:1.
  - UI components, focus rings, and meaningful icons: at least 3:1 against adjacent colors.
- **Color alone (WCAG 1.4.1):** Never convey information by color alone. Add text, icons, patterns, or underlines (e.g. links within body text, error states, chart series).
- **Media (WCAG 1.2.x):** Provide captions for prerecorded video with audio, transcripts or audio descriptions where needed, and controls to pause and adjust volume. Do not autoplay audio.
- **Motion (WCAG 2.2.2, 2.3.1):**
  - Provide pause, stop, or hide controls for moving, blinking, or auto-updating content lasting more than 5 seconds.
  - Nothing may flash more than 3 times per second.
  - Respect `prefers-reduced-motion`:

    ```css
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
      }
    }
    ```

---

## 6. Responsive, Zoom & Text Resizing

- **Zoom (WCAG 1.4.4):** Never set `user-scalable=no` or a restrictive `maximum-scale` in the viewport meta tag. Content must work at 200% text zoom.
- **Reflow (WCAG 1.4.10):** Content must reflow at 320 CSS px width (400% zoom) without horizontal scrolling, except for data tables, maps, and similar two-dimensional content.
- **Text spacing (WCAG 1.4.12):** Layouts must not break or clip when users override line height (1.5), paragraph spacing (2×), letter spacing (0.12em), and word spacing (0.16em). Avoid fixed heights on text containers.
- **Orientation (WCAG 1.3.4):** Do not lock content to portrait or landscape.
- **Use relative units:** Prefer `rem`/`em` for font sizes and spacing.

---

## 7. Hover, Focus & Consistency

- **Content on hover or focus (WCAG 1.4.13):** Tooltips and popovers must be dismissible (e.g. Escape), hoverable (the pointer can move onto them), and persistent until dismissed.
- **Consistent navigation and identification (WCAG 3.2.3, 3.2.4):** Repeated navigation appears in the same order; components with the same function have the same label.
- **Consistent help (WCAG 3.2.6):** Help links, contact details, or chat appear in the same relative place across pages.
- **No unexpected context changes (WCAG 3.2.1, 3.2.2):** Focusing or changing an input must not submit a form or navigate unexpectedly.

---

## 8. Testing Requirements

Automated tools (axe, Lighthouse, eslint-plugin-jsx-a11y) catch only a portion of issues. Before shipping, also verify:

1. **Keyboard only:** Tab through the whole page. Everything is reachable and operable, focus is always visible, and there are no traps.
2. **Screen reader:** Test key flows with at least one screen reader (NVDA or JAWS on Windows, VoiceOver on macOS/iOS, TalkBack on Android).
3. **Zoom and reflow:** Check at 200% and 400% zoom and at 320px width.
4. **Contrast and color:** Check text, UI components, and focus indicators, and confirm nothing relies on color alone.
5. **Reduced motion and forced colors:** Test with `prefers-reduced-motion` and Windows High Contrast / forced-colors mode.

---

## 9. Code Output Requirements for AI

When generating components under these guidelines:

1. Include inline comments explaining any complex ARIA state transitions or keyboard handling logic.
2. Provide complementary CSS for `:focus-visible` states.
3. Ensure all interactive components have appropriate screen-reader text labels and accessible names.
4. Prefer native HTML elements over ARIA and custom widgets.
5. Note in a short comment which WCAG criteria a non-obvious pattern is addressing.
6. When auditing, report each finding with the element, the violated criterion, severity, and a concrete fix.
