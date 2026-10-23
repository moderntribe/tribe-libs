#!/usr/bin/env bash
set -euo pipefail

# Install a global wrapper so `tribe-skills` works anywhere.
# The wrapper points to this repository as the source.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
CLI_SOURCE="$REPO_ROOT/cli/tribe-skills"

if [[ ! -f "$CLI_SOURCE" ]]; then
  echo "Error: CLI not found at $CLI_SOURCE" >&2
  exit 1
fi

INSTALL_BIN_DIR="${TRIBE_SKILLS_BIN_DIR:-$HOME/.local/bin}"
WRAPPER_PATH="$INSTALL_BIN_DIR/tribe-skills"

mkdir -p "$INSTALL_BIN_DIR"

cat > "$WRAPPER_PATH" << EOF
#!/usr/bin/env bash
set -euo pipefail
export TRIBE_SKILLS_REPO_ROOT="$REPO_ROOT"
exec "$CLI_SOURCE" "\$@"
EOF

chmod +x "$WRAPPER_PATH"

echo "Installed: $WRAPPER_PATH"
echo ""
echo "Run from any directory:"
echo "  tribe-skills list"
echo ""
echo "If command is not found, add this to your shell profile:"
echo "  export PATH=\"$INSTALL_BIN_DIR:\$PATH\""
