#!/usr/bin/env bash
#
# Compile the theme's gettext catalogues (languages/*.po → *.mo).
#
# Run this after editing any .po file (e.g. in Poedit) so the runtime
# translation loader in inc/i18n.php picks up the changes.
#
#   ./bin/build-i18n.sh
#
# Requires `msgfmt` from GNU gettext:
#   macOS:  brew install gettext
#   Debian: apt-get install gettext
#
set -euo pipefail

# Resolve the theme root (parent of this script's bin/ dir), independent of cwd.
THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LANG_DIR="$THEME_DIR/languages"

if ! command -v msgfmt >/dev/null 2>&1; then
	echo "error: msgfmt not found. Install GNU gettext (e.g. 'brew install gettext')." >&2
	exit 1
fi

if [ ! -d "$LANG_DIR" ]; then
	echo "error: $LANG_DIR does not exist." >&2
	exit 1
fi

shopt -s nullglob
po_files=("$LANG_DIR"/*.po)
if [ ${#po_files[@]} -eq 0 ]; then
	echo "No .po files found in $LANG_DIR — nothing to compile."
	exit 0
fi

for po in "${po_files[@]}"; do
	mo="${po%.po}.mo"
	printf '%s → %s\n' "$(basename "$po")" "$(basename "$mo")"
	msgfmt --check --statistics -o "$mo" "$po"
done

echo "Done."
