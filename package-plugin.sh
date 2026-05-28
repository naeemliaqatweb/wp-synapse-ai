#!/bin/bash

# WP Synapse AI - Dual Packaging Script (Free & Premium)
# This script builds the React admin app and creates clean ZIP packages for distribution.

SLUG="wp-synapse-ai"
VERSION="1.0.1"
PREMIUM_ZIP="${SLUG}-premium-v${VERSION}.zip"
FREE_ZIP="${SLUG}-free-v${VERSION}.zip"
TEMP_DIR="tmp_package"

echo "🚀 Starting packaging process for ${SLUG} v${VERSION}..."

# 1. Clean up previous builds
rm -f ${PREMIUM_ZIP}
rm -f ${FREE_ZIP}
rm -rf ${TEMP_DIR}

# 2. Ensure React is built
echo "📦 Building React application..."
cd admin
if ! npm run build; then
    echo "❌ React build failed! Aborting."
    exit 1
fi
cd ..

# Helper to copy shared core assets
copy_core_assets() {
    local dest=$1
    mkdir -p "$dest"
    cp -r includes "$dest/"
    cp -r freemius "$dest/"
    cp -r public "$dest/"
    mkdir -p "$dest/admin"
    cp -r admin/dist "$dest/admin/"
    
    cp wp-synapse-ai.php "$dest/"
    cp readme.txt "$dest/"
    cp README.md "$dest/"
    cp PRODUCT_DETAILS.md "$dest/"
    cp PACKAGING_GUIDE.md "$dest/"
    cp LICENSE.txt "$dest/"
}

# 3. Build the PREMIUM package (with premium/ folder)
echo "📁 Packaging PREMIUM version..."
PREMIUM_DEST="${TEMP_DIR}/premium/${SLUG}"
copy_core_assets "$PREMIUM_DEST"
cp -r premium "$PREMIUM_DEST/"

cd "${TEMP_DIR}/premium"
zip -r "../../${PREMIUM_ZIP}" "${SLUG}" -x "*.DS_Store*" "*node_modules*" "*.git*"
cd ../..

# 4. Build the FREE package (excludes premium/ folder)
echo "📁 Packaging FREE version (WordPress.org compliant)..."
FREE_DEST="${TEMP_DIR}/free/${SLUG}"
copy_core_assets "$FREE_DEST"

cd "${TEMP_DIR}/free"
zip -r "../../${FREE_ZIP}" "${SLUG}" -x "*.DS_Store*" "*node_modules*" "*.git*"
cd ../..

# 5. Cleanup
echo "🧹 Cleaning up temporary files..."
rm -rf ${TEMP_DIR}

echo "=================================================="
echo "✅ Packaging complete!"
echo "⭐️ Freemius (Premium): ${PREMIUM_ZIP}"
echo "🌐 WordPress.org (Free):  ${FREE_ZIP}"
echo "=================================================="
