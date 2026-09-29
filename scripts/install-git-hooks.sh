#!/usr/bin/env bash
# Install repo git hooks (shared VERSION file + Composer semver tags on commit).
# Run once per clone: ./scripts/install-git-hooks.sh
#
# Flow:
#   pre-commit  — bumps VERSION (patch) and stages it (source of truth in the repo)
#   post-commit — creates local tag MAJOR.MINOR.PATCH from committed VERSION
#   pre-push    — auto-releases if VERSION tag is behind the tip; pushes tags
# Manual major/minor: stage a different VERSION (e.g. 1.2.0) — kept as-is, no patch +1.
set -euo pipefail

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
hooks_path="${repo_root}/.githooks"

if [[ ! -d "$hooks_path" ]]; then
    echo "Missing hooks directory: ${hooks_path}" >&2
    exit 1
fi

chmod +x "${hooks_path}"/* 2>/dev/null || true

git -C "$repo_root" config core.hooksPath .githooks
git -C "$repo_root" config push.followTags true

echo "Git hooks path set to .githooks"
echo "push.followTags enabled (semver tags travel with git push)."
echo "VERSION format: MAJOR.MINOR.PATCH (Composer semver)."
echo "Each commit bumps patch and tags MAJOR.MINOR.PATCH."
echo "git push auto-creates the next patch if VERSION is still tagged on an older commit."
