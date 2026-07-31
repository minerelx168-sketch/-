#!/usr/bin/env python3
"""
Build the scoring input JSON from collected data files.
Usage: python3 build_scoring_json.py <competitor_analysis.json> [google_trends.md]

Reads competitor_analysis.json (from analyze_competitors.py) and optionally
a markdown file with Google Trends data, then generates a pre-filled scoring
JSON with suggested scores that the agent can review and adjust.

Output: scoring_input.json (ready for score_countries.py)
"""
import sys
import json
import math


def load_competitor_data(filepath):
    """Extract country demand from competitor analysis output."""
    with open(filepath, 'r') as f:
        data = json.load(f)
    return data.get('country_demand', {})


def normalize_score(value, max_value, scale=10):
    """Normalize a value to 0-scale range using log scaling for large ranges."""
    if max_value <= 0 or value <= 0:
        return 0
    # Log scale to handle large differences (e.g., 1M vs 10K visits)
    log_val = math.log1p(value)
    log_max = math.log1p(max_value)
    return round(min((log_val / log_max) * scale, scale), 1)


def build_scoring_json(competitor_file, trends_file=None):
    # Load competitor data
    country_demand = load_competitor_data(competitor_file)

    if not country_demand:
        print("Error: No country demand data found in competitor analysis file.")
        sys.exit(1)

    # Calculate suggested search_interest scores from competitor visits
    max_visits = max(d['total_visits'] for d in country_demand.values()) if country_demand else 1

    countries = []
    for name, data in sorted(country_demand.items(), key=lambda x: x[1]['total_visits'], reverse=True)[:15]:
        search_score = normalize_score(data['total_visits'], max_visits)
        # Suggest higher competition gap for countries appearing in fewer competitors
        total_competitors = max(d['appearances'] for d in country_demand.values())
        gap_suggestion = round(10 - (data['appearances'] / total_competitors) * 5, 1)

        countries.append({
            'name': name,
            'search_interest': search_score,
            'market_need': 5.0,  # placeholder — agent must evaluate manually
            'competition_gap': gap_suggestion,
            '_notes': {
                'total_visits': data['total_visits'],
                'appearances': data['appearances'],
                'best_rank': data['best_rank'],
                'market_need_hint': 'ADJUST: evaluate regulations, used-device market, mobile growth'
            }
        })

    output = {
        'countries': countries,
        'weights': {
            'search_interest': 0.4,
            'market_need': 0.4,
            'competition_gap': 0.2
        }
    }

    # Print summary
    print(f"\n{'='*70}")
    print(f"  SUGGESTED SCORING INPUT ({len(countries)} countries)")
    print(f"{'='*70}")
    print(f"  {'Country':<20} {'Search':<10} {'Market':<10} {'Gap':<10} {'Visits'}")
    print(f"  {'-'*65}")
    for c in countries:
        print(f"  {c['name']:<20} {c['search_interest']:<10} {c['market_need']:<10} "
              f"{c['competition_gap']:<10} {c['_notes']['total_visits']:>12,.0f}")

    print(f"\n  ⚠️  market_need scores are placeholders (5.0)")
    print(f"  ⚠️  Agent must adjust based on: regulations, used-device market, mobile growth")
    print(f"  ⚠️  Remove '_notes' fields before running score_countries.py, or they will be ignored")

    # Save
    output_file = 'scoring_input.json'
    with open(output_file, 'w') as f:
        json.dump(output, f, indent=2, ensure_ascii=False, default=str)
    print(f"\n✅ Scoring input saved to: {output_file}")
    print(f"   Next: review & adjust scores, then run score_countries.py {output_file}")
    return output


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python3 build_scoring_json.py <competitor_analysis.json> [google_trends.md]")
        print("Example: python3 build_scoring_json.py competitor_analysis.json")
        sys.exit(1)
    trends = sys.argv[2] if len(sys.argv) > 2 else None
    build_scoring_json(sys.argv[1], trends)
