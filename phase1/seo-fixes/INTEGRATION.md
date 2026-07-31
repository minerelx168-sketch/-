# SEO Fixes — Integration Guide for Manus / imeihub.net

## What this folder contains

25 markdown content files that fix Ubersuggest's "low word count" flag on imeihub.net:

- **`brands/*.md`** — 12 files, one per brand slug (`apple`, `google`, `huawei`, `motorola`, `nokia`, `oneplus`, `oppo`, `realme`, `samsung`, `sony`, `vivo`, `xiaomi`). Each 1,200-1,500 words.
- **`services/*.md`** — 13 files, one per service slug (`apple-basic`, `apple-full-gsx`, `apple-icloud`, `apple-icloud-status`, `apple-mdm`, `apple-sim-lock`, `apple-warranty`, `blacklist`, `free-imei-check`, `huawei-info`, `pixel-info`, `samsung-info`, `xiaomi-status`). Each 1,200-1,650 words.

All files have identical structure: YAML frontmatter → H1 title → body H2 sections → FAQ H2 → JSON-LD `<script type="application/ld+json">` block at end.

## Before / After per page

| Page type | Before (words) | After (target) | Ubersuggest thin-content flag |
|-----------|----------------|----------------|-------------------------------|
| brand.php?slug=X | 88-110 | 1,200-1,500 | ✅ Cleared |
| service.php?slug=X | 88-108 | 1,200-1,650 | ✅ Cleared |

## Two integration paths — pick one

### Option A (recommended): Insert into DB `brands` / `services` tables

Assuming `brands` and `services` tables have (or you add) a `long_content` TEXT column:

```sql
-- One row per brand
UPDATE brands SET long_content = <markdown-body-of-brands/apple.md>, faq_schema_json = <json-ld-from-apple.md>
  WHERE slug = 'apple';
-- repeat for all 12 brands
```

Then update `brand.php` / `service.php` to render:
```php
echo "<article class='seo-content'>";
echo Parsedown::instance()->text($row['long_content']);
echo "</article>";
if (!empty($row['faq_schema_json'])) {
  echo "<script type='application/ld+json'>{$row['faq_schema_json']}</script>";
}
```

### Option B: Static PHP includes

Convert each `.md` to `.php` via a build step (or manually via Parsedown), then in `brand.php` / `service.php`:
```php
$content_file = __DIR__ . "/content/brands/{$slug}.php";
if (file_exists($content_file)) include $content_file;
```

## Pricing — VERIFY BEFORE PUBLISH

Agents used the confirmed Apple prices ($0.01, $0.03, $0.10, $0.35, $4.20) verbatim. For other services these prices were estimated within the given $0.05–$0.40 range — **swap for production values before insert**:

| Service slug | Frontmatter price | Status |
|--------------|-------------------|--------|
| apple-basic | 0.10 | ✅ Confirmed |
| apple-icloud-status | 0.01 | ✅ Confirmed |
| apple-icloud | 0.03 | ✅ Confirmed |
| apple-mdm | 0.35 | ✅ Confirmed |
| apple-full-gsx | 4.20 | ✅ Confirmed |
| apple-sim-lock | 0.15 | ⚠️ Approx — verify |
| apple-warranty | 0.10 | ⚠️ Approx — verify |
| blacklist | 0.30 | ⚠️ Approx — verify |
| free-imei-check | 0.00 | ✅ Free |
| huawei-info | 0.15 | ⚠️ Approx — verify |
| pixel-info | 0.20 | ⚠️ Approx — verify |
| samsung-info | 0.25 | ⚠️ Approx — verify |
| xiaomi-status | 0.20 | ⚠️ Approx — verify |

The prose in every paid-service body says "see current per-check pricing on the service page" — the numeric price only surfaces in the YAML frontmatter and JSON-LD `Offer` schema. Swap the number in **both** places once production pricing is confirmed.

## Schema markup

Every file has a `<script type="application/ld+json">` block at the bottom containing:
- Brand pages: `Service` + `FAQPage` + `Brand`
- Service pages: `Service` + `FAQPage` + `Offer`

Copy the JSON-LD verbatim into the rendered page `<head>` (or just before `</body>`) — DO NOT wrap in `<pre>` or markdown code fences on the live page.

## Internal links

Each content block includes 2-4 internal links pointing at:
- `/pricing.php`
- `/service.php?slug=<related-service>`
- `/brand.php?slug=<related-brand>`

**If the production URL structure differs** (e.g., you switch to clean URLs `/service/apple-icloud/`), do a find-and-replace across all 25 files before insert.

## Guardrails baked into the copy

- **Verification-only positioning** — no "unlock", "bypass", "jailbreak", or "activate" claims anywhere. imeihub is a diagnostic service.
- **No named third-party API providers** — copy says "certified IMEI API partners" / "GSMA-linked blacklist database" generically. Do not add DHRU / sickw / imei.info references in production.
- **Region-aware examples** — Xiaomi CN vs Global ROM, Samsung US SM-S928U vs INTL SM-S928B, Pixel Fi-locked, Huawei US ban context, HMD-vs-Nokia attribution. These are real buyer pain points, not filler.

## Publish order recommendation

Ubersuggest re-scan usually picks up new content within 2-4 weeks. To maximize compounding effect:

1. **Week 1** — Insert all 12 brand pages (highest search volume: "iphone imei check", "samsung imei", "xiaomi imei").
2. **Week 2** — Insert all 13 service pages (commercial-intent long-tail).
3. **Week 3** — Submit updated sitemap to Google Search Console; request re-index for each updated URL via GSC URL Inspection.
4. **Week 4** — Run Lighthouse SEO audit on 3-4 sample pages, target ≥ 90.

Do NOT bulk-insert everything in one hour — Google may throttle re-crawl of a large batch. Stagger over 2-3 days if possible.

## After integration

- Verify a random 3-4 pages render correctly (H2 structure, FAQ collapse if styled, JSON-LD parses at https://validator.schema.org/)
- Check that Ubersuggest re-scan shows word counts > 1,200 (may take 2-3 weeks)
- Watch Google Search Console for improved impressions on brand-name + IMEI keyword combinations
