#!/bin/bash
# -----------------------------------------------------
# gitpush-stcw.sh
# Helper script to push stcw-coverage-analytics to GitHub
# Repo: https://github.com/derickschaefer/stcw-analytics
# -----------------------------------------------------

# Stop on first error
set -e

# Change to the plugin directory
cd /root/plugins/stcw-coverage-analytics

# Ensure Git trusts this path (important when running as root)
git config --global --add safe.directory "$(pwd)"

# Make sure you're on the main branch
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

# Commit (skip errors if no changes)
git commit -m "$COMMIT_MSG" || echo "⚠️ No changes to commit."

# Push to GitHub
git push -u origin main

echo "✅ Successfully pushed to GitHub: derickschaefer/stcw-analytics"
