# Athenia App Upgrade Guide

## How to Upgrade

1. **Run the update script**
   From your repo root, run:
   ```sh
   ./update.sh /path/to/your/child/project <last_version_tag>
   ```
   This will sync all core files, handle deletions, and update your README with the new version.

2. **Review Changes**
   - Inspect all changes, especially for moved or deleted files.
   - The update script will generate a text file (update report) listing all files that you changed in your project which were updated by the script. Any files that require manual review or have conflicts will be listed here.
   - Files that are safe to update will already be staged for commit in your child project.
   - Check the changelog below for feature and file-specific notes.
   - The script will generate an update report if any files need manual review.

3. **Run Migrations & Helpers**
   - Run any new migrations.
   - Run `php artisan ide-helper:models --smart-reset` if you use Laravel IDE Helper.

4. **Test & Finalize**
   - Test your app thoroughly.
   - Update your `.env` as needed for new config options.

---

# 4.0.0

This release introduces a major refactor and new features, especially around statistics, request structure, and internal architecture. Please review all changes carefully and follow the upgrade steps for each feature area.

### **Statistics System Overhaul**
- **New Models, Contracts, Services, and Repositories** for statistics:
  - Added: `Statistic`, `StatisticFilter`, `TargetStatistic` models and factories
  - Added: Contracts for statistics repositories and services
  - Added: Listeners, events, jobs, and policies for statistics
  - Added: Migration for statistics tables
  - Added: Full test coverage for statistics (unit, integration, feature)
- **Feature Files:**
  - `code/app/Athenia/Contracts/Models/CanBeAggregatedContract.php`
  - `code/app/Athenia/Contracts/Models/CanBeStatisticTargetContract.php`
  - `code/app/Athenia/Contracts/Repositories/Statistics/*`
  - `code/app/Athenia/Contracts/Services/Statistics/*`
  - `code/app/Athenia/Repositories/Statistics/*`
  - `code/app/Athenia/Services/Statistics/*`
  - `code/app/Athenia/Events/Statistics/*`
  - `code/app/Athenia/Listeners/Statistics/*`
  - `code/app/Athenia/Jobs/Statistics/*`
  - `code/app/Athenia/Models/Traits/HasStatisticTargets.php`
  - `code/app/Athenia/Observers/AggregatedModelObserver.php`
  - `code/app/Models/Statistics/Statistic.php`
  - `code/app/Models/Statistics/StatisticFilter.php`
  - `code/app/Models/Statistics/TargetStatistic.php`
  - `code/app/Policies/Statistics/StatisticPolicy.php`
  - `code/database/migrations/2025_04_30_000000_create_statistics_tables.php`
  - `code/database/factories/Statistics/*`
  - `code/tests/Athenia/Feature/Http/Statistics/*`
  - `code/tests/Athenia/Integration/Repositories/Statistics/*`
  - `code/tests/Athenia/Integration/Services/Statistics/*`
  - `code/tests/Athenia/Integration/Policies/Statistics/*`
  - `code/tests/Athenia/Unit/Events/Statistics/*`
  - `code/tests/Athenia/Unit/Jobs/Statistics/*`
  - `code/tests/Athenia/Unit/Listeners/Statistic/*`
  - `code/tests/Athenia/Unit/Models/Statistics/*`
  - `code/tests/Athenia/Unit/Services/Statistics/*`

### **Request Structure Refactor**
- **All request classes have been moved and restructured** for easier customization and clarity.
- **Feature Files:**
  - All new files under `code/app/Http/Core/Requests/`
  - Many files renamed from `RetrieveRequest` to `ViewRequest`, including:
    - `code/app/Athenia/Http/Core/Requests/MembershipPlan/RetrieveRequest.php` → `code/app/Athenia/Http/Core/Requests/MembershipPlan/ViewRequest.php`
    - `code/app/Athenia/Http/Core/Requests/Organization/RetrieveRequest.php` → `code/app/Athenia/Http/Core/Requests/Organization/ViewRequest.php`
  - Removed: `code/app/Http/Core/Requests/.gitkeep`

### **Core/Framework Improvements**
- **New and updated contracts, traits, and base classes** for better extensibility and code organization.
- **Feature Files:**
  - `code/app/Athenia/Models/Traits/HasValidationRules.php`
  - `code/app/Athenia/Observers/IndexableModelObserver.php`
  - `