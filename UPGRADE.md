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

# 4.0.1

This release includes a major refactor of the statistics system, including directory renames, namespace changes, and various improvements. The following changes have been made:

### Directory and Namespace Changes
The following directories have been renamed from "Statistics" to "Statistic":

- `code/app/Athenia/Contracts/Repositories/Statistics/*` → `code/app/Athenia/Contracts/Repositories/Statistic/*`
- `code/app/Athenia/Contracts/Services/Statistics/*` → `code/app/Athenia/Contracts/Services/Statistic/*`
- `code/app/Athenia/Repositories/Statistics/*` → `code/app/Athenia/Repositories/Statistic/*`
- `code/app/Athenia/Services/Statistics/*` → `code/app/Athenia/Services/Statistic/*`
- `code/app/Athenia/Events/Statistics/*` → `code/app/Athenia/Events/Statistic/*`
- `code/app/Athenia/Listeners/Statistics/*` → `code/app/Athenia/Listeners/Statistic/*`
- `code/app/Athenia/Jobs/Statistics/*` → `code/app/Athenia/Jobs/Statistic/*`
- `code/app/Models/Statistics/*` → `code/app/Models/Statistic/*`
- `code/app/Policies/Statistics/*` → `code/app/Policies/Statistic/*`
- `code/database/factories/Statistics/*` → `code/database/factories/Statistic/*`
- `code/tests/Athenia/Feature/Http/Statistics/*` → `code/tests/Athenia/Feature/Http/Statistic/*`
- `code/tests/Athenia/Integration/Repositories/Statistics/*` → `code/tests/Athenia/Integration/Repositories/Statistic/*`
- `code/tests/Athenia/Integration/Services/Statistics/*` → `code/tests/Athenia/Integration/Services/Statistic/*`
- `code/tests/Athenia/Integration/Policies/Statistics/*` → `code/tests/Athenia/Integration/Policies/Statistic/*`
- `code/tests/Athenia/Unit/Events/Statistics/*` → `code/tests/Athenia/Unit/Events/Statistic/*`
- `code/tests/Athenia/Unit/Jobs/Statistics/*` → `code/tests/Athenia/Unit/Jobs/Statistic/*`
- `code/tests/Athenia/Unit/Models/Statistics/*` → `code/tests/Athenia/Unit/Models/Statistic/*`
- `code/tests/Athenia/Unit/Services/Statistics/*` → `code/tests/Athenia/Unit/Services/Statistic/*`

### Modified Files

#### Request Classes
- `code/app/Athenia/Http/Core/Requests/Statistic/DeleteRequestAbstract.php`
- `code/app/Athenia/Http/Core/Requests/Statistic/IndexRequestAbstract.php`
- `code/app/Athenia/Http/Core/Requests/Statistic/StoreRequestAbstract.php`
- `code/app/Athenia/Http/Core/Requests/Statistic/UpdateRequestAbstract.php`
- `code/app/Athenia/Http/Core/Requests/Statistic/ViewRequestAbstract.php`
- `code/app/Http/Core/Requests/Statistic/DeleteRequest.php`
- `code/app/Http/Core/Requests/Statistic/IndexRequest.php`
- `code/app/Http/Core/Requests/Statistic/StoreRequest.php`
- `code/app/Http/Core/Requests/Statistic/UpdateRequest.php`
- `code/app/Http/Core/Requests/Statistic/ViewRequest.php`

#### Service Providers and Core Components
- `code/app/Athenia/Providers/BaseEventServiceProvider.php`
- `code/app/Athenia/Providers/BaseRepositoryProvider.php`
- `code/app/Athenia/Providers/BaseServiceProvider.php`
- `code/app/Athenia/Providers/BaseRouteServiceProvider.php`
- `code/app/Athenia/Observers/AggregatedModelObserver.php`
- `code/app/Athenia/Models/Traits/HasStatisticTargets.php`
- `code/app/Athenia/Contracts/Models/CanBeStatisticTargetContract.php`

#### Models and Repositories
- `code/app/Models/Collection/Collection.php`
- `code/app/Models/Statistic/Statistic.php`
- `code/app/Models/Statistic/StatisticFilter.php`
- `code/app/Models/Statistic/TargetStatistic.php`
- `code/app/Athenia/Repositories/Statistic/StatisticRepository.php`
- `code/app/Athenia/Repositories/Statistic/StatisticFilterRepository.php`
- `code/app/Athenia/Repositories/Statistic/TargetStatisticRepository.php`

#### Tests
- `code/tests/Athenia/Unit/Models/Collection/CollectionTest.php`
- `code/tests/Athenia/Unit/Models/Traits/HasStatisticTargetsTest.php`
- `code/tests/Athenia/Unit/Listeners/Statistic/StatisticCreatedListenerTest.php`
- `code/tests/Athenia/Unit/Listeners/Statistic/StatisticDeletedListenerTest.php`
- `code/tests/Athenia/Unit/Listeners/Statistic/StatisticUpdatedListenerTest.php`
- `code/tests/Athenia/Integration/Policies/Statistics/StatisticPolicyTest.php`


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