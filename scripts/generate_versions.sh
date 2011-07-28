#!/bin/bash
set -eu

# This script expects to be run within www/scripts/ directory
# (it accesses htdocs/ and update_archives/ directories by relative paths).

TARGET_FILE=../htdocs/generated_versions.php
TMP_TARGET_FILE="$TARGET_FILE".new

SHELL_TARGET_FILE=./update_archives/generated_versions.sh
TMP_SHELL_TARGET_FILE="$SHELL_TARGET_FILE".new

# This adds a line for explicitly given PROGRAM_NAME ($1)
# and version PROGRAM_VERSION ($2).
# You can use this in special cases if a program's binary
# can't accept --version parameter and you have to call this.
version_explicit ()
{
  PROGRAM_NAME="$1"
  PROGRAM_VERSION="$2"
  shift 2

  PROGRAM_NAME=`stringoper UpperCase $PROGRAM_NAME`

  echo "  define('VERSION_$PROGRAM_NAME', '$PROGRAM_VERSION');" >> "$TMP_TARGET_FILE"

  echo "GENERATED_VERSION_$PROGRAM_NAME=$PROGRAM_VERSION" >> "$TMP_SHELL_TARGET_FILE"
}

# Call program's binary ($1) to determine program version.
# Program name is also derived from binary name.
version_call ()
{
  PROGRAM_BINARY="$1"
  shift 1

  PROGRAM_NAME=`stringoper ExtractFileName $PROGRAM_BINARY`

  PROGRAM_VERSION=`$PROGRAM_BINARY --version`

  version_explicit "$PROGRAM_NAME" "$PROGRAM_VERSION"
}


echo '<?php /* Version numbers automatically generated by generate_versions.sh */' > "$TMP_TARGET_FILE"

echo '# Version numbers automatically generated by generate_versions.sh' > "$TMP_SHELL_TARGET_FILE"

version_call castle
version_call lets_take_a_walk
version_call malfunction
version_call kambi_lines

version_call view3dscene
version_call rayhunter

version_call glViewImage
version_call glplotter
version_call bezier_curves
version_call glinformation
# glinformation_glut doesn't accept --version, but it should be considered
# to have the same version as glinformation.
version_explicit glinformation_glut `glinformation --version`

version_call gen_function

# Keep this synchronized with P.Version in ../../kambi_vrml_game_engine/fpmake.pp
version_explicit kambi_vrml_game_engine 2.5.1

version_explicit demo_models 3.0.1

echo '?>' >> "$TMP_TARGET_FILE"

echo "Diff old vs new version:"
echo '--------------------'
set +e
diff -u "$TARGET_FILE" "$TMP_TARGET_FILE"
set -e
echo '--------------------'

mv -f "$TMP_TARGET_FILE" "$TARGET_FILE"
echo "$TARGET_FILE generated successfully"

mv -f "$TMP_SHELL_TARGET_FILE" "$SHELL_TARGET_FILE"
echo "$SHELL_TARGET_FILE generated successfully"
