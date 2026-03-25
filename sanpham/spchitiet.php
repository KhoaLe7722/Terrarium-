<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

function u(string $value): string
{
  return json_decode('"' . $value . '"', true);
}

function detail_excerpt(string $html, int $limit = 220): string
{
  $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

  if ($text === '') {
    return u('Thi\u1ebft k\u1ebf ti\u1ec3u c\u1ea3nh n\u00e0y ph\u00f9 h\u1ee3p \u0111\u1ec3 l\u00e0m \u0111i\u1ec3m nh\u1ea5n cho b\u00e0n l\u00e0m vi\u1ec7c, k\u1ec7 s\u00e1ch ho\u1eb7c kh\u00f4ng gian s\u1ed1ng c\u1ea7n th\u00eam m\u1ed9t m\u1ea3ng xanh nh\u1eb9 nh\u00e0ng.');
  }

  if (function_exists('mb_strlen') && function_exists('mb_substr')) {
    return mb_strlen($text, 'UTF-8') > $limit
      ? rtrim(mb_substr($text, 0, $limit, 'UTF-8')) . '...'
      : $text;
  }

  return strlen($text) > $limit ? rtrim(substr($text, 0, $limit)) . '...' : $text;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  header('Location: sanpham.php');
  exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
  header('Location: sanpham.php');
  exit;
}

$gallery = load_product_gallery($conn, $id);
$relatedProducts = array_values(array_filter(
  load_latest_products($conn, 8, $id),
  static function (array $item): bool {
    $name = trim((string) ($item['ten_sp'] ?? ''));
    if ($name === '') {
      return false;
    }

    $normalized = function_exists('mb_strtolower')
      ? mb_strtolower($name, 'UTF-8')
      : strtolower($name);

    return !preg_match('/(?:led|lamp)/', $normalized);
  }
));
$relatedProducts = array_slice($relatedProducts, 0, 4);
$pricing = get_product_pricing($product);
$price = $pricing['price'];
$originalPrice = $pricing['original_price'];
$isSale = $pricing['is_sale'];
$discountPercent = $pricing['discount_percent'];
$stockAvailable = inventory_quantity($product);
$isInStock = product_is_in_stock($product);
$sku = 'SP-' . str_pad((string) $product['id'], 3, '0', STR_PAD_LEFT);
$summary = detail_excerpt($product['mo_ta'] ?? '', 210);
$metaDescription = detail_excerpt($product['mo_ta'] ?? '', 155);
$mainImagePath = public_asset_path($product['hinh_chinh']);

$galleryItems = [];
$seenImages = [];
$imageCandidates = array_merge([
  ['duong_dan' => $mainImagePath],
], $gallery);

foreach ($imageCandidates as $candidate) {
  $assetPath = public_asset_path($candidate['duong_dan'] ?? '');
  if (isset($seenImages[$assetPath])) {
    continue;
  }

  $seenImages[$assetPath] = true;
  $galleryItems[] = [
    'path' => $assetPath,
    'url' => '../' . $assetPath,
  ];
}

if (empty($galleryItems)) {
  $galleryItems[] = [
    'path' => 'images/avatar.png',
    'url' => '../images/avatar.png',
  ];
}

$mainImageUrl = $galleryItems[0]['url'];
$galleryUrls = array_map(static fn(array $item): string => $item['url'], $galleryItems);
$totalImages = count($galleryItems);
$savedAmount = max(0, $originalPrice - $price);

$careTips = [
  [
    'icon' => 'leaf-outline',
    'title' => u('\u0110\u1eb7t n\u01a1i s\u00e1ng d\u1ecbu'),
    'text' => u('\u01afu ti\u00ean \u00e1nh s\u00e1ng t\u00e1n x\u1ea1 ho\u1eb7c g\u1ea7n c\u1eeda s\u1ed5, tr\u00e1nh n\u1eafng g\u1eaft gi\u1eefa tr\u01b0a \u0111\u1ec3 ti\u1ec3u c\u1ea3nh gi\u1eef m\u00e0u xanh \u1ed5n \u0111\u1ecbnh.'),
  ],
  [
    'icon' => 'water-outline',
    'title' => u('T\u01b0\u1edbi v\u1eeba \u0111\u1ee7'),
    'text' => u('Ki\u1ec3m tra b\u1ec1 m\u1eb7t gi\u00e1 th\u1ec3 tr\u01b0\u1edbc khi t\u01b0\u1edbi. Gi\u1eef \u0111\u1ed9 \u1ea9m c\u00e2n b\u1eb1ng \u0111\u1ec3 c\u00e2y kh\u1ecfe m\u00e0 kh\u00f4ng b\u1ecb \u00fang.'),
  ],
  [
    'icon' => 'cube-outline',
    'title' => u('\u0110\u00f3ng g\u00f3i c\u1ea9n th\u1eadn'),
    'text' => u('S\u1ea3n ph\u1ea9m \u0111\u01b0\u1ee3c chu\u1ea9n b\u1ecb ch\u1eafc tay, ph\u00f9 h\u1ee3p \u0111\u1ec3 trang tr\u00ed b\u00e0n l\u00e0m vi\u1ec7c ho\u1eb7c l\u00e0m qu\u00e0 t\u1eb7ng tinh t\u1ebf.'),
  ],
];

$featureHighlights = [
  ['label' => u('Th\u01b0\u01a1ng hi\u1ec7u'), 'value' => u('Thu\u1eadn Ph\u00e1t Garden')],
  ['label' => u('M\u00e3 s\u1ea3n ph\u1ea9m'), 'value' => $sku],
  ['label' => u('Ph\u00f9 h\u1ee3p'), 'value' => u('B\u00e0n l\u00e0m vi\u1ec7c, k\u1ec7 s\u00e1ch, qu\u00e0 t\u1eb7ng')],
  ['label' => u('T\u1ed3n kho'), 'value' => $isInStock ? $stockAvailable . ' ' . u('s\u1ea3n ph\u1ea9m') : u('0 s\u1ea3n ph\u1ea9m')],
  [
    'label' => u('T\u00ecnh tr\u1ea1ng'),
    'value' => $isInStock ? u('C\u00f2n h\u00e0ng') : u('T\u1ea1m h\u1ebft h\u00e0ng'),
    'class' => $isInStock ? 'detail-stock-status is-in-stock' : 'detail-stock-status is-out-stock',
  ],
];

$featuredArticles = [
  [
    'title' => u('C\u00e2y c\u1ea3nh v\u00e0 phong th\u1ee7y - B\u00ed quy\u1ebft l\u00e0m gi\u00e0u t\u1eeb c\u00e2y ki\u1ec3ng'),
    'href' => '../tintuc/detail.php?id=5',
    'image' => '../tintuc/img__tintuc/5.jpg',
    'label' => u('G\u00f3c s\u1ed1ng xanh'),
  ],
  [
    'title' => u('Tr\u1ed3ng c\u00e2y c\u1ea3nh trong nh\u00e0 - M\u1eb9o ch\u0103m s\u00f3c c\u00e2y ki\u1ec3ng'),
    'href' => '../tintuc/detail.php?id=6',
    'image' => '../tintuc/img__tintuc/6.jpg',
    'label' => u('Ch\u0103m s\u00f3c'),
  ],
  [
    'title' => u('Nh\u1eefng lo\u1ea1i c\u00e2y bonsai \u0111\u1eb9p v\u00e0 d\u1ec5 tr\u1ed3ng'),
    'href' => '../tintuc/detail.php?id=7',
    'image' => '../tintuc/img__tintuc/7.jpg',
    'label' => u('C\u1ea3m h\u1ee9ng trang tr\u00ed'),
  ],
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title><?= htmlspecialchars($product['ten_sp']) ?> | <?= htmlspecialchars(u('Thu\u1eadn Ph\u00e1t Garden')) ?></title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../mainfont/main.css?v=20260324-6" />
  <link rel="stylesheet" href="./spchitiet.css?v=20260324-10" />
</head>

<body data-page="products">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js?v=20260324-9"></script>
  <script defer src="../mainfont/main.js?v=20260324-6"></script>

  <main class="detail-page">
    <section class="detail-hero">
      <div class="detail-shell">
        <div class="detail-breadcrumb-wrap">
          <a href="../trangchu/index.php"><?= htmlspecialchars(u('Trang ch\u1ee7')) ?></a>
          <span>/</span>
          <a href="./sanpham.php"><?= htmlspecialchars(u('S\u1ea3n ph\u1ea9m')) ?></a>
          <span>/</span>
          <strong><?= htmlspecialchars($product['ten_sp']) ?></strong>
        </div>

        <div class="detail-hero-grid">
          <section class="detail-gallery-card">
            <?php if ($discountPercent > 0): ?>
              <div class="detail-sale-badge">-<?= (int) $discountPercent ?>%</div>
            <?php endif; ?>

            <div class="detail-gallery-stage">
              <button
                class="detail-gallery-nav <?= $totalImages < 2 ? 'is-hidden' : '' ?>"
                type="button"
                data-gallery-nav="prev"
                aria-label="<?= htmlspecialchars(u('\u1ea2nh tr\u01b0\u1edbc')) ?>">
                <ion-icon name="chevron-back-outline"></ion-icon>
              </button>

              <figure class="detail-main-figure">
                <img
                  src="<?= htmlspecialchars($mainImageUrl) ?>"
                  alt="<?= htmlspecialchars($product['ten_sp']) ?>"
                  class="detail-main-image"
                  id="main-image"
                  onerror="this.onerror=null;this.src='../images/avatar.png';">
              </figure>

              <button
                class="detail-gallery-nav <?= $totalImages < 2 ? 'is-hidden' : '' ?>"
                type="button"
                data-gallery-nav="next"
                aria-label="<?= htmlspecialchars(u('\u1ea2nh ti\u1ebfp theo')) ?>">
                <ion-icon name="chevron-forward-outline"></ion-icon>
              </button>
            </div>

            <div class="detail-thumb-strip" id="detail-thumbs">
              <?php foreach ($galleryItems as $index => $image): ?>
                <button
                  class="detail-thumb <?= $index === 0 ? 'is-active' : '' ?>"
                  type="button"
                  data-image-index="<?= (int) $index ?>"
                  aria-label="<?= htmlspecialchars(u('Xem \u1ea3nh')) ?> <?= (int) ($index + 1) ?>">
                  <img
                    src="<?= htmlspecialchars($image['url']) ?>"
                    alt="<?= htmlspecialchars($product['ten_sp']) ?>"
                    onerror="this.onerror=null;this.src='../images/avatar.png';">
                </button>
              <?php endforeach; ?>
            </div>
          </section>

          <aside class="detail-buy-card">
            <h1 class="detail-title"><?= htmlspecialchars($product['ten_sp']) ?></h1>
            <p class="detail-summary"><?= htmlspecialchars($summary) ?></p>

            <div class="detail-meta-grid">
              <?php foreach ($featureHighlights as $item): ?>
                <div class="detail-meta-item">
                  <span><?= htmlspecialchars($item['label']) ?></span>
                  <strong<?= !empty($item['class']) ? ' class="' . htmlspecialchars($item['class']) . '"' : '' ?>><?= htmlspecialchars($item['value']) ?></strong>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="detail-price-panel">
              <div class="detail-price-row">
                <span class="detail-current-price"><?= htmlspecialchars(format_currency_vnd($price)) ?></span>
                <?php if ($isSale): ?>
                  <span class="detail-old-price"><?= htmlspecialchars(format_currency_vnd($originalPrice)) ?></span>
                <?php endif; ?>
              </div>
              <?php if ($isSale): ?>
                <p class="detail-saving"><?= htmlspecialchars(u('B\u1ea1n \u0111ang ti\u1ebft ki\u1ec7m')) ?> <?= htmlspecialchars(format_currency_vnd($savedAmount)) ?> <?= htmlspecialchars(u('cho m\u1eabu n\u00e0y.')) ?></p>
              <?php endif; ?>
            </div>

            <div class="detail-service-strip">
              <div>
                <ion-icon name="shield-checkmark-outline"></ion-icon>
                <span><?= htmlspecialchars(u('Ki\u1ec3m tra k\u1ef9 tr\u01b0\u1edbc khi giao')) ?></span>
              </div>
              <div>
                <ion-icon name="gift-outline"></ion-icon>
                <span><?= htmlspecialchars(u('\u0110\u1eb9p \u0111\u1ec3 t\u1eb7ng, g\u1ecdn \u0111\u1ec3 tr\u01b0ng b\u00e0y')) ?></span>
              </div>
            </div>

            <div class="detail-qty-card">
              <div>
                <span class="detail-qty-label"><?= htmlspecialchars(u('S\u1ed1 l\u01b0\u1ee3ng')) ?></span>
                <p class="detail-qty-note">
                  <?= $isInStock
                    ? htmlspecialchars(u('B\u1ea1n c\u00f3 th\u1ec3 \u0111i\u1ec1u ch\u1ec9nh nhanh ngay t\u1ea1i \u0111\u00e2y. T\u1ed1i \u0111a ') . $stockAvailable . u(' s\u1ea3n ph\u1ea9m.'))
                    : htmlspecialchars(u('S\u1ea3n ph\u1ea9m \u0111ang t\u1ea1m h\u1ebft h\u00e0ng.')) ?>
                </p>
              </div>
              <div class="detail-quantity-control">
                <button class="detail-qty-btn" type="button" data-qty-change="-1" <?= $isInStock ? '' : 'disabled' ?>>-</button>
                <input type="text" value="<?= $isInStock ? '1' : '0' ?>" class="detail-qty-input" id="qty-input" readonly>
                <button class="detail-qty-btn" type="button" data-qty-change="1" <?= $isInStock ? '' : 'disabled' ?>>+</button>
              </div>
            </div>

            <div class="detail-action-group">
              <?php if ($isInStock): ?>
                <button type="button" class="detail-btn detail-btn-secondary" data-add-cart>
                  <ion-icon name="bag-add-outline"></ion-icon>
                  <span><?= htmlspecialchars(u('Th\u00eam v\u00e0o gi\u1ecf')) ?></span>
                </button>
                <button type="button" class="detail-btn detail-btn-primary" data-buy-now>
                  <ion-icon name="flash-outline"></ion-icon>
                  <span><?= htmlspecialchars(u('Mua ngay')) ?></span>
                </button>
              <?php else: ?>
                <button type="button" class="detail-btn detail-btn-disabled" disabled>
                  <ion-icon name="alert-circle-outline"></ion-icon>
                  <span><?= htmlspecialchars(u('T\u1ea1m h\u1ebft h\u00e0ng')) ?></span>
                </button>
              <?php endif; ?>
            </div>

          </aside>
        </div>
      </div>
    </section>

    <section class="detail-content-section">
      <div class="detail-shell detail-content-grid">
        <div class="detail-main-column">
          <section class="detail-tabs-card">
            <div class="detail-tab-buttons" role="tablist" aria-label="<?= htmlspecialchars(u('N\u1ed9i dung s\u1ea3n ph\u1ea9m')) ?>">
              <button class="detail-tab-button is-active" type="button" data-tab-target="description" role="tab" aria-selected="true"><?= htmlspecialchars(u('Th\u00f4ng tin chi ti\u1ebft')) ?></button>
              <button class="detail-tab-button" type="button" data-tab-target="care" role="tab" aria-selected="false"><?= htmlspecialchars(u('Ch\u0103m s\u00f3c & giao h\u00e0ng')) ?></button>
            </div>

            <div class="detail-tab-panel is-active" id="tab-description" role="tabpanel">
              <div class="detail-copy">
                <?= $product['mo_ta'] ?: '<p>' . htmlspecialchars(u('S\u1ea3n ph\u1ea9m \u0111ang \u0111\u01b0\u1ee3c c\u1eadp nh\u1eadt m\u00f4 t\u1ea3 chi ti\u1ebft.')) . '</p>' ?>
              </div>
            </div>

            <div class="detail-tab-panel" id="tab-care" role="tabpanel" hidden>
              <div class="detail-care-grid">
                <?php foreach ($careTips as $tip): ?>
                  <article class="detail-care-card">
                    <div class="detail-care-icon">
                      <ion-icon name="<?= htmlspecialchars($tip['icon']) ?>"></ion-icon>
                    </div>
                    <h3><?= htmlspecialchars($tip['title']) ?></h3>
                    <p><?= htmlspecialchars($tip['text']) ?></p>
                  </article>
                <?php endforeach; ?>
              </div>

              <div class="detail-care-note">
                <h3><?= htmlspecialchars(u('G\u1ee3i \u00fd khi nh\u1eadn h\u00e0ng')) ?></h3>
                <ul>
                  <li><?= htmlspecialchars(u('M\u1edf h\u1ed9p nh\u1eb9 tay v\u00e0 \u0111\u1eb7t s\u1ea3n ph\u1ea9m \u1edf b\u1ec1 m\u1eb7t ph\u1eb3ng, tho\u00e1ng kh\u00ed.')) ?></li>
                  <li><?= htmlspecialchars(u('\u01afu ti\u00ean v\u1ecb tr\u00ed c\u00f3 \u00e1nh s\u00e1ng t\u1ef1 nhi\u00ean d\u1ecbu \u0111\u1ec3 m\u00e0u l\u00e1 v\u00e0 b\u1ed1 c\u1ee5c gi\u1eef v\u1ebb t\u01b0\u01a1i l\u00e2u.')) ?></li>
                  <li><?= htmlspecialchars(u('N\u1ebfu mua l\u00e0m qu\u00e0 t\u1eb7ng, b\u1ea1n c\u00f3 th\u1ec3 li\u00ean h\u1ec7 tr\u01b0\u1edbc \u0111\u1ec3 \u0111\u01b0\u1ee3c t\u01b0 v\u1ea5n c\u00e1ch tr\u01b0ng b\u00e0y ph\u00f9 h\u1ee3p.')) ?></li>
                </ul>
              </div>
            </div>
          </section>

          <?php if (!empty($relatedProducts)): ?>
            <section class="detail-related-section">
              <div class="detail-section-head">
                <div>
                  <span class="detail-section-kicker"><?= htmlspecialchars(u('B\u1ea1n c\u00f3 th\u1ec3 th\u00edch')) ?></span>
                  <h2><?= htmlspecialchars(u('S\u1ea3n ph\u1ea9m c\u00f9ng phong c\u00e1ch')) ?></h2>
                </div>
                <a href="./sanpham.php"><?= htmlspecialchars(u('Xem to\u00e0n b\u1ed9')) ?></a>
              </div>

              <div class="detail-related-grid">
                <?php foreach ($relatedProducts as $related): ?>
                  <?php $relatedPricing = get_product_pricing($related); ?>
                  <article class="detail-related-card">
                    <a href="spchitiet.php?id=<?= (int) $related['id'] ?>" class="detail-related-link">
                      <div class="detail-related-thumb">
                        <?php if ($relatedPricing['is_sale']): ?>
                          <span class="detail-related-sale">-<?= (int) $relatedPricing['discount_percent'] ?>%</span>
                        <?php endif; ?>
                        <img
                          src="<?= htmlspecialchars(normalize_public_path($related['hinh_chinh'])) ?>"
                          alt="<?= htmlspecialchars($related['ten_sp']) ?>"
                          onerror="this.onerror=null;this.src='../images/avatar.png';">
                      </div>
                      <div class="detail-related-content">
                        <h3><?= htmlspecialchars($related['ten_sp']) ?></h3>
                        <div class="detail-related-price">
                          <strong><?= htmlspecialchars(format_currency_vnd($relatedPricing['price'])) ?></strong>
                          <?php if ($relatedPricing['is_sale']): ?>
                            <span><?= htmlspecialchars(format_currency_vnd($relatedPricing['original_price'])) ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </a>
                  </article>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endif; ?>
        </div>

        <aside class="detail-side-column">
          <section class="detail-side-card detail-side-support">
            <span class="detail-section-kicker"><?= htmlspecialchars(u('H\u1ed7 tr\u1ee3 nhanh')) ?></span>
            <h2><?= htmlspecialchars(u('Ch\u0103m c\u00e2y d\u1ec5 h\u01a1n b\u1ea1n ngh\u0129')) ?></h2>
            <ul>
              <li>
                <ion-icon name="call-outline"></ion-icon>
                <div>
                  <strong><?= htmlspecialchars(u('Hotline t\u01b0 v\u1ea5n')) ?></strong>
                  <a href="tel:0945720038">0945 720 038</a>
                </div>
              </li>
              <li>
                <ion-icon name="document-text-outline"></ion-icon>
                <div>
                  <strong><?= htmlspecialchars(u('H\u01b0\u1edbng d\u1eabn c\u01a1 b\u1ea3n')) ?></strong>
                  <a href="../huongdan/huongdan.php"><?= htmlspecialchars(u('Xem m\u1eb9o ch\u0103m s\u00f3c')) ?></a>
                </div>
              </li>
              <li>
                <ion-icon name="ribbon-outline"></ion-icon>
                <div>
                  <strong><?= htmlspecialchars(u('Cam k\u1ebft \u0111\u00f3ng g\u00f3i')) ?></strong>
                  <span><?= htmlspecialchars(u('S\u1ea1ch, g\u1ecdn, ch\u1eafc tay tr\u01b0\u1edbc khi giao')) ?></span>
                </div>
              </li>
            </ul>
          </section>

          <section class="detail-side-card detail-side-articles">
            <div class="detail-section-head compact">
              <div>
                <span class="detail-section-kicker"><?= htmlspecialchars(u('Tin n\u1ed5i b\u1eadt')) ?></span>
                <h2><?= htmlspecialchars(u('M\u1eb9o hay cho g\u00f3c xanh')) ?></h2>
              </div>
            </div>

            <div class="detail-article-list">
              <?php foreach ($featuredArticles as $article): ?>
                <a href="<?= htmlspecialchars($article['href']) ?>" class="detail-article-item">
                  <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                  <div>
                    <span><?= htmlspecialchars($article['label']) ?></span>
                    <strong><?= htmlspecialchars($article['title']) ?></strong>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </section>
        </aside>
      </div>
    </section>
  </main>

  <?php if ($isInStock): ?>
    <div class="detail-mobile-bar">
      <div class="detail-mobile-price">
        <strong><?= htmlspecialchars(format_currency_vnd($price)) ?></strong>
        <?php if ($isSale): ?>
          <span><?= htmlspecialchars(format_currency_vnd($originalPrice)) ?></span>
        <?php endif; ?>
      </div>
      <div class="detail-mobile-actions">
        <button type="button" class="detail-btn detail-btn-secondary" data-add-cart><?= htmlspecialchars(u('Th\u00eam gi\u1ecf')) ?></button>
        <button type="button" class="detail-btn detail-btn-primary" data-buy-now><?= htmlspecialchars(u('Mua ngay')) ?></button>
      </div>
    </div>
  <?php endif; ?>

  <script src="../giohang/giohang.js?v=20260325-1"></script>
  <script>
    (function () {
      var galleryImages = <?= json_encode($galleryUrls, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
      var currentImageIndex = 0;
      var mainImage = document.getElementById('main-image');
      var thumbButtons = Array.prototype.slice.call(document.querySelectorAll('[data-image-index]'));
      var qtyInput = document.getElementById('qty-input');
      var maxStock = <?= (int) $stockAvailable ?>;
      var tabButtons = Array.prototype.slice.call(document.querySelectorAll('[data-tab-target]'));
      var tabPanels = {
        description: document.getElementById('tab-description'),
        care: document.getElementById('tab-care')
      };

      function setActiveImage(index) {
        if (!galleryImages.length || !mainImage) {
          return;
        }

        if (index < 0) {
          index = galleryImages.length - 1;
        }
        if (index >= galleryImages.length) {
          index = 0;
        }

        currentImageIndex = index;
        mainImage.src = galleryImages[index];

        thumbButtons.forEach(function (button, buttonIndex) {
          button.classList.toggle('is-active', buttonIndex === index);
        });
      }

      function getQuantity() {
        if (!qtyInput || maxStock <= 0) {
          if (qtyInput) {
            qtyInput.value = 0;
          }
          return 0;
        }

        var value = parseInt(qtyInput.value, 10) || 1;
        if (value < 1) {
          value = 1;
        }
        if (value > maxStock) {
          value = maxStock;
        }
        qtyInput.value = value;
        return value;
      }

      function changeQty(delta) {
        if (maxStock <= 0) {
          return;
        }
        qtyInput.value = getQuantity() + delta;
        getQuantity();
      }

      function addCurrentProduct(redirectToCart) {
        if (typeof window.addToCart !== 'function' || maxStock <= 0) {
          return Promise.resolve(false);
        }

        return window.addToCart({
          id: <?= (int) $product['id'] ?>,
          name: <?= json_encode($product['ten_sp'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
          price: <?= json_encode($price) ?>,
          quantity: getQuantity(),
          image: <?= json_encode($mainImagePath, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
          stock: maxStock
        }).then(function (added) {
          if (added && redirectToCart) {
            window.location.href = '../giohang/giohang.php';
          }
          return added;
        });
      }

      thumbButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          setActiveImage(Number(button.getAttribute('data-image-index')) || 0);
        });
      });

      Array.prototype.slice.call(document.querySelectorAll('[data-gallery-nav]')).forEach(function (button) {
        button.addEventListener('click', function () {
          var direction = button.getAttribute('data-gallery-nav') === 'next' ? 1 : -1;
          setActiveImage(currentImageIndex + direction);
        });
      });

      Array.prototype.slice.call(document.querySelectorAll('[data-qty-change]')).forEach(function (button) {
        button.addEventListener('click', function () {
          changeQty(Number(button.getAttribute('data-qty-change')) || 0);
        });
      });

      Array.prototype.slice.call(document.querySelectorAll('[data-add-cart]')).forEach(function (button) {
        button.addEventListener('click', function () {
          addCurrentProduct(false);
        });
      });

      Array.prototype.slice.call(document.querySelectorAll('[data-buy-now]')).forEach(function (button) {
        button.addEventListener('click', function () {
          addCurrentProduct(true);
        });
      });

      tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          var target = button.getAttribute('data-tab-target');

          tabButtons.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
            item.setAttribute('aria-selected', item === button ? 'true' : 'false');
          });

          Object.keys(tabPanels).forEach(function (key) {
            var panel = tabPanels[key];
            if (!panel) {
              return;
            }
            var isActive = key === target;
            panel.classList.toggle('is-active', isActive);
            panel.hidden = !isActive;
          });
        });
      });

      setActiveImage(0);
      getQuantity();
    })();
  </script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>










