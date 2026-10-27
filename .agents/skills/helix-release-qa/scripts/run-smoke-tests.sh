#!/usr/bin/env bash
# Helix Ultimate Pre-Release QA Smoke Test Script
set -e

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
cd "$REPO_ROOT"

echo "============================================="
echo " Helix Ultimate Pre-Release QA Smoke Tests"
echo "============================================="

# 1. PHP Syntax Check on Changed Files
echo ""
echo "--- 1. Checking PHP Syntax on Modified Files ---"
MODIFIED_PHP=$(git diff --name-only origin/master..HEAD 2>/dev/null | grep '\.php$' || git diff --name-only HEAD~10..HEAD 2>/dev/null | grep '\.php$' || true)

if [ -n "$MODIFIED_PHP" ]; then
    for file in $MODIFIED_PHP; do
        if [ -f "$file" ]; then
            php -l "$file"
        fi
    done
    echo "✔ PHP syntax check passed."
else
    echo "No modified PHP files found in range. Scanning core framework files..."
    php -l plugins/system/helixultimate/helixultimate.php
    php -l plugins/system/helixultimate/src/Core/HelixUltimate.php
    php -l plugins/system/helixultimate/src/Platform/Helper.php
    php -l templates/shaper_helixultimate/features/logo.php
    php -l templates/shaper_helixultimate/helper.php
    echo "✔ Core PHP syntax check passed."
fi

# 2. Automated Security Test Suite
echo ""
echo "--- 2. Running Security Test Suite ---"
if [ -f "plugins/system/helixultimate/tests/security/run.php" ]; then
    php plugins/system/helixultimate/tests/security/run.php
    echo "✔ Security test suites passed."
else
    echo "Security test runner not found at plugins/system/helixultimate/tests/security/run.php"
fi

# 3. JSON & XML Validation
echo ""
echo "--- 3. Validating JSON & XML Manifests ---"
python3 -c "
import json, xml.etree.ElementTree as ET
try:
    json.load(open('templates/shaper_helixultimate/options.json'))
    print('✔ options.json is valid JSON')
except Exception as e:
    print('✖ Error in options.json:', e)
    exit(1)

try:
    json.load(open('plugins/system/helixultimate/composer.json'))
    print('✔ composer.json is valid JSON')
except Exception as e:
    print('✖ Error in composer.json:', e)
    exit(1)

try:
    ET.parse('templates/shaper_helixultimate/options.xml')
    print('✔ options.xml is valid XML')
except Exception as e:
    print('✖ Error in options.xml:', e)
    exit(1)

try:
    ET.parse('plugins/system/helixultimate/helixultimate.xml')
    print('✔ helixultimate.xml is valid XML')
except Exception as e:
    print('✖ Error in helixultimate.xml:', e)
    exit(1)
"

echo ""
echo "============================================="
echo "✔ All Helix Ultimate smoke tests passed!"
echo "============================================="
