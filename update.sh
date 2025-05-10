#!/bin/bash

set -e

if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <child_project_path> <last_version_tag>"
    exit 1
fi

CHILD_PATH="$1"
LAST_TAG="$2"
ROOT_PATH="$(pwd)"

if [ ! -d "$CHILD_PATH" ]; then
    echo "Child project path does not exist: $CHILD_PATH"
    exit 1
fi

# 1. Get changed files
CHANGED=$(git diff --name-status "$LAST_TAG" HEAD)

# 2. Prepare lists
ADDED_MODIFIED=()
DELETED=()

while IFS= read -r line; do
    STATUS=$(echo "$line" | awk '{print $1}')
    FILE=$(echo "$line" | awk '{print $2}')
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    if [[ "$STATUS" == "A" || "$STATUS" == "M" ]]; then
        ADDED_MODIFIED+=("$FILE")
    elif [[ "$STATUS" == "D" ]]; then
        DELETED+=("$FILE")
    fi
done <<< "$CHANGED"

# 3. Copy new/modified files
SAFE_FILES=()
for FILE in "${ADDED_MODIFIED[@]}"; do
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    DEST="$CHILD_PATH/$FILE"
    # Check if the file is new in the child repo
    if [ ! -f "$DEST" ]; then
        SAFE_FILES+=("$FILE")
    fi
    mkdir -p "$(dirname "$DEST")"
    cp "$FILE" "$DEST"
    echo "Copied $FILE to $DEST"
done

# 4. Delete removed files
for FILE in "${DELETED[@]}"; do
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    TARGET="$CHILD_PATH/$FILE"
    if [ -f "$TARGET" ]; then
        rm "$TARGET"
        echo "Deleted $TARGET"
    fi
done

# 5. Compare diffs and report
# Get the latest tag for the 'to' version
TO_TAG=$(git describe --tags --abbrev=0)
REPORT_NAME="update_report_${LAST_TAG}_to_${TO_TAG}.txt"
REPORT="$CHILD_PATH/$REPORT_NAME"
> "$REPORT"

for FILE in "${ADDED_MODIFIED[@]}"; do
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    # Generate unified diff in main repo
    git diff "$LAST_TAG" -- "$FILE" | grep -v '^index' | sed 's/[[:space:]]*$//' > /tmp/main_diff
    # Generate unified diff in child repo
    pushd "$CHILD_PATH" > /dev/null
    git diff -- "$FILE" | grep -v '^index' | sed 's/[[:space:]]*$//' > /tmp/child_diff
    popd > /dev/null
    # Compare the diffs byte-for-byte
    if ! diff -q /tmp/main_diff /tmp/child_diff > /dev/null; then
        echo "$FILE" >> "$REPORT"
    fi
    rm -f /tmp/main_diff /tmp/child_diff
done

# Compare deleted files
for FILE in "${DELETED[@]}"; do
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    # Generate unified diff in main repo for deleted file
    git diff "$LAST_TAG" -- "$FILE" | grep -v '^index' | sed 's/[[:space:]]*$//' > /tmp/main_diff
    # Generate unified diff in child repo for deleted file
    pushd "$CHILD_PATH" > /dev/null
    git diff -- "$FILE" | grep -v '^index' | sed 's/[[:space:]]*$//' > /tmp/child_diff
    popd > /dev/null
    # Compare the diffs byte-for-byte
    if ! diff -q /tmp/main_diff /tmp/child_diff > /dev/null; then
        echo "$FILE" >> "$REPORT"
    fi
    rm -f /tmp/main_diff /tmp/child_diff
done

if [ -s "$REPORT" ]; then
    echo "Update report created: $REPORT"
    cat "$REPORT"
else
    echo "All files updated and verified. No mismatches found."
    rm "$REPORT"
fi

# Update README.md in the child project
README="$CHILD_PATH/README.md"
VERSION_LINE="This is based on the core of Athenia version number $TO_TAG"
if [ -f "$README" ]; then
    if grep -q "This is based on the core of Athenia version number" "$README"; then
        sed -i '' "s|This is based on the core of Athenia version number.*|$VERSION_LINE|" "$README"
    else
        echo "$VERSION_LINE" >> "$README"
    fi
else
    echo "$VERSION_LINE" > "$README"
fi

# Git add all updated files not in the report
pushd "$CHILD_PATH" > /dev/null
REPORTED=()
if [ -f "$REPORT_NAME" ]; then
    while IFS= read -r line; do
        REPORTED+=("$line")
    done < "$REPORT_NAME"
fi

should_add() {
    local file="$1"
    for reported in "${REPORTED[@]}"; do
        if [[ "$file" == "$reported" ]]; then
            return 1
        fi
    done
    return 0
}

for FILE in "${ADDED_MODIFIED[@]}"; do
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    should_add "$FILE" && git add "$FILE"
done

for FILE in "${DELETED[@]}"; do
    if [[ "$FILE" == app/Providers/* || "$FILE" == UPGRADE*.md || "$FILE" == "update.sh" ]]; then
        continue
    fi
    if [ ! -f "$FILE" ]; then
        should_add "$FILE" && git rm --cached "$FILE" 2>/dev/null || true
    fi
done
popd > /dev/null

# Stage safe files for commit
echo "Staging safe files for commit..."
cd "$CHILD_PATH"
for FILE in "${SAFE_FILES[@]}"; do
    git add "$FILE"
done
git add update-report.txt
git add README.md
git add $(grep -E '^(Added|Updated|Removed):' update-report.txt | cut -d' ' -f2) 