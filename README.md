# Verbena Aesthetics: custom WordPress theme

A custom classic (PHP template) WordPress theme for **Verbena Aesthetics**, a
fictional medspa in Uptown Dallas. I built it to show the kind of work an
agency does for salon, spa and medspa clients: a bespoke theme from scratch,
Tailwind CSS, ACF Pro fields, Gravity Forms, technical SEO, GA4/GTM tracking
and a third-party booking widget, all tuned for speed.

The business, people, prices and reviews are invented. The footer says so on
every page.

- Live demo: https://mediumslateblue-armadillo-787648.hostingersite.com/
- Repository: https://github.com/KeronLewisGit/verbena-medspa-theme

Activating the theme on a fresh WordPress install builds the complete demo
site (pages, 8 treatments, images, menus, settings). No plugins, importer or
admin clicks are needed.

## Features and where they live

| Feature | Files |
| --- | --- |
| Bootstrap only, logic split into modules | `functions.php`, `inc/*.php` |
| Tailwind CSS v4 with design tokens in `@theme`, brand CSS variables on `:root` | `src/app.css` → `assets/css/app.css` |
| ACF Pro / SCF field groups registered in PHP | `inc/fields-schema.php`, `inc/acf-fields.php` |
| Single field reader with a no-plugin fallback | `inc/helpers.php` (`verbena_field()`, `verbena_option()`) |
| `treatment` post type + `treatment_area` taxonomy, private `verbena_lead` type | `inc/post-types.php` |
| Archive with area filter, single treatment, area archives | `archive-treatment.php`, `single-treatment.php`, `taxonomy-treatment_area.php` |
| Page templates: About, Memberships, Book/Contact, FAQ | `templates/` |
| One inner-page hero component on every non-home page | `template-parts/page-hero.php` |
| Gravity Forms styling + `generate_lead` on confirmation | `inc/gravity-forms.php` |
| Built-in consultation form (fallback when GF isn't configured) | `inc/forms.php`, `template-parts/consultation-form.php` |
| Meta title/description, canonical, Open Graph, Twitter, JSON-LD | `inc/seo.php` |
| 301/302 redirect manager with hit counter (Tools → Redirects) | `inc/redirects.php` |
| GTM / GA4 from settings, dataLayer events | `inc/analytics.php`, `assets/js/app.js` |
| `[verbena_booking]` shortcode, click-to-load booking facade | `inc/booking.php`, `template-parts/booking-widget.php` |
| Front-end clean-up (jQuery, emoji, oEmbed, block CSS) | `inc/performance.php` |
| Demo content on activation + "Reset demo content" (Tools → Verbena demo) | `inc/demo-content.php`, `inc/demo-data.php` |
| Native settings screen when no ACF plugin is active | `inc/settings-fallback.php` |
| Editor palette and fonts | `theme.json` |

## ACF Pro / Secure Custom Fields

Field groups are defined once, as PHP arrays, in `inc/fields-schema.php` and
registered with `acf_add_local_field_group()` on `acf/init`. That covers:

- **Treatment details**: card summary, price from, price unit, appointment
  length, downtime, areas treated, typical plan, before/after notes, booking
  service ID and an FAQ repeater.
- **Home page sections**: hero, intro, "how it works" steps (repeater),
  testimonials (repeater).
- **Page hero** for every other page, **membership tiers** (repeater), the
  **FAQ page** repeater and a **Search & social** box (meta title,
  description, noindex) on pages and treatments.
- **Site settings** options page: phone, email, address, geo, opening hours
  (repeater), social links, booking provider and URL, GTM and GA4 IDs,
  Gravity Forms form ID, lead email, provider team (repeater).

Templates never call `get_field()` directly. They call
`verbena_field( $name, $post_id )`, which uses `get_field()` when ACF Pro, ACF
or the free Secure Custom Fields plugin is active, and otherwise reads the
same post meta / `options_*` rows itself (including repeaters, using the field
definitions to know the sub field names). The demo importer writes values in
ACF's storage format, field-key references included, so the site renders
identically with or without the plugin, and turning SCF on later shows every
value in the field UI. I checked this by diffing the rendered HTML of nine
pages with SCF off and on: no differences.

Without an ACF plugin, Site settings falls back to a plain settings screen
(`inc/settings-fallback.php`) that saves to the same option names.

## Gravity Forms

`inc/gravity-forms.php` returns early unless Gravity Forms is loaded. When it
is, it:

- turns off GF's own CSS and adds Tailwind classes through
  `gform_field_css_class` and `gform_field_content`,
- swaps the submit input for the theme button via `gform_submit_button`,
- restyles the validation message,
- appends a `generate_lead` dataLayer push to text confirmations through
  `gform_confirmation`.

The Book page renders the GF form when GF is active **and** a form ID is set
in Site settings. Otherwise it renders the built-in form, which posts to
`admin-post.php` and has: a nonce, a honeypot field and a minimum fill time,
server-side validation, old input and per-field errors after the redirect
(`aria-invalid` + `aria-describedby`), a light per-IP rate limit, storage as a
private Lead (with admin columns), a `wp_mail()` notification with Reply-To,
and a success state that fires the same `generate_lead` event once.

GF loads jQuery. `inc/performance.php` only dequeues jQuery when nothing in
the queue depends on it, so GF pages keep it.

## Analytics: GTM / GA4 dataLayer events

Set a GTM container ID (or only a GA4 measurement ID) in Site settings. IDs are
validated (`GTM-…`, `G-…`) and nothing is printed when they're empty. Logged-in
editors aren't tracked (filter: `verbena_track_editors`).

With GTM, the theme pushes these events. With GA4 only (gtag.js), the same
events are also sent with `gtag('event', …)`.

| Event | When | Parameters |
| --- | --- | --- |
| `book_now_click` | Any Book button, or loading the booking calendar | `treatment_name` (e.g. `HydraFacial`, `(not set)` for general buttons), `link_location` (`header`, `home_hero`, `treatment_hero`, `treatment_sidebar`, `membership_tier`, `cta_band`, `mobile_bar`, `booking_widget`, …) |
| `phone_click` | Any `tel:` link | `link_location` |
| `generate_lead` | Consultation form submitted successfully (built-in or Gravity Forms) | `form_id`, `form_name`, `treatment_name` (built-in form) |

GTM setup: create a Custom Event trigger for each event name, Data Layer
Variables for the parameters, and a GA4 Event tag per trigger. Mark
`generate_lead` as a key event in GA4.

## SEO

Built into the theme, and switched off automatically if Yoast, Rank Math,
SEOPress or AIOSEO is active (filter: `verbena_seo_enabled`).

- Per-page meta title and description from the Search & social fields, with
  sensible fallbacks (treatment summary, hero intro, excerpt, tagline).
- Canonical URLs (core's `rel_canonical` is replaced so archives get one too),
  Open Graph and Twitter card tags with real image dimensions, per-page
  `noindex`.
- One JSON-LD `@graph` per page:
  - `MedicalBusiness` + `HealthAndBeautyBusiness` with address, geo,
    `openingHoursSpecification`, `sameAs`, phone, price range,
  - `WebSite`, `WebPage` / `CollectionPage`,
  - `BreadcrumbList` built from the same data as the visible breadcrumb,
  - `Service` on each treatment, with an `Offer` / `PriceSpecification`
    (`minPrice`),
  - `FAQPage` from the treatment FAQ repeater and from the FAQ page.
- Visible FAQ accordions use `<details>`/`<summary>` with an H3 per question.
  Every page has one H1 and a clean H2/H3 outline.
- Redirect manager under Tools → Redirects: 301/302 rules in one option,
  matched on `template_redirect` (case- and trailing-slash-insensitive), with
  a hit counter and last-hit time. The demo seeds three examples, e.g.
  `/services/botox/` → `/treatments/neurotoxins/`.

## Booking widget

`[verbena_booking treatment="hydrafacial" height="760"]` renders a static
panel with a "Load booking calendar" button. The provider iframe (Zenoti,
Boulevard, Vagaro, Phorest, Mindbody, or anything with an embed URL) is only
created when the visitor clicks. Booking embeds typically pull in several
hundred KB of script and extra connections; deferring them until someone wants
to book keeps them out of LCP and TBT on every page that has a Book section.
Without JavaScript, a link opens the calendar in a new tab.

The booking URL, provider name and the provider's service-ID query parameter
are site settings; each treatment has its own service ID, so "Book this
treatment" opens the calendar with that service preselected. With no URL set,
the facade loads a small bundled demo widget (`?verbena_booking_demo=1`) so the
flow can be clicked through on the demo. The Google map on the Book and About
pages uses the same facade.

## Performance

- Tailwind output is ~45 KB minified (~8.5 KB gzipped), one stylesheet.
- One deferred vanilla JS file, ~5.6 KB (~1.9 KB gzipped). No jQuery on the
  front end.
- Emoji script/styles, oEmbed discovery and host JS, RSD, WLW, generator and
  shortlink tags removed; block library CSS dropped on pages without blocks.
- Self-hosted WOFF2 fonts, latin subset, `font-display: swap`; the display
  font is preloaded.
- Images go through `wp_get_attachment_image()` for `srcset`, `sizes`,
  `width`/`height` and `loading="lazy"`; the hero image is
  `loading="eager"` + `fetchpriority="high"`. Bundled images are WebP and the
  importer only generates the sizes the theme uses.

Lighthouse 12, mobile preset, against the local PHP dev server (no gzip, no
cache headers, so the real host should do slightly better):

| Page | Performance | Accessibility | Best practices | SEO |
| --- | --- | --- | --- | --- |
| Home | 97 | 100 | 100 | 100 |
| Treatments archive | 96–97 | 100 | 100 | 100 |
| Single treatment (HydraFacial) | 99 | 100 | 100 | 100 |
| Memberships | 99 | 100 | 100 | 100 |
| About | 97 | 100 | 100 | 100 |
| FAQ | 99 | 100 | 100 | 100 |
| Book | 98 | 100 | 100 | 100 |
| Home (desktop preset) | 100 | 100 | 100 | 100 |

CLS was 0 and TBT 0 ms on every page. The remaining flags were text
compression and cache lifetimes, which are server settings.

## Accessibility and compatibility

Skip link, visible focus rings, 44 px minimum targets on controls, AA contrast
(checked for every text/background pair in the palette), a keyboard-operable
mobile menu (`aria-expanded`, Escape closes and returns focus), a filter that
announces results to screen readers, `prefers-reduced-motion` support, and a
sticky Call / Book bar on small screens. Tested at 390 px and 1440 px.

## Demo content and deployment

The import runs on `after_switch_theme`, which core fires on the first request
after `switch_theme()`. That includes activation through WP-CLI
(`wp theme activate verbena`) or a hosting API, where the theme switch happens
in one process and the hook fires on the next page load. As a backstop, it also
runs on `init` whenever the "imported" flag is missing.

Only one request imports: an `INSERT IGNORE` lock row (the same technique core
uses for upgrader locks) guards it. A visitor who arrives while another request
is importing gets a short 503 page that refreshes itself; the importing
visitor is redirected to the same URL once it's done. Locally a full import
takes 3 to 5 seconds.

The import creates the pages (Home, Treatments, About, Memberships, Book a
Consultation, FAQ, Privacy Policy), 8 treatments with every field filled, three
areas, 19 media library images with alt text and featured images, primary and
footer menus, the static front page, `/%postname%/` permalinks (with
`.htaccess` written where the server allows), site settings and example
redirects. It removes the stock "Hello world!" post and "Sample Page".

Tools → Verbena demo has a **Reset demo content** button (nonce and
`manage_options` checked) that deletes the demo items and imports them again.
Leads and anything created by hand are kept.

Note: the custom post types live in the theme so the demo is a single
package. On a client build they'd go in a small plugin so content survives a
theme change.

## Local development

Requirements: PHP 8.1+, Node 18+, Composer (for coding standards only).

```bash
npm install
npm run dev      # watch src/app.css → assets/css/app.css
npm run build    # minified build (the compiled file is committed)

composer install
vendor/bin/phpcs # WordPress Coding Standards, see phpcs.xml

bash scripts/package.sh   # → dist/verbena.zip (top-level folder verbena/)
```

Local WordPress without MySQL, as used for testing:

1. Download WordPress into `.local-wp/wordpress` (git-ignored).
2. Add the [SQLite Database Integration](https://wordpress.org/plugins/sqlite-database-integration/)
   plugin and copy its `db.copy` to `wp-content/db.php`.
3. Symlink this folder to `wp-content/themes/verbena`.
4. From `wordpress/`, run
   `PHP_CLI_SERVER_WORKERS=4 php -S 127.0.0.1:8140 ../../scripts/dev-router.php`
   (the router sends non-file requests to `index.php` so pretty permalinks
   work), then install WordPress and activate the theme.

`phpcs` reports no errors or warnings against the WordPress ruleset.

## Credits

Photography from Unsplash and fonts from Google Fonts (OFL); full list with
photographer names and links in [CREDITS.md](CREDITS.md).

Theme by [Keron Lewis](https://keronlewis.dev). Licensed GPL-2.0-or-later.
