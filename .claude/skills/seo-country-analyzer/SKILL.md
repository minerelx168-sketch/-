---
name: seo-country-analyzer
description: Rank countries by SEO opportunity for a website. Use when the user asks which countries to target for SEO, wants to identify Blue Ocean markets, compare competitor traffic by country, or evaluate international SEO expansion. Combines competitor traffic distribution, regulatory/market factors, and language moats into a scored, ranked recommendation with a strategic roadmap.
allowed-tools: Bash(python3 *), Bash(pip3 install *), Read, Write, Glob, Grep, WebFetch, WebSearch
---

# SEO Country Analyzer

Analyze which countries a website should target for SEO by combining traffic data, search trends, competitor analysis, and market factors into a scored, ranked recommendation.

## Scoring Formula

**Overall Score = Search Interest × 0.4 + Market Need × 0.4 + Competition Gap × 0.2** (weights configurable)

Each dimension is rated 0–10. The scoring engine in `scripts/score_countries.py` auto-classifies results: **Blue Ocean** (score ≥ 8.0 AND gap ≥ 8.0), **High Volume** (score ≥ 8.0 AND search ≥ 8.0), **High Growth** (market ≥ 8.0 AND gap ≥ 7.0), **Red Ocean** (score ≥ 6.5 AND gap ≤ 5.0), and others.

## Process

### Step 1: Analyze the target website's traffic

Goal: get total traffic + country distribution + top keywords.

Recommended data sources, in priority order:
1. **SimilarWeb public pages** (free, no auth): `WebFetch https://www.similarweb.com/website/<domain>/` — returns top-5 countries with traffic share + growth %.
2. **Ahrefs MCP** (if connected): use `site-explorer-metrics-by-country` for exact org_traffic per country.
3. **Semrush MCP** (if connected): use `overview_research` + `trends_research` → execute_report for country splits.
4. **Google Search Console** (if the site is owned): exact data for your own site only.

For a brand-new site with no traffic yet, skip Step 1 and rely on Step 2 (competitor analysis).

### Step 2: Analyze 3–5 direct competitors

Identify competitors in the same niche. For each, fetch country distribution the same way as Step 1.

Save results to `competitor_analysis.json` with this structure:
```json
{
  "date_range": "YYYY-MM to YYYY-MM",
  "country_demand": {
    "Country Name": {"total_visits": 123456, "appearances": 2, "best_rank": 5000},
    ...
  }
}
```

**Signals to look for:**
- Countries appearing in multiple competitors' top-5 = proven demand.
- Countries with high MoM growth % = emerging market — invest fast before it saturates.
- Countries NOT in any competitor top-5 despite regulatory pressure = potential Blue Ocean.

### Step 3: Research Google Trends (optional but high-value)

Search the primary keyword at https://trends.google.com/trends/explore?q=<keyword>. Capture **Interest by Region** for the top 25+ countries. **Breakout** queries are the highest-value signal — they indicate emerging demand competitors haven't yet captured.

If Claude Code doesn't have browser navigation, ask the user to paste the Interest-by-Region data as a markdown table or skip this step.

### Step 4: Evaluate market factors per country

For each candidate country, score `market_need` (0–10) using the checklist in `references/market-factors.md`. The checklist covers:
- Regulatory/compliance demand (mandatory registration creates forced search volume)
- Secondary market size (used/refurbished device trade)
- Internet & device penetration growth
- Language localization opportunity (under-served languages = competitive moat)
- Competition saturation assessment

**Key heuristic:** Countries with NEW regulations (enacted within the last 12–18 months) are the highest opportunity because users are actively searching for compliance guidance while competitors haven't localized yet.

### Step 5: Score and rank

Generate the scoring input JSON. If you have a competitor_analysis.json, run:
```bash
python3 ${CLAUDE_SKILL_DIR}/scripts/build_scoring_json.py competitor_analysis.json
```

This pre-fills `search_interest` and `competition_gap` based on competitor data. Then **manually adjust all scores**, especially `market_need` (defaults to 5.0 placeholder). Remove all `_notes` fields before scoring.

Run the scorer:
```bash
python3 ${CLAUDE_SKILL_DIR}/scripts/score_countries.py scoring_input.json
```

Outputs: ranked table to stdout, `seo_potential_chart.png` (bar chart), `country_rankings.json`.

### Step 6: Generate the strategic report

Use `references/report-template.md` as the structure. The report must contain:
1. Ranked country table with scores, classifications, and one-line strategies
2. Top-3 deep-dive analysis (blockquoted context with citations, specific SEO tactics, local-language keywords)
3. 3-phase roadmap: Quick Win (Blue Ocean) → Scale-Up (High Volume/Growth) → Global Reach
4. The chart from Step 5
5. References with numbered inline citations

## Heuristics

- **Blue Ocean = NO competitor presence + active regulatory mandate + language barrier.** All three together is the gold standard. Two of three is still strong.
- **Regulatory-driven demand:** Always research recent IMEI/device/data-protection mandates in candidate countries. New laws (12–18 months old) create the biggest opportunity windows.
- **Language moats:** Content in under-served languages (Burmese, Bengali, Amharic, Khmer, Sinhala) creates moats English-only competitors cannot easily breach.
- **Cluster strategy:** Target regional clusters (West Africa: Nigeria + Ghana; South Asia: Pakistan + Bangladesh + Sri Lanka) to maximize content reuse and shared playbooks.
- **Mobile-first:** In emerging markets, 90%+ of searches come from mobile — optimize for low-spec Android devices and slow connections.
- **Payment localization:** Stripe/PayPal will lose conversions in many target markets. Research local payment methods (Pix in Brazil, bKash in Bangladesh, Papara in Turkey, GoPay/OVO in Indonesia) before launching paid features.

## What NOT to do

- Don't enter Red Ocean countries frontally (score ≥ 6.5 AND gap ≤ 5.0). The same budget invested in a Blue Ocean target yields 5–10× better ROI.
- Don't English-language SEO into Spanish/Portuguese/Arabic markets — language moats work *against* you.
- Don't trust SimilarWeb data for sites under 100K visits/month — error margins exceed 50%.
- Don't ignore the "Others" bucket in country distributions (often 50–65%) — meaningful smaller markets hide there.

## Adapted from upstream

This skill was originally built for Manus AI's sandbox runtime, which provided a built-in SimilarWeb API at `/opt/.manus/.sandbox-runtime/data_api`. Two scripts depended on that API:
- `analyze_traffic.py` — replaced by SimilarWeb public-page WebFetch / Ahrefs MCP / Semrush MCP
- `analyze_competitors.py` — same replacement strategy

The scoring engine (`score_countries.py`) and JSON builder (`build_scoring_json.py`) are pure Python and run unchanged.
