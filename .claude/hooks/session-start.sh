#!/usr/bin/env bash
# Phase 1 status — printed by the SessionStart hook on each new Claude session.
# Reads docs/phase1-roadmap.md and counts checkbox state per task-ID prefix so a
# fresh session sees current progress without re-reading the whole roadmap.

set -euo pipefail

ROOT="${CLAUDE_PROJECT_DIR:-$(git rev-parse --show-toplevel 2>/dev/null || pwd)}"
ROADMAP="${ROOT}/docs/phase1-roadmap.md"

[[ -f "$ROADMAP" ]] || exit 0

# Count tasks by ID prefix. A task header matches `^#+ <PREFIX>` and the next
# `**Status**` line shows `[ ]` (todo) or `[x]` (done).
progress_for() {
  local prefix="$1"
  awk -v pat="^#+ ${prefix}" '
    $0 ~ pat                          { in_task=1; total++; next }
    in_task && /\*\*Status\*\*/ {
      if ($0 ~ /\[x\]/) done++
      in_task=0
    }
    in_task && /^#+ / { in_task=0 }
    END { printf "%d/%d", done+0, total+0 }
  ' "$ROADMAP"
}

next_open_task() {
  awk '
    /^#+ (LP|AR|PR)-[A-Z]+-[0-9]+/    { current=$2 }
    current && /\*\*Status\*\*/ {
      if ($0 ~ /\[ \]/) { print current; exit }
      current=""
    }
  ' "$ROADMAP"
}

lp=$(progress_for "LP-")
ar_bd=$(progress_for "AR-BD-")
ar_eg=$(progress_for "AR-EG-")
ar_ng=$(progress_for "AR-NG-")
pr=$(progress_for "PR-")
nxt=$(next_open_task)
[[ -z "$nxt" ]] && nxt="— all Phase 1 tasks done 🎉"

cat <<EOF
========================================================
🎯 Phase 1 Status — Blue Ocean capture (BD / EG / NG)
========================================================
Landing pages:    ${lp}
Articles:
  - Bangladesh:   ${ar_bd}
  - Egypt:        ${ar_eg}
  - Nigeria:      ${ar_ng}
Payment research: ${pr}

Next suggested:   ${nxt}
Full plan:        docs/phase1-roadmap.md
========================================================
EOF
