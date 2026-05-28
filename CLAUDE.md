# Claude project memory — imeihub

## What this repo is
imeihub is a free + paid IMEI check service (PHP/MySQL backend, vanilla HTML/CSS/JS frontend). The static HTML demo lives on the `gh-pages` branch; full-backend development happens on `claude/*` branches.

## Active workstream — Phase 1 (Blue Ocean capture)

**Read `docs/phase1-roadmap.md` first** — it's the canonical task list for the current 8-week window.

Phase 1 targets **Bangladesh, Egypt, Nigeria** based on the analysis in `seo-analysis/seo_report.md` (Top-3 Blue Ocean countries: Bangladesh 9.6, Egypt 8.9, Nigeria 8.3). Deliverables:
- 3 country-localized landing pages (`LP-*` task IDs)
- 15 localized articles, 5 per country (`AR-*` task IDs)
- 3 payment-provider research notes (`PR-*` task IDs)

The SessionStart hook prints live Phase 1 progress at the start of every session — that's the up-to-date status, not this file.

## Where things live
- `seo-analysis/` — the strategic analysis that drives Phase 1 priorities. Don't edit unless re-running the `seo-country-analyzer` skill.
- `docs/phase1-roadmap.md` — Phase 1 task list with checkboxes. Tick boxes as work completes.
- `docs/payment-research/` — research notes from `PR-*` tasks.
- `mockup/` — design previews not yet promoted to production.
- `.claude/skills/seo-country-analyzer/` — the country-targeting skill itself.
- `.claude/hooks/session-start.sh` — prints Phase 1 status on session boot.
- `gh-pages` branch (remote-only) — the live static demo. All production HTML/CSS lives there.
- `main` branch — project documentation only.
- `claude/loving-cray-IBMXQ` (current branch) — Phase 1 development. Per standing instructions, push only to this branch.

## Workflow conventions
- **Pick a task by ID** (e.g., `AR-BD-01`) from `docs/phase1-roadmap.md` rather than re-planning. Each task has acceptance criteria — meet them, then tick the box in the roadmap in the same commit.
- **One task per commit when possible.** Commit message format: `Phase 1: <task-id> — <short description>`.
- **Don't push to `gh-pages`** without explicit user permission, even when shipping landing pages. Stage them in `mockup/` or a `phase1/` folder first, then ask.
- **Re-pull `gh-pages` before referencing production HTML** with `git fetch origin gh-pages` — it changes independently.

## Out-of-scope reminders
- Phase 2 (Turkey, Indonesia, Sri Lanka, Thailand) and Phase 3 (Myanmar, Cambodia, Vietnam, Ecuador) are deliberately deferred. Don't start work on them until Phase 1 success metrics in `docs/phase1-roadmap.md` are met.
- Pakistan and India are Red Ocean — do not pursue frontally. The roadmap's "When to Abandon" section explains the conditions to revisit.
- Don't pivot strategy without first re-running the `seo-country-analyzer` skill with updated competitor data; the analysis cost real research effort and shouldn't be discarded on a hunch.
