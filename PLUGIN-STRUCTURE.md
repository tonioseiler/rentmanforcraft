# Rentman for Craft — Plugin Structure

## Table of Contents

- [Directory Tree](#directory-tree)
- [Database Tables](#database-tables)
- [Element Types](#element-types)
- [Services](#services)
- [Web API](#web-api-apicontroller)
- [Twig Variable](#twig-variable-craftrentman)
- [Plugin Settings](#plugin-settings)
- [Console Commands](#console-commands)

---

## Directory Tree

```
/src/
├── RentmanForCraft.php               Main plugin class
├── console/controllers/
│   ├── RentmanController.php         CLI: update products/categories/all
│   └── UtilitiesController.php       CLI: resave elements via queue
├── controllers/
│   └── ApiController.php             Web API endpoints (products, projects, PDF, email)
├── elements/
│   ├── RentmanElement.php            Abstract base for all custom elements
│   ├── Product.php                   Product element type
│   ├── Category.php                  Category element type (hierarchical)
│   ├── Project.php                   Project element type (with custom statuses)
│   ├── conditions/
│   │   ├── ProductCondition.php
│   │   ├── CategoryCondition.php
│   │   └── ProjectCondition.php
│   └── db/
│       ├── ProductQuery.php          Filters: categoryId, rentmanId; status via in_shop
│       ├── CategoryQuery.php         Filters: parentId, rentmanId
│       └── ProjectQuery.php         Filters: userId; statuses: draft/ordered/submitted
├── fields/
│   └── Products.php                  Relation field (BaseRelationField) for Products
├── migrations/
│   ├── Install.php                   Creates all four DB tables
│   ├── m230224_001136_change_column_type_in_projects.php
│   ├── m230303_092720_add_shooting_day_to_projects.php
│   └── m260521_000000_migrate_titles_to_elements_sites.php  Craft 4→5: copies titles from content table
├── models/
│   └── Settings.php                  Plugin settings model
├── records/
│   ├── ElementRecord.php             Abstract AR base with getElement()/getUri()
│   ├── Category.php
│   ├── Product.php
│   ├── Project.php                   AR with getItems(), getItemsGroupedByCategory(),
│   │                                 getUser(), getTotalQuantity/Price/Weight()
│   └── ProjectItem.php               AR with getProject(), getProduct()
├── services/
│   ├── RentmanService.php            Rentman API integration (Guzzle); updateProducts(),
│   │                                 updateCategories(), submitProject()
│   ├── ProductsService.php           getAll/ById/ByCategory, searchProducts()
│   ├── CategoriesService.php         getCategories(), getCategoriesRecursive(),
│   │                                 getCategoryById()
│   └── ProjectsService.php           Active project, PDF generation (DomPDF),
│                                     shooting-days factor, price updates
├── templates/                        Twig templates for CP and email/PDF
├── translations/
│   ├── de/
│   └── en/
├── variables/
│   └── RentmanForCraftVariable.php   Twig variable: craft.rentman.*
└── web/assets/rentmanforcraft/
    ├── RentmanForCraftCPAsset.php
    └── RentmanForCraftSiteAsset.php
```

---

## Database Tables

### rentman-for-craft_categories
| Column | Type |
|---|---|
| id | PK (FK → elements.id CASCADE) |
| parentId | int nullable |
| rentmanId | int NOT NULL |
| displayname | string nullable |
| order | int nullable |
| itemtype | string nullable |
| dateCreated, dateUpdated, uid | standard Craft columns |

### rentman-for-craft_products
| Column | Type |
|---|---|
| id | PK (FK → elements.id CASCADE) |
| rentmanId | int NOT NULL |
| custom | longText nullable |
| displayname | string nullable |
| categoryId | int nullable |
| code | string nullable |
| internal_remark, external_remark | text nullable |
| location_in_warehouse | text nullable |
| unit | string nullable |
| in_shop | boolean default 0 |
| surface_article | boolean default 0 |
| shop_description_short/long | text nullable |
| shop_seo_title/keyword/description | text nullable |
| shop_featured | boolean default 0 |
| price, subrental_costs, list_price | double default 0 |
| critical_stock_level | int nullable |
| type, rental_sales | string NOT NULL |
| temporary, in_planner, in_archive | boolean |
| stock_management, taxclass | string NOT NULL |
| volume, packed_per, height, width, length, weight, power, current | double default 0 |
| images, files | text nullable (JSON arrays of Rentman file objects — images are hosted remotely on S3/Rentman CDN, not stored locally) |
| ledger, defaultValuegroup, qrcodes | string nullable |
| qrcodes_of_serial_numbers | text nullable |
| dateCreated, dateUpdated, uid | standard Craft columns |

### rentman-for-craft_projects
| Column | Type |
|---|---|
| id | PK (FK → elements.id CASCADE) |
| userId | int nullable |
| contact_* | various strings |
| usageperiod_start/end | dateTime nullable |
| is_paid | string nullable |
| in, out | dateTime nullable (pickup/return) |
| location_* | various strings |
| external_referenc | string nullable |
| remark | longText nullable |
| planperiod_start/end | dateTime nullable |
| price | double default 0 |
| shooting_days | int default 1 |
| dateOrdered | dateTime nullable |
| dateSubmitted | dateTime nullable |
| dateCreated, dateUpdated, uid | standard Craft columns |

### rentman-for-craft_projectitems
| Column | Type |
|---|---|
| id | PK |
| projectId | int NOT NULL (FK → projects.id) |
| productId | int NOT NULL (FK → products.id) |
| factor | double default 1 |
| quantity | int default 1 |
| unit_price | double default 1 |
| price | double default 1 |
| itemtype | string nullable |
| dateCreated, dateUpdated, uid | standard Craft columns |

---

## Element Types

### RentmanElement (abstract base)
- **`getUiLabel()`**: returns `displayname` (if the property exists and is non-empty),
  then `title`, then `"ElementType {id}"`. Ensures correct CP labels even when
  `elements_sites.title` is NULL after a Craft 4→5 upgrade.

### Product
- **Statuses**: enabled/disabled (driven by `in_shop` flag)
- **URI format**: configured per-site in plugin settings (`productRoutes`)
- **Field layout**: dynamically built in `getFieldLayout()` — all imported values
  displayed as read-only HTML via `rentman-for-craft/_includes/show/imported-value`,
  plus images and files includes. A second tab merges any custom fields saved via
  the CP settings field layout editor.
- **Table attributes**: images (thumbnail strip), rentmanId, link, files, dateUpdated
- **Sidebar**: hidden (returns empty string from `getSidebarHtml()`)
- **Metadata panel**: ID, Status, Rentman ID, Code, price fields, stock flags, etc.

### Category
- **Hierarchy**: self-referential via `parentId`; `isMainCategory()` returns true
  when `parentId == 0`
- **URI format**: configured per-site in plugin settings (`categoryRoutes`)
- **Field layout**: title + displayname, read-only (both fall back to `displayname`
  if `elements_sites.title` is NULL)
- **Methods**: `getChildren()`, `hasChildren()`, `getParent()`

### Project
- **Statuses**: `draft` (no dates), `ordered` (dateOrdered set), `submitted` (both set)
- **URI format**: configured per-site in plugin settings (`projectRoutes`)
- **Field layout**: four tabs — Project (dates, remarks), Products (items list),
  Contact (person/address fields), Production (location fields)
- **Sidebar**: submit-to-Rentman button (`projects/_submit-button` template)
- **Access control**: `canView()` supports guest users via session `ACTIVE_PROJECT_ID`
- **Methods**: `getItems()`, `getItemsGroupedByCategory()`, `getUser()`,
  `getTotalQuantity()`, `getTotalPrice()`, `getTotalWeight()`

---

## Services

### RentmanService
Connects to the Rentman REST API using Guzzle.
- `updateProducts()`: paginates through `/equipment`, creates/updates Product elements,
  assigns categories, fetches and stores images/files JSON, deletes removed products.
- `updateCategories()`: fetches `/folders?itemtype=equipment`, creates/updates Category
  elements, resolves parent hierarchy, deletes removed categories.
- `getProductAccesories($productId)`: fetches accessories for a product.
- `getSetContents($productId)`: fetches set contents for a kit product.
- `submitProject($project)`: POSTs a project request to Rentman `/projectrequests`,
  then adds each project item as `/projectrequestequipment`. Handles the Rentman
  single-day bug (adds 5 seconds to end date when `shooting_days == 1`).

### ProductsService
- `getAllProducts()`, `getProductById()`, `getProductsByCategory()`
- `searchProducts($query)`: uses Craft's search with wildcard prefix/suffix.

### CategoriesService
- `getCategories($parentId)`, `getCategoriesRecursive($parentId)`, `getCategoryById()`

### ProjectsService
- `getActiveProject()`: reads `ACTIVE_PROJECT_ID` from session, returns Project or null.
- `getUserProjects($user)`: returns all projects for a given user.
- `updateProjectItem($item)`: recalculates factor and price for one item.
  Sales items always use factor 1; rental items use the shooting-days factor table.
- `updateProjectItemsAndPrice($project)`: recalculates all items and saves total price.
- `getShootingDaysFactor($days)`: looks up factor from the plugin settings table.
- `generatePDF($project, $stream)`: renders a Twig template and produces a PDF via
  DomPDF. Can stream directly or save to `@storage/projects/`.

---

## Web API (ApiController)

All endpoints below are under `/actions/rentman-for-craft/api/`.
Most are accessible anonymously (guest users can manage their active project).

| Action | Method | Description |
|---|---|---|
| `index` | GET | Version info |
| `products` | GET | All products, by category, or by ID |
| `search-products` | GET | Full-text product search |
| `categories` | GET | Root or subcategories, or single category |
| `get-active-project` | GET | Returns active project from session |
| `set-active-project` | POST | Sets active project in session |
| `create-project` | POST | Creates new Project element, inherits contact data from last project |
| `update-project` | POST | Updates project fields |
| `set-project-product-quantity` | POST | Adds/updates/removes a ProjectItem |
| `set-project-shooting-days` | POST | Updates shooting_days, recalculates prices |
| `submit-project` | POST | Sets dateOrdered, optionally auto-submits to Rentman, sends email with PDF |
| `submit-project-to-rentman` | POST | CP-only: manually submit an existing project to Rentman |
| `copy-project` | POST | Duplicates project and all its items |
| `delete-project` | POST | Deletes project element |
| `generate-project-pdf` | GET/POST | Generates and saves PDF, returns file path |

---

## Twig Variable (`craft.rentman`)

| Method | Description |
|---|---|
| `cpTitle` | Returns the configured CP title |
| `getAllProducts()` | All enabled products |
| `getProductById($id)` | Single product |
| `getProductsByCategory($categoryId)` | Products in a category |
| `getCategories($parentId)` | Direct children of a category (default: root) |
| `getCategoriesRecursive($parentId)` | All descendants |
| `getCategoryById($id)` | Single category |
| `printCategoryTree($fullTree, $activeCategoryId, $parentId)` | Renders `<ul>` HTML tree with active state |
| `printCategoryTreeMobile(...)` | Mobile variant with checkbox toggles |
| `getSetContents($productId)` | Set kit contents |
| `getProductAccesories($productId)` | Product accessories |
| `getUserProjects()` | Projects belonging to current user |
| `getActiveProject()` | Active project from session |
| `getProjectProductQuantity($productId)` | Quantity of a product in the active project |
| `searchProducts($query)` | Full-text search |

---

## Plugin Settings

Configured under CP → Settings → Rentman for Craft.

| Setting | Description |
|---|---|
| `cpTitle` | CP nav label (default: "Rentman") |
| `apiUrl` | Rentman API base URL |
| `apiKey` | Rentman API bearer token |
| `productRoutes` | Per-site `uriFormat` and `template` for product pages |
| `categoryRoutes` | Per-site `uriFormat` and `template` for category pages |
| `projectRoutes` | Per-site `uriFormat` and `template` for project pages |
| `autoSubmitProjects` | Whether to auto-submit to Rentman when a project is ordered |
| `shootingDaysFactor` | Array of `{days, factor}` objects for pricing multipliers (1–107 days) |
| `pdfFilename` | PDF filename prefix |
| `templateForProjectPdf` | Per-site override template for PDF rendering |
| `templateForProjectEmail` | Per-site override template for order confirmation email |
| `projectEmailSubject` | Email subject line |
| `projectPdfFooter` | Footer text for generated PDFs |

---

## Console Commands

```
craft rentman/rentman/update-products     Fetch and sync products from Rentman API
craft rentman/rentman/update-categories   Fetch and sync categories from Rentman API
craft rentman/rentman/update-all          Sync categories then products
craft rentman/utilities/resave-products   Resave all Product elements via queue
craft rentman/utilities/resave-categories Resave all Category elements via queue
```
