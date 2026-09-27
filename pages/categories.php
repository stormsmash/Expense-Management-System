<?php
/**
 * Categories Management Page (Expense Groups & Items) — Modern Edition
 * Expense Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title  = 'จัดการหมวดค่าใช้จ่าย';
$logout = 'on';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/navbar.php';

$pdo = getDB();

$categories   = $pdo->query("SELECT * FROM expense_group ORDER BY group_idx ASC, group_id ASC")->fetchAll();
$subcategories = $pdo->query("SELECT * FROM expense_item ORDER BY group_id ASC, item_idx ASC, item_id ASC")->fetchAll();

$SYSTEM_DOC = __DIR__ . '/../system_doc/items_list/';
?>

<div class="container mt-page">
    <!-- Breadcrumb & Header Toolbar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="dashboard.php" class="text-muted"><i class="bi bi-house-door"></i> หน้าหลัก</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">จัดการหมวดค่าใช้จ่าย</li>
                </ol>
            </nav>
            <h1 class="fs-4 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="bi bi-tags-fill text-teal" style="color:var(--primary);"></i> หมวดหมู่และรายการค่าใช้จ่าย
            </h1>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="input-group" style="max-width: 260px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ค้นหาหมวดหรือรายการ...">
            </div>
            <button class="btn btn-primary-custom d-flex align-items-center gap-2 shadow-sm text-nowrap" id="addCategoryButton">
                <i class="bi bi-plus-lg"></i>
                <span>เพิ่มหมวดหมู่หลัก</span>
            </button>
        </div>
    </div>

    <!-- Categories List -->
    <div class="row g-3" id="categoriesContainer">
        <?php foreach ($categories as $cat): 
            $catItems = array_filter($subcategories, fn($s) => $s['group_id'] == $cat['group_id']);
            $itemCount = count($catItems);
        ?>
            <div class="col-12 category-wrapper" data-category-name="<?= htmlspecialchars(mb_strtolower($cat['group_name'])) ?>">
                <div class="card-modern">
                    <!-- Category Header -->
                    <div class="p-3 p-md-4 d-flex align-items-center justify-content-between gap-3 bg-white border-bottom border-light">
                        <div class="d-flex align-items-center gap-3 category-toggle flex-grow-1" style="cursor:pointer;" data-category-id="<?= $cat['group_id'] ?>">
                            <div class="kpi-icon-wrap" style="background:#f0fdfa;color:#0f766e;width:40px;height:40px;">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                            <div>
                                <h3 class="fs-5 fw-bold text-dark mb-0 category-title">
                                    <?= htmlspecialchars($cat['group_name']) ?>
                                </h3>
                                <span class="badge-custom badge-primary-subtle mt-1">
                                    <i class="bi bi-layers"></i> <?= $itemCount ?> รายการย่อย
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 edit-category"
                                    onclick="openCategoryModal(<?= $cat['group_id'] ?>, '<?= htmlspecialchars($cat['group_name'], ENT_QUOTES) ?>')"
                                    title="แก้ไขชื่อหมวด">
                                <i class="fas fa-edit me-1"></i> แก้ไขหมวด
                            </button>
                            <button class="btn btn-sm btn-primary-custom rounded-pill px-3 add-subcategory"
                                    data-category-id="<?= $cat['group_id'] ?>">
                                <i class="bi bi-plus-circle me-1"></i> เพิ่มค่าใช้จ่าย
                            </button>
                            <button class="btn btn-sm btn-light rounded-circle category-expand-btn category-toggle" 
                                    data-category-id="<?= $cat['group_id'] ?>" style="width:36px;height:36px;">
                                <i class="bi bi-chevron-down" id="chevron-<?= $cat['group_id'] ?>"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Subcategories Accordion Content -->
                    <div class="subcategories-panel p-3 p-md-4 bg-light" id="subcategories-<?= $cat['group_id'] ?>" style="display:none;">
                        <?php if (empty($catItems)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <span>ยังไม่มีรายการค่าใช้จ่ายในหมวดหมู่นี้</span>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill add-subcategory" data-category-id="<?= $cat['group_id'] ?>">
                                        <i class="bi bi-plus-lg me-1"></i> เพิ่มรายการแรก
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($catItems as $sub): 
                                    // Locate Image
                                    $imgUrl = '';
                                    if (is_dir($SYSTEM_DOC)) {
                                        foreach (array_diff(scandir($SYSTEM_DOC), ['..', '.']) as $img) {
                                            if (strpos($img, $sub['item_id'] . '_') === 0) {
                                                $imgUrl = '../system_doc/items_list/' . $img;
                                                break;
                                            }
                                        }
                                    }

                                    $branchGroupLabel = ['สำนักงานใหญ่และสาขา','สำนักงานใหญ่','สาขา'][$sub['branch_group']] ?? 'ทั้งหมด';
                                    $canSelectLabel   = ['สำนักงานใหญ่และสาขา','สำนักงานใหญ่','สาขา'][$sub['can_select_branch']] ?? 'ทั้งหมด';
                                ?>
                                    <div class="col-12 col-xl-6 subcategory-card-wrap" data-item-name="<?= htmlspecialchars(mb_strtolower($sub['item_name'])) ?>">
                                        <div class="card bg-white border rounded-3 p-3 h-100 shadow-sm">
                                            <div class="d-flex gap-3">
                                                <!-- Image or Icon Box -->
                                                <div class="flex-shrink-0">
                                                    <?php if ($imgUrl): ?>
                                                        <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($sub['item_name']) ?>" 
                                                             class="rounded-3 border object-fit-cover" style="width:90px;height:90px;">
                                                    <?php else: ?>
                                                        <div class="rounded-3 border bg-light d-flex flex-column align-items-center justify-content-center text-muted" 
                                                             style="width:90px;height:90px;">
                                                            <i class="bi bi-receipt fs-3 text-secondary opacity-50"></i>
                                                            <span style="font-size:10px;">ไม่มีรูป</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Item Details -->
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                        <h4 class="fs-6 fw-bold text-dark text-truncate mb-0">
                                                            <?= htmlspecialchars($sub['item_name']) ?>
                                                        </h4>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold rounded-pill px-2 py-1 small text-nowrap">
                                                            ฿<?= number_format((float)$sub['price_limit']) ?>
                                                        </span>
                                                    </div>

                                                    <!-- NAV / Account Codes -->
                                                    <div class="d-flex flex-wrap gap-1 my-2">
                                                        <?php if ($sub['acc_code_office']): ?>
                                                            <span class="badge bg-light text-secondary border font-monospace small" title="บัญชีสำนักงานใหญ่: <?= htmlspecialchars($sub['acc_desc_office']) ?>">
                                                                HQ: <?= htmlspecialchars($sub['acc_code_office']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($sub['acc_code_shop']): ?>
                                                            <span class="badge bg-light text-secondary border font-monospace small" title="บัญชีสาขา: <?= htmlspecialchars($sub['acc_desc_shop']) ?>">
                                                                Shop: <?= htmlspecialchars($sub['acc_code_shop']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($sub['acc_code_special']): ?>
                                                            <span class="badge bg-warning-subtle text-dark border font-monospace small" title="บัญชีพิเศษ: <?= htmlspecialchars($sub['acc_desc_special']) ?>">
                                                                Special: <?= htmlspecialchars($sub['acc_code_special']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Permissions & Responsibilities -->
                                                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted mb-2">
                                                        <span><i class="bi bi-eye text-primary me-1"></i>มองเห็น: <strong><?= $branchGroupLabel ?></strong></span>
                                                        <span>•</span>
                                                        <span><i class="bi bi-person-check text-success me-1"></i>ผู้รับผิดชอบ: <strong><?= $canSelectLabel ?></strong></span>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="d-flex justify-content-end">
                                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 edit-subcategory"
                                                                data-subcategory-id="<?= $sub['item_id'] ?>">
                                                            <i class="fas fa-edit me-1"></i> แก้ไขรายการ
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Add/Edit Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="addCategoryModalLabel">
            <i class="bi bi-folder-plus text-primary"></i> เพิ่มหมวดค่าใช้จ่าย
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addCategoryForm">
          <div class="modal-body">
              <input type="hidden" id="categoryId" name="categoryId">
              <div class="mb-3">
                <label for="categoryName" class="form-label fw-semibold text-secondary">ชื่อหมวดค่าใช้จ่าย <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-lg" id="categoryName" name="categoryName" 
                       placeholder="เช่น ค่าใช้จ่ายสำนักงานและเครื่องเขียน" required>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-primary-custom px-4 rounded-pill">
                <i class="bi bi-check2-circle me-1"></i> บันทึกข้อมูล
            </button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Add/Edit Subcategory -->
<div class="modal fade" id="addSubcategoryModal" tabindex="-1" aria-labelledby="addSubcategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="addSubcategoryModalLabel">
            <i class="bi bi-file-earmark-plus text-primary"></i> เพิ่มค่าใช้จ่าย
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addSubcategoryForm" enctype="multipart/form-data">
          <div class="modal-body">
              <input type="hidden" id="subcategoryId" name="subcategoryId">
              <input type="hidden" id="subCategoryGroupId" name="categoryId">

              <!-- Basic info -->
              <div class="row g-3 mb-3">
                  <div class="col-md-8">
                      <label class="form-label fw-semibold text-secondary">ชื่อค่าใช้จ่าย <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="subcategoryName" name="subcategoryName" 
                             placeholder="เช่น กระดาษ A4 และหมึกพิมพ์" required>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-semibold text-secondary">วงเงินสูงสุด (บาท) <span class="text-danger">*</span></label>
                      <div class="input-group">
                          <span class="input-group-text bg-light">฿</span>
                          <input type="number" class="form-control" id="price_limit" name="price_limit" min="0" step="any" placeholder="0.00" required>
                      </div>
                  </div>
              </div>

              <!-- NAV Accounts -->
              <div class="p-3 bg-light rounded-3 border mb-3">
                  <div class="fw-semibold text-dark small mb-2"><i class="bi bi-journals me-1 text-primary"></i> ผูกรหัสบัญชีในระบบ NAV</div>
                  <div class="row g-3">
                      <div class="col-md-6">
                          <label class="form-label small text-muted">รหัสบัญชี สำนักงานใหญ่</label>
                          <input type="text" class="form-control form-control-sm" id="acc_code_office" name="acc_code_office" placeholder="เช่น 51010-01" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small text-muted">ชื่อบัญชี ใน NAV (สำนักงานใหญ่)</label>
                          <input type="text" class="form-control form-control-sm" id="acc_desc_office" name="acc_desc_office" placeholder="ค่าเครื่องเขียนและแบบพิมพ์" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small text-muted">รหัสบัญชี สาขา</label>
                          <input type="text" class="form-control form-control-sm" id="acc_code_shop" name="acc_code_shop" placeholder="เช่น 51010-02" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small text-muted">ชื่อบัญชี ใน NAV (สาขา)</label>
                          <input type="text" class="form-control form-control-sm" id="acc_desc_shop" name="acc_desc_shop" placeholder="ค่าเครื่องเขียนสาขา" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small text-muted">รหัสบัญชีพิเศษ</label>
                          <input type="text" class="form-control form-control-sm" id="acc_code_special" name="acc_code_special" placeholder="เช่น 51010-99" required>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small text-muted">ชื่อบัญชีพิเศษ</label>
                          <input type="text" class="form-control form-control-sm" id="acc_desc_special" name="acc_desc_special" placeholder="ค่าใช้จ่ายพิเศษ" required>
                      </div>
                  </div>
              </div>

              <!-- Permissions -->
              <div class="row g-3 mb-3">
                  <div class="col-md-6">
                      <label class="form-label fw-semibold text-secondary">ใครสามารถเห็น</label>
                      <select class="form-select" id="branchGroup" name="branchGroup" required>
                          <option value="0">สำนักงานใหญ่และสาขา</option>
                          <option value="1">สำนักงานใหญ่</option>
                          <option value="2">สาขา</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-semibold text-secondary">ใครรับผิดชอบค่าใช้จ่าย</label>
                      <select class="form-select" id="canSelectBranch" name="canSelectBranch" required>
                          <option value="0">สำนักงานใหญ่และสาขา</option>
                          <option value="1">สำนักงานใหญ่</option>
                          <option value="2">สาขา</option>
                      </select>
                  </div>
              </div>

              <!-- Image Attachment -->
              <div class="mb-2">
                  <label class="form-label fw-semibold text-secondary">รูปภาพตัวอย่างรายการ</label>
                  <input type="file" class="form-control" id="subcategoryImage" name="subcategoryImage" accept="image/*">
                  <div class="mt-2 text-center" id="imagePreviewWrap" style="display:none;">
                      <img id="imagePreview" src="" alt="Preview" class="rounded-3 border shadow-sm" style="max-height:160px;object-fit:contain;">
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-primary-custom px-4 rounded-pill">
                <i class="bi bi-check2-circle me-1"></i> บันทึกข้อมูล
            </button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
// Image preview handler
document.getElementById('subcategoryImage').addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('imagePreview');
    const wrap = document.getElementById('imagePreviewWrap');
    if (file) {
        const reader = new FileReader();
        reader.onload = e => { 
            preview.src = e.target.result; 
            wrap.style.display = 'block'; 
        };
        reader.readAsDataURL(file);
    }
});

let catModal = null;
let subModal = null;

function getCatModal() {
    if (!catModal) catModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    return catModal;
}

function getSubModal() {
    if (!subModal) subModal = new bootstrap.Modal(document.getElementById('addSubcategoryModal'));
    return subModal;
}

function openCategoryModal(groupId, groupName) {
    document.getElementById('categoryId').value = groupId;
    document.getElementById('categoryName').value = groupName;
    document.getElementById('addCategoryModalLabel').innerHTML = '<i class="bi bi-pencil-square text-primary"></i> แก้ไขหมวดค่าใช้จ่าย';
    getCatModal().show();
}

document.addEventListener('DOMContentLoaded', function () {
    // Expand/Collapse Category Accordion
    document.querySelectorAll('.category-toggle').forEach(el => {
        el.addEventListener('click', function () {
            const id = this.getAttribute('data-category-id');
            const panel = document.getElementById(`subcategories-${id}`);
            const icon = document.getElementById(`chevron-${id}`);
            if (panel) {
                if (panel.style.display === 'none' || panel.style.display === '') {
                    panel.style.display = 'block';
                    if (icon) { icon.classList.remove('bi-chevron-down'); icon.classList.add('bi-chevron-up'); }
                } else {
                    panel.style.display = 'none';
                    if (icon) { icon.classList.remove('bi-chevron-up'); icon.classList.add('bi-chevron-down'); }
                }
            }
        });
    });

    // Instant Search Filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.category-wrapper').forEach(wrapper => {
                const catName = wrapper.getAttribute('data-category-name') || '';
                let matches = catName.includes(q);

                wrapper.querySelectorAll('.subcategory-card-wrap').forEach(item => {
                    const itemName = item.getAttribute('data-item-name') || '';
                    if (itemName.includes(q)) {
                        matches = true;
                        item.style.display = 'block';
                    } else {
                        item.style.display = (q !== '') ? 'none' : 'block';
                    }
                });

                if (matches || q === '') {
                    wrapper.style.display = 'block';
                    if (q !== '') {
                        const panel = wrapper.querySelector('.subcategories-panel');
                        if (panel) panel.style.display = 'block';
                    }
                } else {
                    wrapper.style.display = 'none';
                }
            });
        });
    }

    // Add Category Button
    const addCatBtn = document.getElementById('addCategoryButton');
    if (addCatBtn) {
        addCatBtn.addEventListener('click', function () {
            document.getElementById('categoryId').value = ''; 
            document.getElementById('categoryName').value = '';
            document.getElementById('addCategoryModalLabel').innerHTML = '<i class="bi bi-folder-plus text-primary"></i> เพิ่มหมวดค่าใช้จ่าย';
            getCatModal().show();
        });
    }

    // Submit Category Form
    const addCatForm = document.getElementById('addCategoryForm');
    if (addCatForm) {
        addCatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const catId   = document.getElementById('categoryId').value;
            const catName = document.getElementById('categoryName').value;
            const url     = catId ? '../api/update_category.php' : '../api/add_category.php';
            
            const formData = new FormData();
            formData.append('categoryId', catId);
            formData.append('categoryName', catName);

            fetch(url, { method: 'POST', body: formData })
                .then(r => r.text())
                .then(res => {
                    if (res === 'success') {
                        Swal.fire({ 
                            title: 'สำเร็จ!', 
                            text: catId ? 'อัปเดตหมวดหมู่สำเร็จ' : 'เพิ่มหมวดหมู่สำเร็จ', 
                            icon: 'success', 
                            timer: 1500, 
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถดำเนินการได้: ' + res, 'error');
                    }
                })
                .catch(() => Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อ Server', 'error'));
        });
    }

    // Add Subcategory Buttons
    document.querySelectorAll('.add-subcategory').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            document.getElementById('subCategoryGroupId').value = this.getAttribute('data-category-id');
            document.getElementById('addSubcategoryForm').reset();
            document.getElementById('subcategoryId').value = '';
            document.getElementById('imagePreviewWrap').style.display = 'none';
            document.getElementById('addSubcategoryModalLabel').innerHTML = '<i class="bi bi-file-earmark-plus text-primary"></i> เพิ่มค่าใช้จ่าย';
            getSubModal().show();
        });
    });

    // Submit Subcategory Form
    const addSubForm = document.getElementById('addSubcategoryForm');
    if (addSubForm) {
        addSubForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const subId = document.getElementById('subcategoryId').value;
            const url   = subId ? '../api/update_subcategory.php' : '../api/add_subcategory.php';
            const fd    = new FormData(this);

            fetch(url, { method: 'POST', body: fd })
                .then(r => r.text())
                .then(res => {
                    if (res === 'success') {
                        Swal.fire({ 
                            title: 'สำเร็จ!', 
                            text: subId ? 'อัปเดตรายการสำเร็จ' : 'เพิ่มค่าใช้จ่ายสำเร็จ', 
                            icon: 'success', 
                            timer: 1500, 
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถดำเนินการได้: ' + res, 'error');
                    }
                })
                .catch(() => Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อ Server', 'error'));
        });
    }

    // Edit Subcategory Buttons
    document.querySelectorAll('.edit-subcategory').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const subId = this.getAttribute('data-subcategory-id');
            fetch(`../api/update_subcategory.php?subcategoryId=${subId}`)
                .then(r => r.json())
                .then(d => {
                    if (d.error) { 
                        Swal.fire('เกิดข้อผิดพลาด', d.error, 'error'); 
                        return; 
                    }
                    document.getElementById('subcategoryId').value = d.item_id;
                    document.getElementById('subCategoryGroupId').value = d.group_id;
                    document.getElementById('subcategoryName').value = d.item_name; 
                    document.getElementById('price_limit').value = d.price_limit;
                    document.getElementById('acc_code_office').value = d.acc_code_office; 
                    document.getElementById('acc_desc_office').value = d.acc_desc_office;
                    document.getElementById('acc_code_shop').value = d.acc_code_shop; 
                    document.getElementById('acc_desc_shop').value = d.acc_desc_shop;
                    document.getElementById('acc_code_special').value = d.acc_code_special; 
                    document.getElementById('acc_desc_special').value = d.acc_desc_special;
                    document.getElementById('branchGroup').value = d.branch_group; 
                    document.getElementById('canSelectBranch').value = d.can_select_branch;
                    document.getElementById('imagePreviewWrap').style.display = 'none';
                    document.getElementById('addSubcategoryModalLabel').innerHTML = '<i class="bi bi-pencil-square text-primary"></i> แก้ไขค่าใช้จ่าย';
                    getSubModal().show();
                })
                .catch(err => Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลรายการได้', 'error'));
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
