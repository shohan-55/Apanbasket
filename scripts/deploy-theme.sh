#!/usr/bin/env bash
# Copy only the AponBasket theme from this checkout into an existing Bagisto project.
# Default mode is read-only. Database migrations, provider registration and caches are manual.
set -euo pipefail

usage() {
    cat <<'USAGE'
Usage: bash scripts/deploy-theme.sh --target BAGISTO_ROOT [options]

  --target PATH               Existing, installed Bagisto project root (required)
  --apply                     Copy files after creating a backup (default: report only)
  --backup-root PATH          Existing private directory outside the project (required with --apply)
  --allow-dirty               Explicitly allow applying an uncommitted/modified checkout
  --allow-custom-layout       Explicitly allow an existing AponBasket layout override
  --help                      Show this help

This does not run git pull, edit bootstrap/providers.php, migrate a database,
activate a theme, delete files, or change Bagisto's product/order/media data.
Always test on isolated staging and back up the database and storage separately.
USAGE
}

fail() { printf 'ERROR: %s\n' "$*" >&2; exit 1; }
warn() { printf 'WARNING: %s\n' "$*" >&2; }

TARGET=''
BACKUP_ROOT=''
APPLY=0
ALLOW_DIRTY=0
ALLOW_CUSTOM_LAYOUT=0
while (($#)); do
    case "$1" in
        --target|--backup-root)
            (($# >= 2)) || fail "$1 needs a path."
            if [[ "$1" == '--target' ]]; then TARGET=$2; else BACKUP_ROOT=$2; fi
            shift 2
            ;;
        --apply) APPLY=1; shift ;;
        --allow-dirty) ALLOW_DIRTY=1; shift ;;
        --allow-custom-layout) ALLOW_CUSTOM_LAYOUT=1; shift ;;
        --help|-h) usage; exit 0 ;;
        *) fail "Unknown option: $1" ;;
    esac
done

[[ -n "$TARGET" && -d "$TARGET" ]] || fail 'Pass an existing Bagisto root using --target PATH.'
SOURCE=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)
TARGET=$(cd -P -- "$TARGET" && pwd)

# Keep the theme checkout and the running Bagisto project separate.
case "$TARGET/" in "$SOURCE/"*) fail 'The target cannot be the checkout or a child of it.' ;; esac
case "$SOURCE/" in "$TARGET/"*) fail 'Keep the checkout outside the Bagisto project.' ;; esac

for required in artisan bootstrap/providers.php vendor/autoload.php \
    packages/Webkul/Theme/src/Sections/SectionType.php \
    packages/Webkul/Theme/src/SectionSchema.php \
    packages/Webkul/Theme/src/Repositories/SectionRepository.php; do
    [[ -f "$TARGET/$required" ]] || fail "Not a compatible installed Bagisto project; missing $required."
done
for method in getRenderable findAllOfType; do
    grep -Eq "function[[:space:]]+$method[[:space:]]*\\(" "$TARGET/packages/Webkul/Theme/src/Repositories/SectionRepository.php" \
        || fail "Bagisto's section repository does not provide $method; compatibility needs review."
done
[[ ! -L "$TARGET/bootstrap" && ! -L "$TARGET/bootstrap/providers.php" ]] \
    || fail 'The provider path is a symlink; review the backup setup manually.'

paths=('app/AponBasket' 'resources/themes/aponbasket' 'public/themes/shop/aponbasket')
for rel in "${paths[@]}"; do
    [[ -d "$SOURCE/$rel" ]] || fail "Theme source is incomplete: $rel"
    [[ -z "$(find "$SOURCE/$rel" -type l -print -quit)" ]] || fail "Source contains a symlink: $rel"
    if [[ -e "$TARGET/$rel" && ! -d "$TARGET/$rel" ]]; then
        fail "Target path is not a directory: $rel"
    fi
    if [[ -d "$TARGET/$rel" && -n "$(find "$TARGET/$rel" -type l -print -quit)" ]]; then
        fail "Target contains a symlink in $rel; review the custom setup manually."
    fi
done
# Existing symlinks in intermediate directories could otherwise redirect writes elsewhere.
for rel in app app/AponBasket resources resources/themes resources/themes/aponbasket \
    public public/themes public/themes/shop public/themes/shop/aponbasket; do
    [[ ! -L "$TARGET/$rel" ]] || fail "Target path is a symlink: $rel; review it manually."
done

DIRTY=0
REVISION='not-a-git-checkout'
if git -C "$SOURCE" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    REVISION=$(git -C "$SOURCE" rev-parse --short HEAD)
    if [[ -n "$(git -C "$SOURCE" status --porcelain --untracked-files=normal)" ]]; then
        DIRTY=1
        warn 'The theme checkout has local/uncommitted changes. Review git status before deploying.'
    fi
else
    warn 'Source is not a Git checkout; verify the source contents before deploying.'
fi

LEGACY="$TARGET/resources/themes/aponbasket/views/components/layouts/index.blade.php"
if [[ -e "$LEGACY" ]]; then
    warn 'An existing AponBasket layout override may conflict with this version.'
    warn 'Back it up and review it. Remove the old v1.1 override from the active path before applying.'
fi

printf 'Source: %s\nTarget: %s\nSource revision: %s\n' "$SOURCE" "$TARGET" "$REVISION"
CORE="$TARGET/packages/Webkul/Core/src/Core.php"
if [[ -f "$CORE" ]]; then
    printf 'Detected Bagisto version declaration: '
    grep -m 1 'BAGISTO_VERSION[[:space:]]*=' "$CORE" || printf 'unknown\n'
fi
printf '\nFiles to add or overwrite (unchanged files are omitted):\n'
changes=0
for rel in "${paths[@]}"; do
    # Check source directories too; cp must not replace a target file with a directory.
    while IFS= read -r -d '' srcdir; do
        suffix=${srcdir#"$SOURCE/$rel"}
        dest="$TARGET/$rel$suffix"
        [[ ! -e "$dest" || -d "$dest" ]] || fail "Target file conflicts with a source directory: $rel$suffix"
    done < <(find "$SOURCE/$rel" -type d -print0)

    while IFS= read -r -d '' file; do
        suffix=${file#"$SOURCE/$rel/"}
        dest="$TARGET/$rel/$suffix"
        if [[ -d "$dest" ]]; then
            fail "Target directory conflicts with a source file: $rel/$suffix"
        elif [[ -e "$dest" ]]; then
            if ! cmp -s -- "$file" "$dest"; then
                printf '  OVERWRITE %s/%s\n' "$rel" "$suffix"
                changes=$((changes + 1))
            fi
        else
            printf '  ADD       %s/%s\n' "$rel" "$suffix"
            changes=$((changes + 1))
        fi
    done < <(find "$SOURCE/$rel" -type f -print0)
done
printf 'Total files to add/overwrite: %s\n' "$changes"

if (( ! APPLY )); then
    printf '\nRead-only check complete. No files or database records were changed.\n'
    exit 0
fi

(( ! DIRTY || ALLOW_DIRTY )) || fail 'Refusing to apply a dirty checkout; review it or explicitly pass --allow-dirty.'
[[ ! -e "$LEGACY" || "$ALLOW_CUSTOM_LAYOUT" == 1 ]] || fail 'Review the existing layout override before applying (see README-BN.md).'
[[ -n "$BACKUP_ROOT" && -d "$BACKUP_ROOT" ]] || fail 'With --apply, pass an existing private --backup-root directory.'
BACKUP_ROOT=$(cd -P -- "$BACKUP_ROOT" && pwd)
case "$BACKUP_ROOT/" in "$TARGET/"*) fail 'Backup root must be outside the Bagisto project.' ;; esac
case "$BACKUP_ROOT/" in "$SOURCE/"*) fail 'Backup root must be outside the theme checkout.' ;; esac
[[ -w "$BACKUP_ROOT" ]] || fail 'Backup root is not writable.'

# A private, unique folder is prepared fully before any project file is overwritten.
old_umask=$(umask)
umask 077
BACKUP=$(mktemp -d "$BACKUP_ROOT/aponbasket-$(date +%Y%m%d-%H%M%S)-XXXXXX")
for rel in "${paths[@]}"; do
    if [[ -d "$TARGET/$rel" ]]; then
        mkdir -p -- "$BACKUP/$(dirname -- "$rel")"
        cp -a -- "$TARGET/$rel" "$BACKUP/$rel"
        printf 'PRESENT %s\n' "$rel" >> "$BACKUP/manifest.txt"
    else
        printf 'MISSING %s\n' "$rel" >> "$BACKUP/manifest.txt"
    fi
done
mkdir -p -- "$BACKUP/bootstrap"
cp -a -- "$TARGET/bootstrap/providers.php" "$BACKUP/bootstrap/providers.php"
printf 'SOURCE_REVISION %s\nTARGET %s\n' "$REVISION" "$TARGET" >> "$BACKUP/manifest.txt"
umask "$old_umask"
printf '\nBackup created: %s\n' "$BACKUP"

trap 'status=$?; if (( status != 0 )); then printf "\nCopy may be incomplete. Review the backup at: %s\n" "$BACKUP" >&2; fi' EXIT
for rel in "${paths[@]}"; do
    while IFS= read -r -d '' file; do
        suffix=${file#"$SOURCE/$rel/"}
        dest="$TARGET/$rel/$suffix"
        if [[ ! -e "$dest" ]] || ! cmp -s -- "$file" "$dest"; then
            mkdir -p -- "$(dirname -- "$dest")"
            cp -a -- "$file" "$dest"
        fi
    done < <(find "$SOURCE/$rel" -type f -print0)
done
trap - EXIT
printf 'Theme files copied. No database, config, provider, cache, storage or activation changes were made.\n'
printf 'Follow README-BN.md for provider registration, migration, installer and cache steps.\n'
