#!/usr/bin/env bash
#
# AponBasket theme installer for Bagisto 2.x
#
#   bash scripts/install.sh /path/to/bagisto          # install / update theme files
#   bash scripts/install.sh /path/to/bagisto --force  # overwrite existing theme files
#
# It copies the theme into public/themes/shop/aponbasket and
# resources/themes/aponbasket. It never edits config/themes.php — the entry you
# need to paste is printed at the end (see docs/INSTALL.md).

set -euo pipefail

TARGET="${1:-}"
FORCE="${2:-}"

if [[ -z "$TARGET" ]]; then
    echo "Usage: bash scripts/install.sh /path/to/bagisto [--force]"
    exit 1
fi

if [[ ! -f "$TARGET/artisan" ]]; then
    echo "Error: $TARGET does not look like a Bagisto/Laravel root (artisan not found)."
    exit 1
fi

SOURCE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "Installing AponBasket theme"
echo "  from: $SOURCE"
echo "  into: $TARGET"
echo

copy_dir () {
    local from="$1" to="$2"

    if [[ -d "$to" && "$FORCE" != "--force" ]]; then
        echo "  ! $to already exists — skipping (use --force to overwrite)"
        return
    fi

    mkdir -p "$to"

    if command -v rsync >/dev/null 2>&1; then
        rsync -a --delete "$from"/ "$to"/
    else
        rm -rf "$to"
        mkdir -p "$to"
        cp -R "$from"/. "$to"/
    fi

    echo "  ✓ $to"
}

copy_dir "$SOURCE/public/themes/shop/aponbasket" "$TARGET/public/themes/shop/aponbasket"
copy_dir "$SOURCE/resources/themes/aponbasket" "$TARGET/resources/themes/aponbasket"

echo
echo "Next steps:"
echo
echo "  1. Add the theme to config/themes.php, inside the 'shop' => [ ... ] array:"
echo
cat <<'PHP'
        'aponbasket' => [
            'name' => 'AponBasket Organic',
            'assets_path' => 'public/themes/shop/aponbasket',
            'views_path' => 'resources/themes/aponbasket/views',

            'vite' => [
                'hot_file' => 'shop-default-vite.hot',
                'build_directory' => 'themes/shop/default/build',
                'package_assets_directory' => 'src/Resources/assets',
            ],
        ],
PHP
echo
echo "  2. Clear the cache:            php artisan optimize:clear"
echo "  3. Activate on your channel:   Admin → Settings → Channels → Theme → AponBasket Organic"
echo "  4. Hard refresh the storefront (Ctrl/Cmd + Shift + R)."
echo
echo "Docs: docs/INSTALL.md · docs/CUSTOMIZATION.md"
