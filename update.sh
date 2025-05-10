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
    if [[ "$FILE" == app/Providers/* ]]; then
        continue
    fi
    if [[ "$STATUS" == "A" || "$STATUS" == "M" ]]; then
        ADDED_MODIFIED+=("$FILE")
    elif [[ "$STATUS" == "D" ]]; then
        DELETED+=("$FILE")
    fi
done <<< "$CHANGED"

# 3. Copy new/modified files
for FILE in "${ADDED_MODIFIED[@]}"; do
    if [[ "$FILE" == app/Providers/* ]]; then
        continue
    fi
    DEST="$CHILD_PATH/$FILE"
    mkdir -p "$(dirname "$DEST")"
    cp "$FILE" "$DEST"
    echo "Copied $FILE to $DEST"
done

# 4. Delete removed files
for FILE in "${DELETED[@]}"; do
    if [[ "$FILE" == app/Providers/* ]]; then
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
    if [[ "$FILE" == app/Providers/* ]]; then
        continue
    fi
    if [ -f "$FILE" ] && [ -f "$CHILD_PATH/$FILE" ]; then
        DIFF_MAIN=$(git diff "$LAST_TAG" HEAD -- "$FILE")
        pushd "$CHILD_PATH" > /dev/null
        DIFF_CHILD=$(git diff "$LAST_TAG" HEAD -- "$FILE" 2>/dev/null || true)
        popd > /dev/null
        if [ "$DIFF_MAIN" != "$DIFF_CHILD" ]; then
            echo "$FILE" >> "$REPORT"
        fi
    fi
done

# Compare deleted files
for FILE in "${DELETED[@]}"; do
    if [[ "$FILE" == app/Providers/* ]]; then
        continue
    fi
    if [ -f "$CHILD_PATH/$FILE" ]; then
        git show "$LAST_TAG:$FILE" > /tmp/original_file 2>/dev/null || true
        if [ -s /tmp/original_file ]; then
            if ! diff -q /tmp/original_file "$CHILD_PATH/$FILE" > /dev/null; then
                echo "$FILE" >> "$REPORT"
            fi
        else
            echo "$FILE" >> "$REPORT"
        fi
        rm -f /tmp/original_file
    fi
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
if [ -f "$REPORT_NAME" ]; then
    mapfile -t REPORTED < "$REPORT_NAME"
else
    REPORTED=()
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
    if [[ "$FILE" == app/Providers/* ]]; then
        continue
    fi
    should_add "$FILE" && git add "$FILE"
done

for FILE in "${DELETED[@]}"; do
    if [[ "$FILE" == app/Providers/* ]]; then
        continue
    fi
    if [ ! -f "$FILE" ]; then
        should_add "$FILE" && git rm --cached "$FILE" 2>/dev/null || true
    fi
done
popd > /dev/null 