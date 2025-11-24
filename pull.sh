#!/bin/bash

set -e

# Configuration - Edit these arrays to customize what to skip
SKIP_EXTENSIONS=(
    "jpg"
    "jpeg"
    "png"
    "gif"
    "bmp"
    "tiff"
    "webp"
    "ico"
    "svg"
    "pdf"
    "zip"
    "tar"
    "gz"
    "7z"
    "rar"
    "mp3"
    "mp4"
    "avi"
    "mov"
    "wmv"
    "flv"
    "wav"
    "ogg"
    "sh"
    "md"
)

SKIP_PATHS=(
    "node_modules/"
    ".git/"
    "vendor/"
    "storage/"
    "bootstrap/cache/"
    "public/storage/"
    ".env"
    ".env.*"
    "composer.lock"
    "package-lock.json"
    "yarn.lock"
    "*.log"
    "*.tmp"
    "*.cache"
    ".DS_Store"
    "Thumbs.db"
    "code/tests/Feature/"
    "code/tests/Integration/"
    "code/tests/Unit/"
)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_usage() {
    echo "Usage: $0 <remote_project_path> [options]"
    echo ""
    echo "Options:"
    echo "  -h, --help          Show this help message"
    echo "  -n, --dry-run       Show what would be copied without actually copying"
    echo "  -f, --force         Copy files without prompting for confirmation"
    echo "  -v, --verbose       Verbose output"
    echo "  --skip-ext EXT      Skip files with extension EXT (can be used multiple times)"
    echo "  --skip-path PATH    Skip files matching PATH pattern (can be used multiple times)"
    echo ""
    echo "Examples:"
    echo "  $0 /path/to/remote/project"
    echo "  $0 /path/to/remote/project --dry-run"
    echo "  $0 /path/to/remote/project --skip-ext txt --skip-path 'temp/'"
}

should_skip_file() {
    local file="$1"
    local basename=$(basename "$file")
    local extension="${basename##*.}"
    
    # Skip if extension is in SKIP_EXTENSIONS
    for skip_ext in "${SKIP_EXTENSIONS[@]}"; do
        if [[ "$extension" == "$skip_ext" ]]; then
            return 0
        fi
    done
    
    # Special case: Skip code/app/ but NOT code/app/Athenia/
    if [[ "$file" == code/app/* ]] && [[ "$file" != code/app/Athenia/* ]]; then
        return 0
    fi
    
    # Skip if path matches any pattern in SKIP_PATHS
    for skip_path in "${SKIP_PATHS[@]}"; do
        if [[ "$file" == *"$skip_path"* ]]; then
            return 0
        fi
    done
    
    return 1
}

compare_files() {
    local main_file="$1"
    local remote_file="$2"
    
    # If remote file doesn't exist, we can't pull it
    if [ ! -f "$remote_file" ]; then
        return 1
    fi
    
    # If main file doesn't exist, it's definitely different
    if [ ! -f "$main_file" ]; then
        return 0
    fi
    
    # Compare files byte-for-byte
    if ! diff -q "$main_file" "$remote_file" > /dev/null 2>&1; then
        return 0
    fi
    
    return 1
}

# Parse command line arguments
REMOTE_PATH=""
DRY_RUN=false
FORCE=false
VERBOSE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            print_usage
            exit 0
            ;;
        -n|--dry-run)
            DRY_RUN=true
            shift
            ;;
        -f|--force)
            FORCE=true
            shift
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        --skip-ext)
            SKIP_EXTENSIONS+=("$2")
            shift 2
            ;;
        --skip-path)
            SKIP_PATHS+=("$2")
            shift 2
            ;;
        -*)
            echo "Unknown option: $1"
            print_usage
            exit 1
            ;;
        *)
            if [ -z "$REMOTE_PATH" ]; then
                REMOTE_PATH="$1"
            else
                echo "Too many arguments"
                print_usage
                exit 1
            fi
            shift
            ;;
    esac
done

if [ -z "$REMOTE_PATH" ]; then
    echo "Error: Remote project path is required"
    print_usage
    exit 1
fi

if [ ! -d "$REMOTE_PATH" ]; then
    echo "Error: Remote project path does not exist: $REMOTE_PATH"
    exit 1
fi

# Get absolute paths
REMOTE_PATH=$(cd "$REMOTE_PATH" && pwd)
MAIN_PATH=$(pwd)

echo -e "${BLUE}Pulling changes from: ${NC}$REMOTE_PATH"
echo -e "${BLUE}To main project: ${NC}$MAIN_PATH"
echo ""

# Get all tracked files in the main project
TRACKED_FILES=()
while IFS= read -r line; do
    TRACKED_FILES+=("$line")
done < <(git ls-files)

# Arrays to store results
DIFFERENT_FILES=()
SKIPPED_FILES=()
MISSING_FILES=()

echo -e "${BLUE}Analyzing files...${NC}"

# Check each tracked file
for file in "${TRACKED_FILES[@]}"; do
    if should_skip_file "$file"; then
        SKIPPED_FILES+=("$file")
        [ "$VERBOSE" = true ] && echo -e "${YELLOW}SKIP: ${NC}$file"
        continue
    fi
    
    main_file="$MAIN_PATH/$file"
    remote_file="$REMOTE_PATH/$file"
    
    if [ ! -f "$remote_file" ]; then
        MISSING_FILES+=("$file")
        [ "$VERBOSE" = true ] && echo -e "${RED}MISSING: ${NC}$file (not found in remote)"
        continue
    fi
    
    if compare_files "$main_file" "$remote_file"; then
        DIFFERENT_FILES+=("$file")
        [ "$VERBOSE" = true ] && echo -e "${GREEN}DIFF: ${NC}$file"
    else
        [ "$VERBOSE" = true ] && echo -e "SAME: $file"
    fi
done

echo ""
echo -e "${BLUE}Analysis complete:${NC}"
echo -e "${GREEN}Different files: ${NC}${#DIFFERENT_FILES[@]}"
echo -e "${YELLOW}Skipped files: ${NC}${#SKIPPED_FILES[@]}"
echo -e "${RED}Missing files: ${NC}${#MISSING_FILES[@]}"
echo ""

if [ ${#DIFFERENT_FILES[@]} -eq 0 ]; then
    echo -e "${GREEN}No differences found. All files are up to date.${NC}"
    exit 0
fi

# Show what will be copied
echo -e "${BLUE}Files that are DIFFERENT and will be copied from remote:${NC}"
for file in "${DIFFERENT_FILES[@]}"; do
    echo "  $file"
done

if [ ${#MISSING_FILES[@]} -gt 0 ]; then
    echo ""
    echo -e "${RED}Files missing in remote project (will NOT be copied):${NC}"
    for file in "${MISSING_FILES[@]}"; do
        echo "  $file"
    done
fi

echo ""

# Ask for confirmation unless forced or dry run
if [ "$DRY_RUN" = true ]; then
    echo -e "${YELLOW}DRY RUN: Would copy ${#DIFFERENT_FILES[@]} different files${NC}"
    exit 0
fi

if [ "$FORCE" = false ]; then
    echo -n "Do you want to proceed with copying these ${#DIFFERENT_FILES[@]} DIFFERENT files? (y/N): "
    read -r response
    if [[ ! "$response" =~ ^[Yy]$ ]]; then
        echo "Aborted."
        exit 0
    fi
fi

# Create report file
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
REPORT_FILE="$MAIN_PATH/pull_report_$TIMESTAMP.txt"

echo "Pull Report - $(date)" > "$REPORT_FILE"
echo "Remote: $REMOTE_PATH" >> "$REPORT_FILE"
echo "Main: $MAIN_PATH" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"

# Copy files
echo -e "${BLUE}Copying files...${NC}"
COPIED_COUNT=0
FAILED_COUNT=0

for file in "${DIFFERENT_FILES[@]}"; do
    main_file="$MAIN_PATH/$file"
    remote_file="$REMOTE_PATH/$file"
    
    # Create directory if it doesn't exist
    mkdir -p "$(dirname "$main_file")"
    
    # Copy file
    if cp "$remote_file" "$main_file"; then
        echo -e "${GREEN}✓${NC} $file"
        echo "COPIED: $file" >> "$REPORT_FILE"
        ((COPIED_COUNT++))
    else
        echo -e "${RED}✗${NC} $file"
        echo "FAILED: $file" >> "$REPORT_FILE"
        ((FAILED_COUNT++))
    fi
done

# Add skipped and missing files to report
if [ ${#SKIPPED_FILES[@]} -gt 0 ]; then
    echo "" >> "$REPORT_FILE"
    echo "SKIPPED FILES:" >> "$REPORT_FILE"
    for file in "${SKIPPED_FILES[@]}"; do
        echo "  $file" >> "$REPORT_FILE"
    done
fi

if [ ${#MISSING_FILES[@]} -gt 0 ]; then
    echo "" >> "$REPORT_FILE"
    echo "MISSING FILES:" >> "$REPORT_FILE"
    for file in "${MISSING_FILES[@]}"; do
        echo "  $file" >> "$REPORT_FILE"
    done
fi

echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "${GREEN}Successfully copied: ${NC}$COPIED_COUNT files"
if [ $FAILED_COUNT -gt 0 ]; then
    echo -e "${RED}Failed to copy: ${NC}$FAILED_COUNT files"
fi
echo -e "${BLUE}Report saved to: ${NC}$REPORT_FILE"

# Git status
echo ""
echo -e "${BLUE}Git status:${NC}"
git status --porcelain

exit 0 