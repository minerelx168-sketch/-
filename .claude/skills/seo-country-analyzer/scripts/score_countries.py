#!/usr/bin/env python3
"""
Score and rank countries by SEO potential based on collected data.
Usage: python3 score_countries.py <data_file.json>

Input JSON format:
{
  "countries": [
    {
      "name": "Country Name",
      "search_interest": 8.5,    // 0-10: from Google Trends + competitor traffic
      "market_need": 9.0,        // 0-10: regulations, used market, mobile growth
      "competition_gap": 7.0     // 0-10: higher = less saturated, easier to enter
    }
  ],
  "weights": {                   // optional, defaults below
    "search_interest": 0.4,
    "market_need": 0.4,
    "competition_gap": 0.2
  }
}

Output: Ranked table, visualization (PNG chart), and rankings JSON.
"""
import sys
import json


def classify_country(score, search, market, gap):
    """Assign a classification label based on score profile."""
    if score >= 8.0 and gap >= 8.0:
        return "Blue Ocean"
    if score >= 8.0 and search >= 8.0:
        return "High Volume"
    if market >= 8.0 and gap >= 7.0:
        return "High Growth"
    if score >= 6.5 and gap >= 8.0:
        return "Niche Opportunity"
    if score >= 6.5 and gap <= 5.0:
        return "Red Ocean"
    if score >= 5.0:
        return "Secondary"
    return "Monitor"


def score_countries(data_file):
    with open(data_file, 'r') as f:
        data = json.load(f)

    countries = data['countries']
    weights = data.get('weights', {
        'search_interest': 0.4,
        'market_need': 0.4,
        'competition_gap': 0.2
    })

    # Calculate scores
    results = []
    for c in countries:
        score = (
            c['search_interest'] * weights['search_interest'] +
            c['market_need'] * weights['market_need'] +
            c['competition_gap'] * weights['competition_gap']
        )
        classification = classify_country(
            round(score, 2), c['search_interest'], c['market_need'], c['competition_gap']
        )
        results.append({
            'name': c['name'],
            'search_interest': c['search_interest'],
            'market_need': c['market_need'],
            'competition_gap': c['competition_gap'],
            'overall_score': round(score, 1),
            'classification': classification
        })

    # Sort by overall score
    results.sort(key=lambda x: x['overall_score'], reverse=True)

    # Print ranked table
    print(f"\n{'='*85}")
    print(f"  SEO POTENTIAL RANKING")
    print(f"{'='*85}")
    print(f"  {'Rank':<6} {'Country':<18} {'Search':<9} {'Market':<9} {'Gap':<9} {'SCORE':<8} {'Classification'}")
    print(f"  {'-'*80}")
    for i, r in enumerate(results, 1):
        print(f"  {i:<6} {r['name']:<18} {r['search_interest']:<9} {r['market_need']:<9} "
              f"{r['competition_gap']:<9} {r['overall_score']:<8} {r['classification']}")

    print(f"\n  Weights: Search Interest={weights['search_interest']:.0%}, "
          f"Market Need={weights['market_need']:.0%}, "
          f"Competition Gap={weights['competition_gap']:.0%}")

    # Generate visualization
    try:
        import matplotlib.pyplot as plt
        import numpy as np
        import seaborn as sns

        sns.set_theme(style="whitegrid")
        plt.rcParams['font.family'] = 'DejaVu Sans'

        fig, ax = plt.subplots(figsize=(10, 6))
        x = np.arange(len(results))
        width = 0.25

        names = [r['name'] for r in results]
        si = [r['search_interest'] for r in results]
        mn = [r['market_need'] for r in results]
        cg = [r['competition_gap'] for r in results]
        overall = [r['overall_score'] for r in results]

        ax.bar(x - width, si, width, label='Search Interest', color='#4e79a7')
        ax.bar(x, mn, width, label='Market Need', color='#f28e2b')
        ax.bar(x + width, cg, width, label='Competition Gap', color='#e15759')

        ax.set_ylabel('Score (0-10)', fontsize=12, fontweight='bold')
        ax.set_title('SEO Potential Analysis by Country', fontsize=14, fontweight='bold')
        ax.set_xticks(x)
        ax.set_xticklabels(names, fontsize=10, fontweight='bold')
        ax.set_ylim(0, 11)
        ax.legend(loc='upper right')

        ax2 = ax.twinx()
        ax2.plot(x, overall, color='#59a14f', marker='o', linewidth=2.5, label='Overall Score')
        ax2.set_ylabel('Overall Score', color='#59a14f', fontsize=12, fontweight='bold')
        ax2.set_ylim(0, 11)
        for i, txt in enumerate(overall):
            ax2.annotate(f'{txt}', (x[i], overall[i]), textcoords="offset points",
                        xytext=(0, 10), ha='center', fontweight='bold', color='#59a14f')

        plt.tight_layout()
        chart_file = 'seo_potential_chart.png'
        plt.savefig(chart_file, dpi=300)
        plt.close()
        print(f"\n✅ Chart saved to: {chart_file}")
    except ImportError:
        print("\n⚠️  matplotlib not available, skipping chart generation")

    # Save ranked results
    output_file = 'country_rankings.json'
    with open(output_file, 'w') as f:
        json.dump(results, f, indent=2, ensure_ascii=False)
    print(f"✅ Rankings saved to: {output_file}")

    return results


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python3 score_countries.py <data_file.json>")
        print("\nExample data_file.json:")
        example = {
            "countries": [
                {"name": "Myanmar", "search_interest": 10.0, "market_need": 9.5, "competition_gap": 8.5},
                {"name": "Indonesia", "search_interest": 8.5, "market_need": 9.0, "competition_gap": 6.0},
            ],
            "weights": {"search_interest": 0.4, "market_need": 0.4, "competition_gap": 0.2}
        }
        print(json.dumps(example, indent=2))
        sys.exit(1)
    score_countries(sys.argv[1])
