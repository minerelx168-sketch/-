# Market Factors Evaluation Checklist

Use this checklist to evaluate each candidate country's market_need score (0-10).

## 1. Regulatory / Compliance Demand

Check whether the target country has government-mandated systems that create forced search demand:

| Signal | Score Impact | How to Find |
|--------|-------------|-------------|
| Mandatory registration/verification by law | +3 to +4 | Search `"[niche] registration [country]"` or `"[niche] law [country]"` |
| New regulation (enacted within 1-2 years) | +1 to +2 bonus | Search `"[niche] new regulation 2025 OR 2026"` |
| Government portal exists but is hard to use | +1 | Try the official portal — if confusing, users will seek alternatives |
| Penalties for non-compliance (blocking, fines) | +1 | Check enforcement mechanism |

**Key insight:** Countries with NEW regulations represent the highest opportunity because users actively search for compliance guidance and competitors haven't built localized content yet.

**Known IMEI registration systems** (for IMEI-related niches):

| Country | System | Impact |
|---------|--------|--------|
| Myanmar | CEIR | Very High |
| Indonesia | Bea Cukai IMEI registration | High |
| Pakistan | DIRBS (PTA) | High |
| Turkey | e-Devlet portal | High |
| Bangladesh | BTRC NEIR | High |
| India | CEIR India / ICDR | Medium |
| Sri Lanka | TRCSL registration | High |
| Ecuador | ARCOTEL registration | Medium |
| Nepal | NTA MDMS | Medium |

## 2. Secondary Market Size

Indicators of strong demand from used/second-hand transactions:

| Region | Market Characteristics |
|--------|----------------------|
| Sub-Saharan Africa | Fastest growing pre-owned market globally; Nigeria, Kenya, Ghana, South Africa |
| South Asia | Large informal markets; India, Pakistan, Bangladesh |
| Southeast Asia | Growing markets; Indonesia, Philippines, Vietnam |
| Latin America | Significant used trade; Mexico, Brazil, Argentina |

**Why it matters:** Buyers of used goods need verification (authenticity, stolen status, warranty, carrier lock).

## 3. Internet & Device Penetration

Focus on **growth markets** — countries with rapidly increasing adoption have expanding addressable markets. High-penetration mature markets (US, UK, Japan) are typically Red Ocean.

**Scoring guide:**
- 9-10: Rapid growth + large population (e.g., India, Indonesia, Nigeria)
- 7-8: Growing market with moderate population
- 5-6: Mature or small market
- 3-4: Low penetration, limited addressable audience

## 4. Language & Localization Opportunity

| Opportunity Level | Description |
|-------------------|-------------|
| Very High (Blue Ocean) | Language with <2 competing sites (e.g., Burmese, Amharic, Khmer) |
| High | Language with 2-5 competing sites (e.g., Urdu, Bengali, French-Africa) |
| Medium | Major language with moderate competition (e.g., Bahasa, Spanish, Portuguese) |
| Low | English-dominant market with many competitors |

**Strategy:** Content in under-served languages creates a natural competitive moat.

## 5. Competition Assessment

How to evaluate competition saturation for the competition_gap score:

1. Search `site:competitor.com/[country-name]` on Google — check if competitors have localized pages
2. Check SimilarWeb country rank for competitors — rank >50,000 means weak presence
3. Search the primary keyword + country name on Google — count high-DA results in top 10
4. Check for local competitors (government portals, local startups)

**Scoring guide:**
- 9-10: No competitors have localized content, no local alternatives
- 7-8: 1-2 competitors have basic pages, no local alternatives
- 5-6: Multiple competitors present but not dominant
- 3-4: Strong competitor presence with localized content
- 1-2: Market dominated by established players or government portals

## 6. Data Sources

| Data Point | Source | Access |
|-----------|--------|--------|
| Traffic by country | SimilarWeb API | `scripts/analyze_traffic.py` |
| Competitor traffic | SimilarWeb API | `scripts/analyze_competitors.py` |
| Search interest by region | Google Trends | Browser navigation |
| Regulations | BNESIM guide, Wikipedia, government sites | Web search |
| Mobile subscribers | ITU, GSMA Intelligence | Web search |
| Used device market | IDC, Counterpoint Research | Web search |
| Keyword volume | Google Keyword Planner, Ahrefs, SEMrush | Web search or API |
