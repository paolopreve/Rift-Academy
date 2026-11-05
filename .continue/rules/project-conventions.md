---
name: Rift Academy conventions
description: Project rules for the Rift Academy PHP/MySQL site (security, XML-valid HTML, helpers, schema, accessibility of generated HTML)
alwaysApply: true
---

# Rift Academy: project conventions

Rift Academy is a university web-technologies project: plain PHP (no framework), MySQL, pure CSS, vanilla JS.
Follow these rules whenever you write or edit code in this repository. Also follow the accessibility rules file (WCAG 2.2 AA).

## 1. How to work

- Change only the file(s) the user asked for. Do not rename, move or delete files.
- Complete the `TODO` comments in place, then delete each `TODO` you finished. Keep the existing structure and the existing `render_page()` call.
- Reuse the helpers in `includes/`. Never write a second version of a helper that already exists.
- Do not add libraries, frameworks, Composer packages or CDN links. No jQuery.
- Keep code simple and readable: this is a student project that the authors must be able to explain at an oral exam.
- Write comments in Italian. Write all user-facing text (labels, messages, errors) in Italian.

## 2. Target environment (compatibility)

- Code must run on PHP 7.3. Do NOT use PHP 8 features: no `match`, no named arguments, no `str_contains`/`str_starts_with`, no union types, no constructor property promotion, no nullsafe `?->`.
- MySQL 5.6 or newer, InnoDB, utf8mb4.
- All URLs in HTML, CSS, JS and `redirect()` are RELATIVE (`css/style.css`, `guide_detail.php?id=3`). Never start a path with `/` and never hard-code a host.

## 3. Project layout

- All pages and POST action scripts are in the project root.
- `includes/config.php`: DB constants. `includes/db.php`: `db()` returns a PDO. `includes/auth.php`: session, roles, CSRF. `includes/functions.php`: escaping, messages, rendering.
- `templates/`: `header.html`, `footer.html` and one `<page>.html` per page, with `{{placeholder}}` markers.
- `css/`, `js/`, `img/`, `sql/`. `tools/` is local only.
- Every page starts with `require_once 'includes/auth.php';` and, when it uses the database, `require_once 'includes/db.php';`.

## 4. Helpers that already exist (use them)

- `e($value)`: escapes for HTML output. `redirect($url)`, `require_post()`, `post_str($key)` (trimmed string), `get_int($key)`.
- `flash_set($message)` sets a one-time message. `errors_html(array $errors)` builds the error list HTML (already escaped).
- `render_page($template, ['title' => ..., 'description' => ...], ['placeholder' => $html])`. Values in the third argument are raw HTML, so escape every user-supplied piece with `e()` before putting it there.
- `current_user()` returns `['id','username','role']` or `null`. `login_user($row)`, `logout_user()`, `require_login()`, `require_admin()`, `is_owner_or_admin($ownerId)`.
- `csrf_field()` returns the hidden input for forms, `csrf_check()` verifies it.
- `db()` returns the shared PDO (exceptions on, associative arrays, no emulated prepares).

## 5. Security (mandatory)

- Database: ALWAYS PDO prepared statements with bound parameters. Never concatenate or interpolate user input into SQL. For `LIKE` searches, bind the pattern as a parameter and escape `%` and `_` in the user text.
- XSS: escape with `e()` at OUTPUT time, every time user data is printed. Do not escape when saving to the database.
- Authentication: store passwords with `password_hash($password, PASSWORD_DEFAULT)` and check them with `password_verify()`. Never log or print passwords or hashes.
- Login: use one generic error message for wrong username or password. Reject users whose `status` is not `'active'`. Call `login_user()` after a successful check (it regenerates the session id).
- Authorization is checked ON THE SERVER for every protected action. Hiding a button is not protection.
  - Pages for registered users call `require_login()`; admin pages call `require_admin()`.
  - Editing or deleting a guide or comment: load the row, then check `is_owner_or_admin((int) $row['user_id'])`; otherwise respond with HTTP 403.
  - An admin must not be able to delete or suspend their own account.
- CSRF: every form that changes data uses `method="post"` with `<?php echo csrf_field(); ?>`/`csrf_field()`, and the handler calls `csrf_check()` first. Never change data from a GET request.
- Validation: validate on the server even if `js/validation.js` already validated. Check presence, type, length and allowed values (ids must be positive integers, ratings 1 to 5, ids must exist in the database).
- Never show database errors, stack traces or file paths to the user. Log with `error_log()` and show a generic Italian message.
- Redirect after a successful POST (`flash_set()` then `redirect()`), so that a reload does not resubmit the form.

## 6. Database (schema in sql/schema.sql)

Tables: `users`, `roles`, `champions`, `champion_roles`, `items`, `runes`, `guides`, `guide_items`, `guide_runes`, `comments`, `ratings`, `favorites`, `reports`.

- `users.role` is `'user'` or `'admin'`; `users.status` is `'active'` or `'suspended'`. Suspension blocks login but keeps content. Deleting a user cascades to their guides, comments, ratings and favorites.
- `guides`: `user_id`, `champion_id`, `role_id`, `title`, `strategy_text`, `patch`, `created_at`, `updated_at`. In every `UPDATE` of a guide set `updated_at = NOW()`.
- `ratings` has primary key `(guide_id, user_id)`: one rating per user per guide, value 1 to 5 (validate in PHP). Use `INSERT ... ON DUPLICATE KEY UPDATE`. The average rating is computed with `AVG(value)`, never stored.
- `favorites` has primary key `(user_id, guide_id)`.
- `reports`: exactly one of `guide_id` or `comment_id` is set (validate in PHP). New reports have `status = 'open'`.
- Creating or editing a guide writes to `guides`, `guide_items` and `guide_runes`: wrap these in a transaction (`beginTransaction`, `commit`, `rollBack` on exception).
- Champion lane roles (Top, Jungle, Mid, Bot, Support) are stored in `roles` and `champion_roles`.

## 7. HTML must be XML-compatible (course requirement)

This applies to templates AND to any HTML written inside PHP strings.

- Lowercase tag and attribute names. Every tag closed. Void elements self-closed with a space: `<br />`, `<input ... />`, `<meta ... />`, `<img ... />`.
- All attribute values in double quotes. Boolean attributes carry a value: `required="required"`, `checked="checked"`, `selected="selected"`, `disabled="disabled"`.
- Escape `&` as `&amp;` in URLs written in HTML attributes (`href="search.php?q=a&amp;page=2"`).
- No inline `style=""`, no inline event handlers (`onclick=""`), no `<script>` blocks with logic and no `<style>` blocks. CSS goes in `css/`, JS in `js/`.
- Keep presentation out of PHP: build small HTML fragments in PHP only when a template cannot do it, and keep the page structure in `templates/`.
- Do not use deprecated presentational tags or attributes (`<center>`, `<font>`, `align=`, `border=`).

## 8. Accessibility of generated HTML (WCAG 2.2 AA)

The accessibility rules file is the full reference. For PHP-generated HTML in particular:

- Use native elements first: `<button>` for actions, `<a href>` for navigation, `<ul>` for lists, `<table>` with `<th scope>` for tabular data.
- Headings stay in order (`h1`, then `h2`, then `h3`), one `h1` per page, and the page `<title>` is unique.
- Every form field has a visible `<label for>` linked to the field `id`. Group radios and checkboxes in `<fieldset>` with `<legend>`. Do not use placeholder as a label.
- Server-side validation errors: list them with `errors_html()`, and for each invalid field set `aria-invalid="true"` and `aria-describedby` pointing at its error message. Error text says what is wrong and how to fix it.
- Status messages use `role="status"`; critical errors use `role="alert"` (the helpers already do this).
- Images have meaningful `alt` text; decorative images have `alt=""`. Champion, item and rune images get the item name as `alt`.
- Links have descriptive text (never "clicca qui"). Do not convey information by colour alone.
- Icon-only buttons need an accessible name.
- Destructive actions (delete, suspend) are `<button type="submit">` inside a POST form, with clear text.
- Tables of data have a `<caption>` and header cells. Do not use tables for layout.
- Pagination and lists use `<nav aria-label="...">` and `<ul>`.
- Keep keyboard order equal to DOM order. Never use `tabindex` above 0.

## 9. CSS and JavaScript

- CSS: pure CSS3 in `css/style.css` (Flexbox and Grid). Mobile-first, relative units (`rem`, `em`, `%`). Never remove focus outlines without a visible replacement (at least 3:1 contrast). Text contrast at least 4.5:1. Respect `prefers-reduced-motion`. Interactive targets at least 24x24 px.
- JavaScript: vanilla JS, progressive enhancement. The site must work with JavaScript disabled because the server validates everything. No inline handlers: attach listeners from the script file. Do not set `user-scalable=no` or restrict zoom.
- `js/validation.js` validates forms that have the `data-validate="data-validate"` attribute. Use `data-minlength` for minimum lengths.

## 10. Before you finish a file

Check this list silently and fix what fails:

1. All SQL uses prepared statements. All printed user data goes through `e()`.
2. Every protected page or action has its `require_login()` / `require_admin()` and ownership check. Every POST handler has `require_post()` and `csrf_check()`.
3. The HTML it outputs is XML-compatible and follows the accessibility rules.
4. It runs on PHP 7.3 and uses relative paths only.
5. The finished `TODO` comments are removed, and any TODO you could not finish is left in place and mentioned in your answer.
