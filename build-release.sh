#!/bin/bash
# -------------------------------------------------------------------------
# Branding plugin for GLPI - Release Package Script
# -------------------------------------------------------------------------
#
# This script creates a release package ready for distribution
#
# Usage: ./build-release.sh [version]
# Example: ./build-release.sh 2.0.0
# -------------------------------------------------------------------------

set -e

VERSION=${1:-2.0.1}
PLUGIN_NAME="branding"
BUILD_DIR="build"
PACKAGE_NAME="glpi-${PLUGIN_NAME}-${VERSION}"

echo "🎨 Building Branding Plugin v${VERSION}"
echo "==========================================="
echo ""

# Clean build directory
echo "📁 Cleaning build directory..."
rm -rf ${BUILD_DIR}
mkdir -p ${BUILD_DIR}/${PLUGIN_NAME}

# Copy plugin files
echo "📋 Copying plugin files..."
rsync -av --exclude='build' \
          --exclude='.git' \
          --exclude='.gitignore' \
          --exclude='node_modules' \
          --exclude='vendor' \
          --exclude='*.tar.gz' \
          --exclude='*.zip' \
          ./ ${BUILD_DIR}/${PLUGIN_NAME}/

# Install Composer dependencies (production only)
echo "📦 Installing Composer dependencies..."
cd ${BUILD_DIR}/${PLUGIN_NAME}
if [ -f composer.json ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi
cd ../..

# Create tarball
echo "📦 Creating release package..."
cd ${BUILD_DIR}
tar -czf ../${PACKAGE_NAME}.tar.gz ${PLUGIN_NAME}
cd ..

# Create checksum
echo "🔐 Generating checksum..."
sha256sum ${PACKAGE_NAME}.tar.gz > ${PACKAGE_NAME}.tar.gz.sha256

# Clean up
echo "🧹 Cleaning up..."
rm -rf ${BUILD_DIR}

# Summary
echo ""
echo "✅ Release package created!"
echo "==========================================="
echo "Package: ${PACKAGE_NAME}.tar.gz"
echo "Size: $(du -h ${PACKAGE_NAME}.tar.gz | cut -f1)"
echo "SHA256: $(cat ${PACKAGE_NAME}.tar.gz.sha256)"
echo ""
echo "📦 Ready for distribution!"
echo ""
echo "Next steps:"
echo "  1. Test the package in a clean GLPI installation"
echo "  2. Create a GitHub release"
echo "  3. Upload ${PACKAGE_NAME}.tar.gz"
echo "  4. Add release notes from CHANGELOG.md"
echo "  5. Update branding.xml with download URL"
echo "  6. Submit to GLPI Marketplace"
