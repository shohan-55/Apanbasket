#!/usr/bin/env bash
# Safe fixture-only tests for the deployment helper; no Bagisto database is used.
set -euo pipefail
ROOT=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd)
SCRIPT="$ROOT/scripts/deploy-theme.sh"
TMP=$(mktemp -d "${TMPDIR:-/tmp}/aponbasket-deploy-test-XXXXXX")
trap 'rm -rf -- "$TMP"' EXIT
TARGET="$TMP/bagisto"
BACKUPS="$TMP/private-backups"
mkdir -p "$TARGET/bootstrap" "$TARGET/vendor" \
    "$TARGET/packages/Webkul/Theme/src/Sections" \
    "$TARGET/packages/Webkul/Theme/src/Repositories" \
    "$TARGET/packages/Webkul/Core/src" \
    "$TARGET/app/AponBasket/Theme" \
    "$TARGET/resources/themes/aponbasket/views/components/layouts" \
    "$TARGET/public/themes/shop/aponbasket" \
    "$BACKUPS"
printf 'artisan placeholder\n' > "$TARGET/artisan"
printf 'original providers\n' > "$TARGET/bootstrap/providers.php"
printf 'autoload placeholder\n' > "$TARGET/vendor/autoload.php"
printf 'section API\n' > "$TARGET/packages/Webkul/Theme/src/Sections/SectionType.php"
printf 'section schema\n' > "$TARGET/packages/Webkul/Theme/src/SectionSchema.php"
printf 'function getRenderable(\nfunction findAllOfType(\n' > "$TARGET/packages/Webkul/Theme/src/Repositories/SectionRepository.php"
printf "const BAGISTO_VERSION = '2.5.0';\n" > "$TARGET/packages/Webkul/Core/src/Core.php"
printf 'merchant original\n' > "$TARGET/app/AponBasket/Theme/Defaults.php"
printf 'preserve this additional file\n' > "$TARGET/public/themes/shop/aponbasket/extra.txt"
printf 'database is never touched\n' > "$TARGET/database-marker.txt"
printf 'old custom layout\n' > "$TARGET/resources/themes/aponbasket/views/components/layouts/index.blade.php"

bash "$SCRIPT" --help > "$TMP/help.log"
if bash "$SCRIPT" --target "$TMP/not-a-project" > /dev/null 2>&1; then echo 'Invalid project was accepted'; exit 1; fi

# Default mode must report files without touching project, database or backups.
bash "$SCRIPT" --target "$TARGET" > "$TMP/check.log" 2>&1
grep -q 'Read-only check complete' "$TMP/check.log"
grep -q 'OVERWRITE app/AponBasket/Theme/Defaults.php' "$TMP/check.log"
grep -q '^merchant original$' "$TARGET/app/AponBasket/Theme/Defaults.php"
[[ -z "$(find "$BACKUPS" -mindepth 1 -maxdepth 1 -print -quit)" ]]

# An uncommitted checkout is not deployed unless the operator deliberately opts in.
if [[ -n "$(git -C "$ROOT" status --porcelain --untracked-files=normal)" ]]; then
    if bash "$SCRIPT" --target "$TARGET" --apply --allow-custom-layout --backup-root "$BACKUPS" > "$TMP/dirty.log" 2>&1; then echo 'Dirty source was accepted without explicit review'; exit 1; fi
    grep -q 'Refusing to apply a dirty checkout' "$TMP/dirty.log"
fi

# Apply is blocked for an existing layout, missing backup, or an unsafe backup location.
if bash "$SCRIPT" --target "$TARGET" --apply --allow-dirty --backup-root "$BACKUPS" > /dev/null 2>&1; then echo 'Legacy override was accepted without explicit review'; exit 1; fi
rm "$TARGET/resources/themes/aponbasket/views/components/layouts/index.blade.php"
if bash "$SCRIPT" --target "$TARGET" --apply --allow-dirty > /dev/null 2>&1; then echo 'Apply without a backup root was accepted'; exit 1; fi
if bash "$SCRIPT" --target "$TARGET" --apply --allow-dirty --backup-root "$TARGET/public" > /dev/null 2>&1; then echo 'Backup inside project was accepted'; exit 1; fi
grep -q '^merchant original$' "$TARGET/app/AponBasket/Theme/Defaults.php"

bash "$SCRIPT" --target "$TARGET" --apply --allow-dirty --backup-root "$BACKUPS" > "$TMP/apply.log" 2>&1
backup=$(find "$BACKUPS" -mindepth 1 -maxdepth 1 -type d -print -quit)
[[ -n "$backup" ]]
[[ "$(stat -c '%a' "$backup")" == '700' ]]
grep -q '^merchant original$' "$backup/app/AponBasket/Theme/Defaults.php"
grep -q '^original providers$' "$backup/bootstrap/providers.php"
grep -q '^PRESENT app/AponBasket$' "$backup/manifest.txt"
cmp "$ROOT/app/AponBasket/Theme/Defaults.php" "$TARGET/app/AponBasket/Theme/Defaults.php"
cmp "$ROOT/public/themes/shop/aponbasket/css/aponbasket.css" "$TARGET/public/themes/shop/aponbasket/css/aponbasket.css"
grep -q '^preserve this additional file$' "$TARGET/public/themes/shop/aponbasket/extra.txt"
grep -q '^database is never touched$' "$TARGET/database-marker.txt"
grep -q '^original providers$' "$TARGET/bootstrap/providers.php"

# Symlinks in the theme trees cannot redirect or overwrite unrelated files.
ln -s "$TARGET/database-marker.txt" "$TARGET/public/themes/shop/aponbasket/do-not-follow"
if bash "$SCRIPT" --target "$TARGET" --apply --allow-dirty --backup-root "$BACKUPS" > /dev/null 2>&1; then echo 'Target symlink was accepted'; exit 1; fi
grep -q '^database is never touched$' "$TARGET/database-marker.txt"
rm "$TARGET/public/themes/shop/aponbasket/do-not-follow"

# A reviewed custom layout can be explicitly kept and is captured in the backup.
printf 'reviewed custom layout\n' > "$TARGET/resources/themes/aponbasket/views/components/layouts/index.blade.php"
bash "$SCRIPT" --target "$TARGET" --apply --allow-dirty --allow-custom-layout --backup-root "$BACKUPS" > "$TMP/custom.log" 2>&1
grep -q '^reviewed custom layout$' "$TARGET/resources/themes/aponbasket/views/components/layouts/index.blade.php"
if grep -q '^reviewed custom layout$' "$backup/resources/themes/aponbasket/views/components/layouts/index.blade.php" 2>/dev/null; then echo 'Wrong backup selected'; exit 1; fi
[[ "$(grep -rl '^reviewed custom layout$' "$BACKUPS" | wc -l)" -ge 1 ]]

printf 'ALL DEPLOYMENT SAFETY TESTS PASSED\n'
