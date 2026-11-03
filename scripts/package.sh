#!/usr/bin/env bash
# Build dist/verbena.zip containing only the installable theme, with a
# top-level verbena/ folder. Run `npm run build` first if src/app.css changed.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$ROOT/dist"
STAGE="$(mktemp -d)"
trap 'rm -rf "$STAGE"' EXIT

mkdir -p "$STAGE/verbena" "$DIST"

FILES=(
	style.css functions.php theme.json screenshot.jpg
	front-page.php index.php page.php singular.php 404.php header.php footer.php
	archive-treatment.php taxonomy-treatment_area.php single-treatment.php
	README.md CREDITS.md
)
DIRS=( inc templates template-parts assets )

for f in "${FILES[@]}"; do
	cp "$ROOT/$f" "$STAGE/verbena/$f"
done
for d in "${DIRS[@]}"; do
	cp -R "$ROOT/$d" "$STAGE/verbena/$d"
done
find "$STAGE/verbena" -name '.DS_Store' -delete

for f in $(find "$STAGE/verbena" -name '*.php'); do
	php -l "$f" >/dev/null
done

rm -f "$DIST/verbena.zip"
(cd "$STAGE" && zip -qr -X "$DIST/verbena.zip" verbena)
echo "Wrote $DIST/verbena.zip ($(du -h "$DIST/verbena.zip" | cut -f1))"
