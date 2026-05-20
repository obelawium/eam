# Obelawium EAM � Employee Asset Management

A Laravel package for managing physical assets across any holder type (employees, warehouses, departments, etc.) using a polymorphic "holder" pattern.

Part of the [Obelawium IUM](https://github.com/obelawium/ium) ecosystem.

---

## Requirements

- PHP ^8.2
- Laravel ^11.0 | ^12.0
- `obelaw/ium`

---

## Installation

```bash
composer require obelaw/ium-eam
```

### Publish migrations

```bash
php artisan vendor:publish --tag=ium-eam-migrations
php artisan migrate
```

### Publish config (optional)

```bash
php artisan vendor:publish --tag=ium-eam-config
```

---

## Configuration

`config/eam.php` maps holder class name fragments to asset statuses:

```php
return [
    'holder_status_map' => [
        'Employee' => 'assigned',
        'Warehouse' => 'available',
        'Maintenance' => 'maintenance',
        'Department' => 'assigned',
    ],
];
```

When an asset is transferred to a holder, the status is resolved by matching `class_basename($holderType)` against the keys above.

### Morph map (recommended)

Register a morph map in your `AppServiceProvider` to keep class names out of the database:

```php
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'employee' => \App\Models\Employee::class,
    'warehouse' => \App\Models\Warehouse::class,
    'department' => \App\Models\Department::class,
]);
```

---

## Making a Model a Valid Asset Holder

Add the `HasAssets` trait to any Eloquent model:

```php
use Obelaw\Ium\Eam\Traits\HasAssets;

class Employee extends Model
{
    use HasAssets;
}
```

This provides:

| Method | Description |
|---|---|
| `assets()` | `MorphMany` � all assets currently held |
| `assetTransitions()` | `MorphMany` � all transitions involving this holder |
| `receiveAsset(int $assetId, array $data = [])` | Transfer an asset to this holder |

---

## Asset Lifecycle

1. **Create** � asset enters inventory with status `available`
2. **Transfer** � asset is transferred to a holder; status updates from config map
3. **Transfer again** � asset moves between holders; full history is recorded
4. **Return** � transfer with `to_holder_type = null` returns asset to pool (`available`)
5. **Retire** � asset is permanently retired; holder is cleared, status set to `retired`
6. **Report** � query assets by holder, category, status, or transition history

---

## Usage

All functionality is accessible via `ium()->eam()`:

### Assets

```php
use Obelaw\Ium\Eam\Data\AssetDTO;
use Obelaw\Ium\Eam\Data\TransferAssetDTO;

// Create
$asset = ium()->eam()->assets()->create(AssetDTO::from([
    'code' => 'LPT-001',
    'name' => 'Dell Laptop',
    'description' => 'Developer laptop',
    'category_id' => 1,
    'serial_number' => 'SN123456',
    'purchase_date' => '2024-01-15',
    'purchase_value' => 1200.00,
]));

// Update
$asset = ium()->eam()->assets()->update($asset->id, AssetDTO::from([...]));

// Find
$asset = ium()->eam()->assets()->find($asset->id);

// List all
$assets = ium()->eam()->assets()->list();

// List available only
$assets = ium()->eam()->assets()->available();

// Transfer to a holder
$transition = ium()->eam()->assets()->transferTo(TransferAssetDTO::from([
    'asset_id' => $asset->id,
    'toHolder' => Employee::find(42),
    'performed_by' => 'admin',
    'condition' => 'good',
    'reason' => 'New hire equipment',
]));

// Return to pool (nullable holder)
$transition = ium()->eam()->assets()->transferTo(TransferAssetDTO::from([
    'asset_id' => $asset->id,
    'toHolder' => null,
    'performed_by' => 'admin',
    'condition' => 'good',
]));

// View transition history
$history = ium()->eam()->assets()->history($asset->id);

// Retire
$asset = ium()->eam()->assets()->retire($asset->id, 'End of life');

// Assets held by a specific holder
$assets = ium()->eam()->assets()->heldBy(\App\Models\Employee::class, 42);
```

### Using the HasAssets trait directly

```php
$employee = Employee::find(42);

// All assets currently held
$employee->assets;

// Receive an asset
$employee->receiveAsset($asset->id, [
    'performed_by' => 'admin',
    'condition' => 'good',
]);
```

### Categories

```php
use Obelaw\Ium\Eam\Data\AssetCategoryDTO;

$category = ium()->eam()->categories()->create(AssetCategoryDTO::from([
    'name' => 'Laptops',
    'slug' => 'laptops',
    'parent_id' => null,
]));

$category = ium()->eam()->categories()->find(1);
$categories = ium()->eam()->categories()->list();
```

### Reports

```php
// Overdue assets
$overdue = ium()->eam()->reports()->overdue();

// Assets by holder
$assets = ium()->eam()->reports()->byHolder(\App\Models\Warehouse::class, 3);

// Assets by category
$assets = ium()->eam()->reports()->byCategory(1);

// Assets by status
$assets = ium()->eam()->reports()->byStatus('maintenance');

// Transition history for an asset
$transitions = ium()->eam()->reports()->transitions($asset->id);

// Lost and damaged assets
$assets = ium()->eam()->reports()->lostAndDamaged();
```

---

## Database Schema

### `eam_asset_categories`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `name` | string | |
| `slug` | string | unique |
| `parent_id` | bigint | nullable, self-ref FK |

### `eam_assets`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `code` | string | unique |
| `name` | string | |
| `description` | text | nullable |
| `category_id` | bigint | FK |
| `status` | enum | available, assigned, maintenance, lost, retired |
| `serial_number` | string | nullable |
| `purchase_date` | date | nullable |
| `purchase_value` | decimal(15,2) | nullable |
| `holder_type` | string | nullable (morph) |
| `holder_id` | bigint | nullable (morph) |

### `eam_asset_transitions`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `asset_id` | bigint | FK ? eam_assets |
| `from_holder_type` | string | nullable |
| `from_holder_id` | bigint | nullable |
| `to_holder_type` | string | nullable |
| `to_holder_id` | bigint | nullable |
| `performed_by` | string | |
| `condition` | enum | good, damaged, lost (nullable) |
| `reason` | string | nullable |
| `notes` | text | nullable |
| `transitioned_at` | timestamp | |

### `eam_audit_log`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `asset_id` | bigint | FK ? eam_assets |
| `action` | string | created, updated, transferred, retired |
| `performed_by` | string | |
| `payload` | json | nullable |
| `notes` | text | nullable |

---

## Events

| Event | Payload | Fired when |
|---|---|---|
| `AssetCreated` | `Asset $asset` | Asset is created |
| `AssetTransferred` | `AssetTransition $transition` | Asset changes holder |
| `AssetRetired` | `Asset $asset` | Asset is retired |
| `AssetOverdue` | `Asset $asset` | Overdue report run (once per asset) |

---

## Exceptions

| Exception | Thrown when |
|---|---|
| `AssetNotFoundException` | Asset ID does not exist |
| `AssetAlreadyRetiredException` | Attempting to transfer/retire an already-retired asset |
| `InvalidHolderException` | Target holder class does not use `HasAssets` trait |
| `InvalidTransitionException` | Transition is not permitted in current state |

---

## License

MIT
