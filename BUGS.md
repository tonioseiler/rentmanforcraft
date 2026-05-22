# Known Bugs

Bugs identified but not yet fixed. Each entry includes the file, line number, exact
code, and the recommended fix.

---

## ~~Bug 1 — Undefined global function call in ProjectsService~~ FIXED

`ProjectsService::getProjectItemsGroupedByCategory()` was a broken stub calling a
non-existent global function. The real implementation lives on the `Project` element
(`src/elements/Project.php`) and record (`src/records/Project.php`); templates call it
directly on the element. The stub was never called anywhere and has been **deleted**.

---

## ~~Bug 2 — Undefined variable `$user` in ApiController~~ FIXED

**File:** `src/controllers/ApiController.php` lines 218–226  
**Method:** `actionSetProjectProductQuantity()`

Both branches of the `if (empty($user))` block were identical and `$user` was never
defined. Since `$projectId` already comes from the session (`ACTIVE_PROJECT_ID`), no
user check was needed. The entire block was replaced with:

```php
$project = Project::find()->id($projectId)->one();
```

---

## ~~Bug 3 — Null dereference after `getActiveProject()` in ApiController~~ FIXED

**File:** `src/controllers/ApiController.php` line 587  
**Method:** `getProjectFromRequest()` (private helper)  
**Severity:** Fatal error when a guest has no active project in session

**Code:**
```php
$project = $projectService->getActiveProject();

// guest user can only access active project
if ($project->id == $params['projectId']) {
```

`getActiveProject()` returns `?Project` (null if no active project is stored in
session). Accessing `$project->id` without a null check will throw a fatal error for
guests who haven't started a project yet.

**Fix:**
```php
$project = $projectService->getActiveProject();
if (!$project || $project->id != $params['projectId']) {
    throw new ForbiddenHttpException();
}
return Project::find()->userId(0)->id($params['projectId'])->one();
```

---

## ~~Bug 4 — `getThumbUrl()` returns a placeholder string~~ FIXED

**File:** `src/elements/Product.php` line 464  
**Severity:** Thumbnails are not displayed anywhere Craft uses this method (e.g. element
index chips, relation fields)

**Code:**
```php
public function getThumbUrl(int $size): ?string {
    return 'thumb url';
}
```

This is an unfinished stub. The string `'thumb url'` will be used as an `<img src>`
value, producing broken images.

**Fix:** Either return the URL of the first public image from `getImages()`, or return
`null` to let Craft display its default placeholder:

```php
public function getThumbUrl(int $size): ?string {
    $images = $this->getImages();
    return !empty($images) ? reset($images)['url'] : null;
}
```

---

## ~~Bug 5 — Unguarded array key access in route/URI settings~~ FIXED

**Files:**
- `src/elements/Product.php` lines 253–254 and 274–277
- `src/elements/Category.php` lines 168–169 and 189–196
- `src/elements/Project.php` lines 222–223 and 242–249

**Severity:** Undefined index notice (PHP 8: TypeError) if a site handle is not
configured in the plugin settings

**Code (representative example — Product.php):**
```php
public function getUriFormat(): ?string {
    $settings = RentmanForCraft::getInstance()->getSettings()->productRoutes;
    return $settings[$this->site->handle]['uriFormat'];
}

protected function route(): array|string|null {
    $productRoutes = RentmanForCraft::getInstance()->getSettings()->productRoutes;
    return [
        'templates/render', [
            'template' => $productRoutes[$this->site->handle]['template'],
            ...
        ],
    ];
}
```

If the current site handle has no entry in `productRoutes` (or `categoryRoutes` /
`projectRoutes`), accessing `$settings[$this->site->handle]` returns null, and
`$settings[$this->site->handle]['uriFormat']` throws an error.

**Fix:** Guard with null-coalescing:
```php
public function getUriFormat(): ?string {
    $settings = RentmanForCraft::getInstance()->getSettings()->productRoutes;
    return $settings[$this->site->handle]['uriFormat'] ?? null;
}
```

Apply the same `?? null` / `?? ''` pattern to all `route()` method lookups in all
three element classes.

---

## ~~Bug 6 — Null dereference on `$product` in actionSetProjectProductQuantity~~ FIXED

**File:** `src/controllers/ApiController.php` lines 200, 205, 209, 210  
**Severity:** Fatal error

**Code:**
```php
$product = null;
if (isset($params['productId'])) {
    $product = Product::find()->id($params['productId'])->one();
}

// ... then immediately:
$item = ProjectItem::find()->where(['productId' => $product->id, ...])->one(); // line 200
$item->productId = $product->id;   // line 205
$item->itemtype  = $product->type; // line 209
$item->unit_price = $product->price; // line 210
```

`$product` starts as `null` and is only set if `productId` is in the request params.
Even when `productId` is present, `Product::find()->one()` returns `?Product` — it can
still be null if no product with that ID exists. Either way, the code immediately
dereferences `$product->id` with no null check, causing a fatal error.

**Fix:** Validate that `$product` is not null before proceeding and return an error
response if it is.

---

## ~~Bug 7 — Undefined `$params` in getProjectFromRequest()~~ FIXED

**File:** `src/controllers/ApiController.php` lines 559–569  
**Severity:** Error (undefined variable)

**Code:**
```php
if ($request->method == 'GET') {
    $params = $request->getQueryParams();
} else if ($request->method == 'POST') {
    $params = $request->getBodyParams();
}
// no else — $params is never set for other methods

if ($request->isCpRequest) {
    return Project::find()->id($params['projectId'])->one(); // $params undefined
}
```

If the HTTP method is anything other than GET or POST (e.g. DELETE, PUT, or a
malformed request), `$params` is never initialized. Accessing `$params['projectId']`
triggers an undefined variable error.

**Fix:** Add an `else` branch or initialize `$params = []` before the if-block.

---

## ~~Bug 8 — Null dereference in printCategoryTree()~~ FIXED

**File:** `src/variables/RentmanForCraftVariable.php` lines 83–88  
**Severity:** Fatal error

**Code:**
```php
$tmp = Category::find()->id($activeCategoryId)->one(); // returns ?Category
while (!$tmp->isMainCategory()) {  // fatal if $tmp is null
    $activeCatIds[] = $tmp->id;
    $tmp = $tmp->getParent(); // also returns ?Category — can go null mid-loop
}
$activeCatIds[] = $tmp->id; // fatal if $tmp is null after loop
```

Two null risks: (1) if `$activeCategoryId` doesn't match any category, `->one()`
returns null; (2) `getParent()` returns `?Category`, so if a category has no parent
(orphaned data), `$tmp` becomes null mid-loop and the next `isMainCategory()` call
crashes.

**Frontend impact:** Any page that calls `craft.rentman.printCategoryTree()` with an
active category ID that has been deleted (or has broken parent chain) returns a 500.

**Fix:**
```php
$tmp = Category::find()->id($activeCategoryId)->one();
while ($tmp && !$tmp->isMainCategory()) {
    $activeCatIds[] = $tmp->id;
    $tmp = $tmp->getParent();
}
if ($tmp) {
    $activeCatIds[] = $tmp->id;
}
```

---

## ~~Bug 9 — Null dereference in printCategoryTreeMobile()~~ FIXED

**File:** `src/variables/RentmanForCraftVariable.php` lines 135–140  
**Severity:** Fatal error

Identical code and identical bug to Bug 8, in the mobile variant of the same method.
Same fix applies.

---

## ~~Bug 10 — Null dereference in createProjectResponse()~~ FIXED

**File:** `src/controllers/ApiController.php` line 551  
**Severity:** Fatal error

**Code:**
```php
protected function createProjectResponse($project) {
    return [
        'project' => $project,
        'totals'  => $projectService->getProjectTotals($project),
        'items'   => $project->getItems(), // fatal if $project is null
    ];
}
```

`createProjectResponse()` is called after `Project::find()->id($projectId)->one()`,
which returns `?Project`. If the project is not found (e.g. stale session ID), `$project`
is null and `$project->getItems()` crashes. This affects multiple action methods:
`actionSetProjectProductQuantity()`, `actionSetProjectShootingDays()`,
`actionUpdateProject()`, and others.

**Fix:** Either guard in each caller before passing null, or add a null check at the
top of `createProjectResponse()` and return a safe empty response.
