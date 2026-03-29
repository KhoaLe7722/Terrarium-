<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

function format_product_currency(float|int|string $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . 'đ';
}

function build_product_page_url(string $searchTerm, ?string $sortValue = null): string
{
    $query = [];

    if ($searchTerm !== '') {
        $query['q'] = $searchTerm;
    }

    if ($sortValue !== null && $sortValue !== '' && $sortValue !== 'default') {
        $query['sort'] = $sortValue;
    }

    return 'sanpham.php' . ($query !== [] ? '?' . http_build_query($query) : '');
}

$searchTerm = trim((string) ($_GET['q'] ?? ''));
$sortBy = trim((string) ($_GET['sort'] ?? 'default'));
$allowedSorts = ['default', 'price_asc', 'price_desc'];
if (!in_array($sortBy, $allowedSorts, true)) {
    $sortBy = 'default';
}

$sql = "
    SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, mo_ta, so_luong_ton
    FROM products
";
$params = [];

if ($searchTerm !== '') {
    $sql .= "
        WHERE ten_sp LIKE :search
           OR mo_ta LIKE :search
    ";
    $params['search'] = '%' . $searchTerm . '%';
}

$orderBy = match ($sortBy) {
    'price_asc' => '(so_luong_ton > 0) DESC, gia ASC, id ASC',
    'price_desc' => '(so_luong_ton > 0) DESC, gia DESC, id DESC',
    default => '(so_luong_ton > 0) DESC, id ASC',
};

$sql .= " ORDER BY {$orderBy}";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$productCount = count($products);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tất cả sản phẩm | Thuận Phát Garden</title>
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="sanpham.css?v=20260329-2" />
<link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body data-page="products">
  <nav class="navigation" id="main-nav"></nav>
<script defer src="../mainfont/layout.js?v=20260329-4"></script>
  <script defer src="../mainfont/main.js?v=20260329-2"></script>

  <div class="page-header">
    <h1><?= $searchTerm !== '' ? 'Kết quả tìm kiếm' : 'Tất cả sản phẩm' ?></h1>
    <div class="breadcrumb">
      <a href="../trangchu/index.php">Trang chủ</a> &gt;
      <span>Tất cả sản phẩm</span>
    </div>
    <div class="product-search-summary">
      <?php if ($searchTerm !== ''): ?>
        <div class="product-search-pill">
          <ion-icon name="search-outline" aria-hidden="true"></ion-icon>
          <span>Từ khóa: <strong><?= htmlspecialchars($searchTerm) ?></strong></span>
        </div>
        <span class="product-search-meta"><?= $productCount ?> sản phẩm phù hợp</span>
        <a class="product-search-reset" href="sanpham.php">Xóa tìm kiếm</a>
      <?php else: ?>
        <span class="product-search-meta">Hiện có <?= $productCount ?> sản phẩm</span>
      <?php endif; ?>
    </div>
  </div>

  <main class="container">
    <div class="grid-layout">
      <aside class="sidebar filter-sidebar">
        <h3 class="aside-titles">Lọc sản phẩm</h3>
        <div class="filter-group">
          <ul>
            <li class="filter-item"><label><input type="checkbox" value="0-100000"> Giá dưới 100.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="100000-200000"> 100.000đ - 200.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="200000-300000"> 200.000đ - 300.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="300000-500000"> 300.000đ - 500.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="500000-1000000"> 500.000đ - 1.000.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="1000000-999999999"> Trên 1.000.000đ</label></li>
          </ul>
        </div>
      </aside>

      <section class="product-section">
        <div class="product-toolbar">
          <div class="product-toolbar__label">Sắp xếp theo giá</div>
          <div class="product-sort-controls">
            <a
              class="product-sort-btn <?= $sortBy === 'default' ? 'is-active' : '' ?>"
              href="<?= htmlspecialchars(build_product_page_url($searchTerm, 'default')) ?>">
              Mặc định
            </a>
            <a
              class="product-sort-btn <?= $sortBy === 'price_asc' ? 'is-active' : '' ?>"
              href="<?= htmlspecialchars(build_product_page_url($searchTerm, 'price_asc')) ?>">
              Giá thấp đến cao
            </a>
            <a
              class="product-sort-btn <?= $sortBy === 'price_desc' ? 'is-active' : '' ?>"
              href="<?= htmlspecialchars(build_product_page_url($searchTerm, 'price_desc')) ?>">
              Giá cao đến thấp
            </a>
          </div>
        </div>

        <div class="product-grid" id="product-grid">
          <?php if (empty($products)): ?>
            <div class="product-empty-state">
              <?php if ($searchTerm !== ''): ?>
                <p>Không tìm thấy sản phẩm phù hợp với "<strong><?= htmlspecialchars($searchTerm) ?></strong>".</p>
                <a href="sanpham.php">Xem tất cả sản phẩm</a>
              <?php else: ?>
                <p>Chưa có sản phẩm nào.</p>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <?php foreach ($products as $product): ?>
              <?php
              $pricing = get_product_pricing($product);
              $stock = inventory_quantity($product);
              $isInStock = $stock > 0;
              ?>
              <div class="product-card <?= $isInStock ? '' : 'is-sold-out' ?>" data-price="<?= htmlspecialchars((string) $pricing['price']) ?>">
                <a href="spchitiet.php?id=<?= (int) $product['id'] ?>" class="product-link">
                  <div class="product-image-container">
                    <?php if ($pricing['is_sale']): ?>
                      <span class="sale-badge">-<?= (int) $pricing['discount_percent'] ?>%</span>
                    <?php endif; ?>
                    <?php if (!$isInStock): ?>
                      <span class="stock-badge stock-badge-out">Hết hàng</span>
                    <?php endif; ?>
                    <img
                      class="product-image"
                      src="<?= htmlspecialchars(normalize_public_path($product['hinh_chinh'])) ?>"
                      alt="<?= htmlspecialchars($product['ten_sp']) ?>"
                      onerror="this.onerror=null;this.src='../images/avatar.png';">
                  </div>
                </a>
                <div class="product-content">
                  <div class="product-title"><?= htmlspecialchars($product['ten_sp']) ?></div>
                  <div class="product-price">
                    <span class="current-price"><?= htmlspecialchars(format_product_currency($pricing['price'])) ?></span>
                    <?php if ($pricing['is_sale']): ?>
                      <span class="old-price"><?= htmlspecialchars(format_product_currency($pricing['original_price'])) ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="product-stock-note <?= $isInStock ? '' : 'is-out' ?>">
                    <?= $isInStock ? 'Còn ' . $stock . ' sản phẩm' : 'Sản phẩm đang tạm hết hàng' ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </main>

  <footer class="site-footer" id="site-footer"></footer>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var checkboxes = document.querySelectorAll(".filter-item input[type='checkbox']");
      var cards = document.querySelectorAll(".product-card");

      function filterProducts() {
        var ranges = Array.prototype.slice.call(checkboxes)
          .filter(function (checkbox) { return checkbox.checked; })
          .map(function (checkbox) { return checkbox.value.split("-").map(Number); });

        cards.forEach(function (card) {
          var price = Number(card.getAttribute("data-price"));
          var visible = ranges.length === 0 || ranges.some(function (range) {
            return price >= range[0] && price <= range[1];
          });
          card.style.display = visible ? "" : "none";
        });
      }

      checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", filterProducts);
      });
    });
  </script>
</body>

</html>
