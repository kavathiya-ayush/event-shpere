<?php
// admin/manage-categories.php - Royal Celebration Category Management
require_once '../config/db.php';
require_once '../includes/functions.php';

$action = $_GET['action'] ?? 'list';
$catId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['category_name'] ?? '');

    if (!empty($categoryName)) {
        $slug = slugify($categoryName);

        if ($action === 'edit' && $catId > 0) {
            $upStmt = $pdo->prepare("UPDATE categories SET category_name = ?, slug = ? WHERE id = ?");
            $upStmt->execute([$categoryName, $slug, $catId]);
            set_flash('success', 'Royal category updated successfully.');
        } else {
            // Check uniqueness
            $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE category_name = ?");
            $checkStmt->execute([$categoryName]);
            if ($checkStmt->fetch()) {
                set_flash('danger', 'A category with this name already exists.');
            } else {
                $inStmt = $pdo->prepare("INSERT INTO categories (category_name, slug) VALUES (?, ?)");
                $inStmt->execute([$categoryName, $slug]);
                set_flash('success', 'New royal category "' . $categoryName . '" created successfully.');
            }
        }
    } else {
        set_flash('danger', 'Category name cannot be empty.');
    }
    header("Location: manage-categories.php");
    exit;
}

// Handle Delete
if ($action === 'delete' && $catId > 0) {
    // Delete category
    $delStmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $delStmt->execute([$catId]);
    set_flash('success', 'Category and its celebrations have been deleted.');
    header("Location: manage-categories.php");
    exit;
}

// Fetch edit target
$editCat = null;
if ($action === 'edit' && $catId > 0) {
    $cStmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $cStmt->execute([$catId]);
    $editCat = $cStmt->fetch();
}

// Fetch all categories with event count
$categories = $pdo->query("SELECT c.*, COUNT(e.id) AS event_count 
                           FROM categories c 
                           LEFT JOIN events e ON c.id = e.category_id 
                           GROUP BY c.id 
                           ORDER BY c.category_name ASC")->fetchAll();

$pageTitle = "Manage Categories";
$pageHeader = "Celebration Taxonomy & Categories";
include 'sidebar.php';
?>

<?php
function get_category_icon($catName) {
    $name = strtolower($catName);
    if (str_contains($name, 'wedding') || str_contains($name, 'mandap')) return 'fa-ring';
    if (str_contains($name, 'garba') || str_contains($name, 'navratri')) return 'fa-wand-magic-sparkles';
    if (str_contains($name, 'corporate') || str_contains($name, 'summit') || str_contains($name, 'gala')) return 'fa-building-columns';
    if (str_contains($name, 'sufi') || str_contains($name, 'concert') || str_contains($name, 'music')) return 'fa-guitar';
    if (str_contains($name, 'sangeet')) return 'fa-compact-disc';
    if (str_contains($name, 'youth') || str_contains($name, 'college')) return 'fa-graduation-cap';
    return 'fa-champagne-glasses';
}
?>

<div class="admin-categories-layout" style="display:grid; grid-template-columns: 1fr 2fr; gap:30px;">
  
  <!-- Left: Category Form -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid <?= $action === 'edit' ? 'fa-pen-to-square' : 'fa-plus' ?> me-2" style="color:var(--gold);"></i>
        <?= $action === 'edit' ? 'Edit Royal Category' : 'Add New Category' ?>
      </h3>
    </div>
    
    <form action="manage-categories.php<?= $action === 'edit' ? '?action=edit&id=' . $catId : '' ?>" method="POST" style="padding:24px;">
      <div class="form-group">
        <label for="category_name">Category Name *</label>
        <input type="text" id="category_name" name="category_name" class="form-control" placeholder="e.g. Royal Wedding &amp; Mandap" required value="<?= e($editCat['category_name'] ?? '') ?>">
      </div>

      <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="flex:1;">
          <i class="fa-solid fa-check me-1"></i> <?= $action === 'edit' ? 'Update Category' : 'Save Category' ?>
        </button>
        <?php if ($action === 'edit'): ?>
          <a href="manage-categories.php" class="btn btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);">Cancel</a>
        <?php endif; ?>
      </div>

      <!-- Curator's Architecture Helper Box -->
      <div style="margin-top:24px; padding:18px; background:#FFFDF9; border:1.5px solid var(--gold-border); border-radius:12px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
          <i class="fa-solid fa-gem" style="color:var(--gold); font-size:14px;"></i>
          <strong style="color:var(--burgundy); font-size:13px;">Curator's Architecture</strong>
        </div>
        <p style="font-size:12px; color:var(--charcoal-muted); margin:0; line-height:1.6;">
          Categories structure public celebrations into curated heritage themes. Each celebration is linked to a category for seamless guest discovery across Gondal &amp; Gujarat.
        </p>
      </div>
    </form>
  </div>

  <!-- Right: Categories List Table -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
          <i class="fa-solid fa-layer-group me-2" style="color:var(--gold);"></i> All Categories (<?= count($categories) ?>)
        </h3>
      </div>
      <div class="admin-toolbar">
        <div class="search-wrapper">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="tableSearch" placeholder="Search categories..." class="search-input" style="width:200px;">
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Celebrations</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td><strong style="color:var(--burgundy);">#<?= $cat['id'] ?></strong></td>
              <td>
                <div style="display:flex; align-items:center; gap:12px;">
                  <div class="category-icon-circle">
                    <i class="fa-solid <?= get_category_icon($cat['category_name']) ?>"></i>
                  </div>
                  <div>
                    <strong style="color:var(--burgundy-dark); font-size:14px; display:block;">
                      <?= e($cat['category_name']) ?>
                    </strong>
                    <span style="font-size:11.5px; color:var(--charcoal-muted);">Theme #<?= $cat['id'] ?></span>
                  </div>
                </div>
              </td>
              <td>
                <code style="background:var(--ivory-soft); color:var(--charcoal); padding:3px 8px; border-radius:4px; border:1px solid var(--gold-border); font-size:12px;"><?= e($cat['slug']) ?></code>
              </td>
              <td>
                <a href="manage-events.php" class="royal-category-pill" title="View Celebrations in this category" style="text-decoration:none;">
                  <i class="fa-solid fa-calendar-check" style="color:var(--gold); font-size:11px;"></i>
                  <?= $cat['event_count'] ?> <?= $cat['event_count'] == 1 ? 'Celebration' : 'Celebrations' ?>
                </a>
              </td>
              <td style="text-align:right;" class="nowrap">
                <div class="action-btns" style="justify-content:flex-end;">
                  <a href="manage-categories.php?action=edit&id=<?= $cat['id'] ?>" class="btn-icon edit" title="Edit Category">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a href="manage-categories.php?action=delete&id=<?= $cat['id'] ?>" class="btn-icon delete" title="Delete Category" onclick="return confirmAction('Deleting this category will cascade and delete associated celebrations. Continue?')">
                    <i class="fa-solid fa-trash-can"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include 'footer.php'; ?>
