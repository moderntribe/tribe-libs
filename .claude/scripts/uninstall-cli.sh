#!/usr/bin/env bash
set -euo pipefail

INSTALL_BIN_DIR="${TRIBE_SKILLS_BIN_DIR:-$HOME/.local/bin}"
WRAPPER_PATH="$INSTALL_BIN_DIR/tribe-skills"

if [[ -f "$WRAPPER_PATH" ]]; then
  rm "$WRAPPER_PATH"
  echo "Removed: $WRAPPER_PATH"
else
  echo "No installed wrapper found at: $WRAPPER_PATH"
fi
