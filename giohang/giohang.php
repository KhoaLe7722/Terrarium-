<?php
session_start();
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

function redirect_with_message(string $type, string $message, string $path = 'giohang.php'): void
{
    $_SESSION['cart_flash'] = [
        'type' => $type,
        'message' => $message,
    ];

    header('Location: ' . $path);
    exit;
}

function fetch_or_create_cart_id(PDO $conn, int $userId): int
{
    $stmt = $conn->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $cartId = (int) $stmt->fetchColumn();

    if ($cartId > 0) {
        return $cartId;
    }

    $stmt = $conn->prepare('INSERT INTO carts (user_id) VALUES (?)');
    $stmt->execute([$userId]);

    return (int) $conn->lastInsertId();
}

function fetch_cart_items(PDO $conn, int $cartId): array
{
    $stmt = $conn->prepare("\n        SELECT\n            ci.product_id,\n            ci.so_luong,\n            p.ten_sp,\n            p.gia,\n            p.tinh_trang,\n            p.hinh_chinh\n        FROM cart_items ci\n        INNER JOIN products p ON p.id = ci.product_id\n        WHERE ci.cart_id = ?\n        ORDER BY ci.id DESC\n    ");
    $stmt->execute([$cartId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

  function sync_cart_quantities(PDO $conn, int $cartId, array $quantities): void
  {
    $updateStmt = $conn->prepare('UPDATE cart_items SET so_luong = ? WHERE cart_id = ? AND product_id = ?');
    $deleteStmt = $conn->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?');

    foreach ($quantities as $productIdRaw => $qtyRaw) {
      $productId = (int) $productIdRaw;
      $qty = (int) $qtyRaw;

      if ($productId <= 0) {
        continue;
      }

      if ($qty <= 0) {
        $deleteStmt->execute([$cartId, $productId]);
        continue;
      }

      $qty = min(99, $qty);
      $updateStmt->execute([$qty, $cartId, $productId]);
    }
  }

if (empty($_SESSION['user_id'])) {
    header('Location: ../dangky_dangnhap/dangnhap.php?redirect=cart');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$cartId = fetch_or_create_cart_id($conn, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));
    $removeProductId = (int) ($_POST['remove_product_id'] ?? 0);

    try {
        if ($removeProductId > 0) {
            $stmt = $conn->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?');
            $stmt->execute([$cartId, $removeProductId]);
            redirect_with_message('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        if ($action === 'add' || $action === 'add_and_checkout') {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $quantity = max(1, min(99, (int) ($_POST['quantity'] ?? 1)));

            if ($productId <= 0) {
                redirect_with_message('error', 'Sản phẩm không hợp lệ.');
            }

            $productStmt = $conn->prepare('SELECT id, tinh_trang FROM products WHERE id = ? LIMIT 1');
            $productStmt->execute([$productId]);
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || ($product['tinh_trang'] ?? '') !== 'con_hang') {
                redirect_with_message('error', 'Sản phẩm không còn khả dụng.');
            }

            $itemStmt = $conn->prepare('SELECT so_luong FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1');
            $itemStmt->execute([$cartId, $productId]);
            $existingQty = $itemStmt->fetchColumn();

            if ($existingQty !== false) {
                $newQty = min(99, (int) $existingQty + $quantity);
                $updateStmt = $conn->prepare('UPDATE cart_items SET so_luong = ? WHERE cart_id = ? AND product_id = ?');
                $updateStmt->execute([$newQty, $cartId, $productId]);
            } else {
                $insertStmt = $conn->prepare('INSERT INTO cart_items (cart_id, product_id, so_luong) VALUES (?, ?, ?)');
                $insertStmt->execute([$cartId, $productId, $quantity]);
            }

            if ($action === 'add_and_checkout') {
                $_SESSION['checkout_selected_product_ids'] = [$productId];
                header('Location: ../thanhtoan/thanhtoan.php');
                exit;
            }

            $returnTo = trim((string) ($_POST['return_to'] ?? ''));
            if ($returnTo !== '') {
                header('Location: ' . $returnTo);
                exit;
            }

            redirect_with_message('success', 'Đã thêm sản phẩm vào giỏ hàng.');
        }

        if ($action === 'update') {
            $quantities = $_POST['quantities'] ?? [];
            if (!is_array($quantities)) {
                $quantities = [];
            }

          sync_cart_quantities($conn, $cartId, $quantities);

            redirect_with_message('success', 'Đã cập nhật giỏ hàng.');
        }

        if ($action === 'sync') {
          $quantities = $_POST['quantities'] ?? [];
          if (!is_array($quantities)) {
            $quantities = [];
          }

          sync_cart_quantities($conn, $cartId, $quantities);

          header('Location: giohang.php');
          exit;
        }

        if ($action === 'remove') {
            $productId = (int) ($_POST['product_id'] ?? 0);
            if ($productId > 0) {
                $stmt = $conn->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?');
                $stmt->execute([$cartId, $productId]);
            }

            redirect_with_message('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        if ($action === 'remove_selected') {
            $selectedIds = $_POST['selected_ids'] ?? [];
            if (!is_array($selectedIds) || $selectedIds === []) {
                redirect_with_message('error', 'Vui lòng chọn sản phẩm cần xóa.');
            }

            $selectedIds = array_values(array_filter(array_map('intval', $selectedIds), static fn ($id) => $id > 0));
            if ($selectedIds === []) {
                redirect_with_message('error', 'Dữ liệu sản phẩm không hợp lệ.');
            }

            $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
            $params = array_merge([$cartId], $selectedIds);
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id IN ($placeholders)");
            $stmt->execute($params);

            redirect_with_message('success', 'Đã xóa sản phẩm đã chọn.');
        }

        if ($action === 'checkout') {
          $quantities = $_POST['quantities'] ?? [];
          if (is_array($quantities) && $quantities !== []) {
            sync_cart_quantities($conn, $cartId, $quantities);
          }

            $selectedIds = $_POST['selected_ids'] ?? [];
            if (!is_array($selectedIds) || $selectedIds === []) {
                redirect_with_message('error', 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.');
            }

            $selectedIds = array_values(array_filter(array_map('intval', $selectedIds), static fn ($id) => $id > 0));
            if ($selectedIds === []) {
                redirect_with_message('error', 'Dữ liệu sản phẩm thanh toán không hợp lệ.');
            }

            $_SESSION['checkout_selected_product_ids'] = $selectedIds;
            header('Location: ../thanhtoan/thanhtoan.php');
            exit;
        }

        redirect_with_message('error', 'Hành động không hợp lệ.');
    } catch (Throwable $exception) {
        redirect_with_message('error', 'Có lỗi xảy ra: ' . $exception->getMessage());
    }
}

$flash = $_SESSION['cart_flash'] ?? null;
unset($_SESSION['cart_flash']);

$items = fetch_cart_items($conn, $cartId);
$grandTotal = 0;

foreach ($items as &$item) {
    $item['so_luong'] = (int) $item['so_luong'];
    $item['gia'] = (float) $item['gia'];
    $item['line_total'] = $item['gia'] * $item['so_luong'];
    $item['image_url'] = '../' . public_asset_path((string) ($item['hinh_chinh'] ?? ''));
    $grandTotal += $item['line_total'];
}
unset($item);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Giỏ hàng | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
  <link rel="stylesheet" href="giohangchitiet.css?v=20260322-17">
</head>

<body data-page="cart">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js?v=20260318-2"></script>
  <script defer src="../mainfont/main.js?v=20260322-1"></script>

  <main class="cart-container">
    <section class="cart-card" id="cart-popup">
      <h2 class="shop-title">Shop Terrarium</h2>

      <?php if ($flash): ?>
        <div class="flash-message <?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
      <?php endif; ?>

      <div class="cart-head-row">
        <div>Sản phẩm</div>
        <div>Giá</div>
        <div>SL</div>
        <div>Tổng</div>
        <div>Thao tác</div>
      </div>

      <?php if ($items === []): ?>
        <div class="empty-cart">
          Giỏ hàng của bạn đang trống. <a href="../sanpham/sanpham.php">Mua sắm ngay</a>
        </div>
      <?php else: ?>
        <form method="post" class="checkout-form">
          <div class="cart-items" id="items">
            <?php foreach ($items as $item): ?>
              <article class="cart-item-card">
                <div class="item-main">
                  <label class="row-checkbox-wrap" title="Chọn sản phẩm">
                    <input class="row-checkbox" type="checkbox" name="selected_ids[]" value="<?= (int) $item['product_id'] ?>" checked>
                  </label>
                  <div class="product">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars((string) $item['ten_sp']) ?>" onerror="this.onerror=null;this.src='../images/avatar.png';">
                    <div>
                      <div class="product-name"><?= htmlspecialchars((string) $item['ten_sp']) ?></div>
                      <div class="product-sub">Mã SP: #<?= (int) $item['product_id'] ?></div>
                    </div>
                  </div>
                </div>

                <div class="col-price">
                  <span class="price-text"><?= htmlspecialchars(format_currency_vnd($item['gia'])) ?></span>
                </div>

                <div class="col-qty">
                  <input
                    class="qty-input-control"
                    type="number"
                    min="0"
                    max="99"
                    name="quantities[<?= (int) $item['product_id'] ?>]"
                    value="<?= (int) $item['so_luong'] ?>">
                </div>

                <div class="col-total">
                  <span class="line-total"><?= htmlspecialchars(format_currency_vnd($item['line_total'])) ?></span>
                </div>

                <div class="col-action">
                  <button class="remove-btn" type="submit" name="remove_product_id" value="<?= (int) $item['product_id'] ?>" title="Xóa">
                    ×
                  </button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="cart-bottom-wrap">
            <div class="cart-bottom-bar">
              <label class="select-all">
                <input type="checkbox" id="all" checked>
                <span>Chọn tất cả</span>
              </label>

              <div class="total-price">
                Tổng: <span id="total"><?= htmlspecialchars(format_currency_vnd($grandTotal)) ?></span>
              </div>

              <button class="checkout" type="submit" name="action" value="checkout">Thanh toán</button>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </section>
  </main>

  <script>
    (function() {
      var allCheckbox = document.getElementById('all');
      var rowCheckboxes = document.querySelectorAll('.row-checkbox');
      var cartForm = document.querySelector('.checkout-form');
      var qtyInputs = document.querySelectorAll('.qty-input-control');

      if (!allCheckbox || rowCheckboxes.length === 0) {
        return;
      }

      allCheckbox.addEventListener('change', function() {
        for (var i = 0; i < rowCheckboxes.length; i += 1) {
          rowCheckboxes[i].checked = allCheckbox.checked;
        }
      });

      for (var i = 0; i < rowCheckboxes.length; i += 1) {
        rowCheckboxes[i].addEventListener('change', function() {
          for (var j = 0; j < rowCheckboxes.length; j += 1) {
            if (!rowCheckboxes[j].checked) {
              allCheckbox.checked = false;
              return;
            }
          }
          allCheckbox.checked = true;
        });
      }

      if (!cartForm || qtyInputs.length === 0) {
        return;
      }

      function submitSync() {
        var actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'sync';
        cartForm.appendChild(actionInput);
        cartForm.submit();
      }

      for (var k = 0; k < qtyInputs.length; k += 1) {
        qtyInputs[k].setAttribute('data-prev', qtyInputs[k].value);

        qtyInputs[k].addEventListener('change', function() {
          var qty = parseInt(this.value, 10);

          if (isNaN(qty)) {
            qty = 1;
          }

          if (qty < 0) {
            qty = 0;
          }

          if (qty > 99) {
            qty = 99;
          }

          this.value = String(qty);

          if (this.getAttribute('data-prev') === this.value) {
            return;
          }

          this.setAttribute('data-prev', this.value);
          submitSync();
        });
      }
    })();
  </script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
