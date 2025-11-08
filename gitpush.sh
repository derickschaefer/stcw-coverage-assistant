#!/bin/bash
# -----------------------------------------------------
# gitpush.sh
# Helper script to push stcw-coverage-assistant to GitHub
# Repo: https://github.com/derickschaefer/stcw-coverage-assistant
# -----------------------------------------------------

# Stop on first error
set -e

# Define plugin directory
PLUGIN_DIR="/root/plugins/stcw-coverage-assistant"

# Move to plugin directory
cd "$PLUGIN_DIR"

# Ensure Git trusts this path (important when running as root)
git config --global --add safe.directory "$PLUGIN_DIR"

# Ensure we're on main branch (create or rename if needed)
git branch -M main

# Add all changes
git add .

# Prompt for commit message
echo "Enter commit message:"
read COMMIT_MSG

# Default commit message if none entered
if [ -z "$COMMIT_MSG" ]; then
  COMMIT_MSG="Auto-commit: $(date +'%Y-%m-%d %H:%M:%S')"
fi

# Commit (ignore 'nothing to commit' case)
git commit -m "$COMMIT_MSG" || echo "⚠️ No changes to commit."

# Make sure remote is correct (update if you renamed the repo)
EXPECTED_REMOTE="https://github.com/derickschaefer/stcw-coverage-assistant.git"
CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "")

if [ "$CURRENT_REMOTE" != "$EXPECTED_REMOTE" ]; then
  echo "🔧 Updating remote origin to $EXPECTED_REMOTE"
  git remote set-url origin "$EXPECTED_REMOTE"
fi

# Push to GitHub
echo "🚀 Pushing to GitHub..."
git push -u origin main

echo "✅ Push complete!"
