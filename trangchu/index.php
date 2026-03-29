<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

$stmt = $conn->query("
    SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, so_luong_ton
    FROM products
    WHERE so_luong_ton > 0 AND id <> 8
    ORDER BY id ASC
");
$featuredProducts = $stmt->fetchAll();
$featuredPayload = array_map(
  static function (array $product): array {
    $pricing = get_product_pricing($product);

    return [
      'id' => (int) $product['id'],
      'name' => $product['ten_sp'],
      'price' => format_currency_vnd($pricing['price']),
      'originalPrice' => format_currency_vnd($pricing['original_price']),
      'discountPercent' => $pricing['discount_percent'],
      'isSale' => $pricing['is_sale'],
      'image' => normalize_public_path($product['hinh_chinh']),
      'href' => '../sanpham/spchitiet.php?id=' . (int) $product['id'],
    ];
  },
  $featuredProducts
);

$saleStmt = $conn->query("
    SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, so_luong_ton
    FROM products
    WHERE so_luong_ton > 0
      AND id <> 8
      AND (giam_gia_phan_tram > 0 OR (gia_goc IS NOT NULL AND gia_goc > gia))
    ORDER BY giam_gia_phan_tram DESC, (COALESCE(gia_goc, gia) - gia) DESC, id DESC
    LIMIT 3
");
$saleProducts = $saleStmt->fetchAll();

$homeNews = [
  [
    'title' => '🌿 Cây Cảnh Trồng Trong Nhà: Vừa Đẹp Vừa Tốt Cho Sức Khỏe',
    'image' => '../tintuc/img__tintuc/16.jpg',
    'href' => '../tintuc/detail.php?id=16',
    'meta' => 'Tin tức nổi bật',
    'excerpt' => 'Trồng cây cảnh trong nhà không chỉ giúp không gian sống xanh mát hơn mà còn hỗ trợ lọc không khí, giảm căng thẳng và tạo cảm giác thư thái mỗi ngày.',
  ],
  [
    'title' => '🌱 Chất lượng sản phẩm đảm bảo – Từ vườn đến tay khách',
    'image' => '../tintuc/img__tintuc/14.jpg',
    'href' => '../tintuc/detail.php?id=14',
    'meta' => 'Cam kết chất lượng',
    'excerpt' => 'Từ khâu chọn giống, chăm sóc đến đóng gói và giao cây, Thuận Phát Garden luôn kiểm soát kỹ từng công đoạn để sản phẩm đến tay khách hàng trong trạng thái tốt nhất.',
  ],
  [
    'title' => '🌿 Thuận Phát Garden – Cây Kiểng Đẹp, Thiết Kế Sáng Tạo Giữa Lòng Cần Thơ',
    'image' => '../tintuc/img__tintuc/13.jpg',
    'href' => '../tintuc/detail.php?id=13',
    'meta' => 'Không gian & cảm hứng',
    'excerpt' => 'Những thiết kế cây kiểng tại Thuận Phát Garden được chăm chút theo bố cục riêng, mang thiên nhiên đến gần hơn với nhà ở, quán cà phê và không gian làm việc hiện đại.',
  ],
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Terrarium Cần Thơ | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
  <link rel="stylesheet" href="index.css?v=20260316-11" />
  <style>
    .home-featured-wrap {
      max-width: 1280px;
      margin: 50px auto;
      padding: 0 22px;
      text-align: center;
    }

    .home-featured-shell {
      padding: 40px;
      border-radius: 32px;
      background: linear-gradient(135deg, #ffffff 0%, #f7fbf7 100%);
      border: 1px solid rgba(84, 121, 74, 0.14);
      box-shadow: 0 18px 38px rgba(32, 52, 25, 0.08);
      text-align: center;
    }

    .home-featured-wrap h2 {
      text-align: center;
      font-size: 32px;
      color: #000;
      margin-bottom: 40px;
      font-weight: bold;
    }

    .home-featured-wrap h2::after {
      content: "";
      display: block;
      width: 300px;
      height: 4px;
      background-color: #54794a;
      margin: 10px auto 0;
      border-radius: 2px;
    }

    .home-featured-empty {
      padding: 40px 0;
      color: #666;
    }

    .home-featured-carousel {
      position: relative;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .home-featured-window {
      width: 100%;
      overflow: hidden;
      touch-action: pan-y;
    }

    .home-featured-track {
      --home-featured-card-width: 280px;
      display: flex;
      gap: 18px;
      align-items: stretch;
      will-change: transform;
    }

    .home-featured-track.is-compact-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .home-featured-card {
      flex: 0 0 var(--home-featured-card-width);
      max-width: var(--home-featured-card-width);
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 12px 26px rgba(27, 44, 20, 0.12);
      padding: 12px;
      overflow: hidden;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .home-featured-card:hover {
      transform: translateY(-6px);
    }

    .home-featured-card a {
      display: flex;
      flex-direction: column;
      height: 100%;
      text-decoration: none;
    }

    .home-featured-image {
      position: relative;
      width: 100%;
      height: 228px;
      margin: 0 auto;
      border-radius: 20px;
      overflow: hidden;
      background: linear-gradient(180deg, #f8fbf4 0%, #ffffff 100%);
    }

    .home-featured-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 58px;
      padding: 8px 12px;
      border-radius: 999px;
      background: linear-gradient(135deg, #ff4d4f 0%, #c81e1e 100%);
      color: #fff;
      font-size: 0.9rem;
      font-weight: 700;
      line-height: 1;
      box-shadow: 0 12px 22px rgba(200, 30, 30, 0.26);
    }

    .home-featured-card img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 20px;
    }

    .home-featured-info {
      display: flex;
      flex: 1;
      flex-direction: column;
      gap: 8px;
      padding-top: 10px;
    }

    .home-featured-card h3 {
      margin: 0;
      font-size: 1rem;
      line-height: 1.35;
      color: #333;
      min-height: 2.7em;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .home-featured-price {
      margin-top: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: baseline;
      justify-content: center;
    }

    .home-featured-price-current {
      color: #2c6d31;
      font-size: 0.98rem;
      font-weight: 700;
    }

    .home-featured-price-old {
      color: #8b9590;
      font-size: 0.88rem;
      text-decoration: line-through;
    }

    .home-featured-arrow {
      width: 44px;
      height: 44px;
      border: none;
      border-radius: 50%;
      background: #54794a;
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      flex: 0 0 44px;
      box-shadow: 0 8px 16px rgba(84, 121, 74, 0.22);
      transition: transform 0.2s ease, background 0.2s ease;
    }

    .home-featured-arrow:not(:disabled):hover {
      background: #6a965f;
      transform: translateY(-2px);
    }

    .home-featured-arrow:disabled,
    .home-featured-arrow.is-disabled {
      opacity: 0.45;
      cursor: default;
      box-shadow: none;
      background: #92ab8a;
      transform: none;
    }

    .home-featured-arrow ion-icon {
      font-size: 22px;
    }

    @media (max-width: 1200px) {
      .home-featured-wrap {
        padding: 0 18px;
      }

      .home-featured-shell {
        padding: 34px 26px;
      }

      .home-featured-track {
        gap: 16px;
      }

      .home-featured-image {
        height: 220px;
      }
    }

    @media (max-width: 900px) {
      .home-featured-wrap {
        padding: 0 15px;
        margin: 30px auto;
      }

      .home-featured-shell {
        padding: 28px 18px;
        border-radius: 26px;
      }

      .home-featured-wrap h2 {
        font-size: 26px;
        margin-bottom: 25px;
      }

      .home-featured-wrap h2::after {
        width: 200px;
      }

      .home-featured-carousel {
        gap: 0;
        padding: 0 18px;
      }

      .home-featured-track {
        gap: 14px;
      }

      .home-featured-card {
        padding: 10px;
        border-radius: 16px;
      }

      .home-featured-image {
        height: 210px;
        border-radius: 18px;
      }

      .home-featured-arrow {
        position: absolute;
        top: 50%;
        width: 38px;
        height: 38px;
        flex-basis: auto;
        transform: translateY(-50%);
        z-index: 1;
      }

      #home-featured-prev {
        left: 0;
      }

      #home-featured-next {
        right: 0;
      }

      .home-featured-arrow:not(:disabled):hover {
        transform: translateY(-50%);
      }
    }

    @media (max-width: 575px) {
      .home-featured-wrap {
        padding: 0 10px;
        margin: 20px auto;
      }

      .home-featured-shell {
        padding: 22px 14px;
        border-radius: 22px;
      }

      .home-featured-wrap h2 {
        font-size: 22px;
        margin-bottom: 20px;
      }

      .home-featured-wrap h2::after {
        width: 150px;
        height: 3px;
      }

      .home-featured-carousel {
        padding: 0 10px;
      }

      .home-featured-track {
        gap: 10px;
      }

      .home-featured-image {
        height: 190px;
      }

      .home-featured-arrow {
        width: 34px;
        height: 34px;
      }
    }

    @media (max-width: 480px) {
      .home-featured-wrap {
        max-width: 430px;
        padding: 0 8px;
      }

      .home-featured-shell {
        padding: 18px 12px;
        border-radius: 20px;
      }

      .home-featured-wrap h2 {
        font-size: 20px;
        margin-bottom: 16px;
      }

      .home-featured-carousel {
        padding: 0 8px;
      }

      .home-featured-track.is-compact-grid {
        gap: 10px;
      }

      .home-featured-track.is-compact-grid .home-featured-card {
        width: 100%;
        max-width: none;
        min-width: 0;
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 8px 18px rgba(27, 44, 20, 0.11);
      }

      .home-featured-track.is-compact-grid .home-featured-card:hover {
        transform: none;
      }

      .home-featured-track.is-compact-grid .home-featured-image {
        height: clamp(80px, 21vw, 92px);
        border-radius: 14px;
      }

      .home-featured-track.is-compact-grid .home-featured-card img {
        border-radius: 14px;
      }

      .home-featured-track.is-compact-grid .home-featured-info {
        gap: 6px;
        padding-top: 8px;
      }

      .home-featured-track.is-compact-grid .home-featured-card h3 {
        min-height: 2.5em;
        font-size: 0.82rem;
        line-height: 1.25;
      }

      .home-featured-track.is-compact-grid .home-featured-price {
        gap: 6px;
      }

      .home-featured-track.is-compact-grid .home-featured-price-current {
        font-size: 0.84rem;
      }

      .home-featured-track.is-compact-grid .home-featured-price-old {
        font-size: 0.74rem;
      }

      .home-featured-track.is-compact-grid .home-featured-badge {
        min-width: 44px;
        padding: 6px 8px;
        font-size: 0.72rem;
      }

      .home-featured-arrow {
        width: 30px;
        height: 30px;
      }

      .home-featured-arrow ion-icon {
        font-size: 18px;
      }
    }

    .home-about-section {
      max-width: 1280px;
      margin: 42px auto 18px;
      padding: 0 22px;
    }

    .home-about-shell {
      display: grid;
      grid-template-columns: minmax(0, 1.06fr) minmax(320px, 0.94fr);
      gap: 42px;
      align-items: center;
      padding: 40px;
      border-radius: 32px;
      background: linear-gradient(135deg, #ffffff 0%, #f7fbf7 100%);
      border: 1px solid rgba(84, 121, 74, 0.14);
      box-shadow: 0 18px 38px rgba(32, 52, 25, 0.08);
    }

    .home-about-copy {
      min-width: 0;
    }

    .home-about-quote {
      display: inline-flex;
      margin-bottom: 10px;
      font-size: 92px;
      line-height: 0.82;
      font-weight: 700;
      color: #102019;
    }

    .home-about-copy h2 {
      margin: 0 0 18px;
      color: #2b322b;
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      line-height: 1.05;
    }

    .home-about-text {
      --collapsed-height: 272px;
      position: relative;
      color: #394539;
      font-size: clamp(1rem, 1.2vw, 1.08rem);
      line-height: 1.72;
      text-align: justify;
    }

    .home-about-text p {
      margin: 0 0 16px;
    }

    .home-about-text.is-ready {
      overflow: hidden;
      max-height: var(--collapsed-height);
      transition: max-height 0.38s ease;
    }

    .home-about-text.is-ready:not(.is-expanded)::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      height: 96px;
      background: linear-gradient(180deg, rgba(247, 251, 247, 0) 0%, #f7fbf7 100%);
      pointer-events: none;
    }

    .home-about-actions {
      margin-top: 26px;
    }

    .home-about-toggle {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 24px;
      border: none;
      border-radius: 18px;
      background: #102019;
      color: #fff;
      font: inherit;
      font-size: 1.02rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 14px 28px rgba(16, 32, 25, 0.18);
      transition: background 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
    }

    .home-about-toggle:hover {
      background: #1a3024;
      transform: translateY(-2px);
      box-shadow: 0 18px 30px rgba(16, 32, 25, 0.2);
    }

    .home-about-toggle ion-icon {
      font-size: 19px;
      transition: transform 0.22s ease;
    }

    .home-about-toggle[aria-expanded="true"] ion-icon {
      transform: rotate(90deg);
    }

    .home-about-media {
      position: relative;
      padding: 24px 0 0 24px;
      min-width: 0;
    }

    .home-about-media::before {
      content: "";
      position: absolute;
      top: 0;
      right: 0;
      width: 78%;
      height: 70%;
      border-radius: 30px;
      background: #e6efe8;
    }

    .home-about-media img {
      position: relative;
      display: block;
      width: 100%;
      aspect-ratio: 4 / 3.15;
      object-fit: cover;
      border-radius: 28px;
      box-shadow: 0 18px 34px rgba(35, 57, 34, 0.16);
    }

    @media (max-width: 1024px) {
      .home-about-section {
        margin: 34px auto 14px;
      }

      .home-about-shell {
        gap: 30px;
        padding: 32px;
        grid-template-columns: 1fr;
      }

      .home-about-text {
        --collapsed-height: 232px;
      }

      .home-about-media {
        width: 100%;
        max-width: 720px;
        margin: 0 auto;
        padding: 16px 0 0 16px;
      }
    }

    @media (max-width: 575px) {
      .home-about-section {
        margin: 26px auto 10px;
        padding: 0 10px;
      }

      .home-about-shell {
        padding: 24px 18px;
        border-radius: 24px;
      }

      .home-about-quote {
        font-size: 72px;
      }

      .home-about-copy h2 {
        margin-bottom: 14px;
      }

      .home-about-text {
        --collapsed-height: 204px;
        font-size: 1rem;
        line-height: 1.65;
      }

      .home-about-toggle {
        width: 100%;
        border-radius: 16px;
      }

      .home-about-media {
        padding: 12px 0 0 12px;
      }

      .home-about-media::before {
        width: 82%;
        height: 62%;
        border-radius: 22px;
      }

      .home-about-media img {
        border-radius: 20px;
        aspect-ratio: 4 / 3.6;
      }
    }

    .home-sale-section {
      max-width: 1280px;
      margin: 22px auto 18px;
      padding: 0 22px;
    }

    .home-sale-shell {
      padding: 34px 40px 38px;
      border-radius: 32px;
      background: linear-gradient(135deg, #ffffff 0%, #f7fbf7 100%);
      border: 1px solid rgba(84, 121, 74, 0.14);
      box-shadow: 0 18px 38px rgba(32, 52, 25, 0.08);
    }

    .home-sale-heading {
      margin-bottom: 24px;
    }

    .home-sale-heading a {
      position: relative;
      display: inline-block;
      text-decoration: none;
      color: #1f2f23;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      font-weight: 700;
      line-height: 1.12;
    }

    .home-sale-heading a::after {
      content: "";
      display: block;
      width: 160px;
      height: 4px;
      margin-top: 10px;
      border-radius: 999px;
      background: #54794a;
    }

    .home-sale-content {
      display: grid;
      grid-template-columns: minmax(0, 0.96fr) minmax(0, 1.04fr);
      gap: 22px;
      align-items: stretch;
    }

    .home-sale-hero {
      display: block;
      min-height: 100%;
      border-radius: 28px;
      overflow: hidden;
      background: #edf3ed;
      box-shadow: 0 12px 26px rgba(31, 47, 35, 0.08);
    }

    .home-sale-hero img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .home-sale-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      align-items: stretch;
    }

    .home-sale-card a,
    .home-sale-more {
      display: flex;
      flex-direction: column;
      height: 100%;
      padding: 14px;
      border-radius: 22px;
      background: #ffffff;
      border: 1px solid rgba(84, 121, 74, 0.12);
      box-shadow: 0 12px 24px rgba(31, 47, 35, 0.08);
      text-decoration: none;
      transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .home-sale-card a:hover,
    .home-sale-more:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 28px rgba(31, 47, 35, 0.12);
      border-color: rgba(84, 121, 74, 0.22);
    }

    .home-sale-thumb {
      position: relative;
      aspect-ratio: 1 / 1;
      border-radius: 18px;
      overflow: hidden;
      background: #eef4ee;
    }

    .home-sale-thumb img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: #fff;
    }

    .home-sale-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 1;
      padding: 6px 10px;
      border-radius: 999px;
      background: #c2410c;
      color: #fff;
      font-size: 0.82rem;
      font-weight: 700;
      box-shadow: 0 10px 18px rgba(194, 65, 12, 0.18);
    }

    .home-sale-info {
      padding-top: 12px;
      text-align: left;
    }

    .home-sale-title {
      margin: 0 0 10px;
      color: #223126;
      font-size: 0.98rem;
      font-weight: 700;
      line-height: 1.38;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .home-sale-price {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: baseline;
    }

    .home-sale-price-current {
      color: #2c6d31;
      font-size: 1rem;
      font-weight: 700;
    }

    .home-sale-price-old {
      color: #8b9590;
      font-size: 0.88rem;
      text-decoration: line-through;
    }

    .home-sale-more {
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: #102019;
      color: #fff;
      text-align: center;
      font-size: 1.02rem;
      font-weight: 700;
    }

    .home-sale-more-logo {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.12);
      font-size: 1.5rem;
      font-weight: 700;
    }

    .home-sale-more ion-icon {
      font-size: 22px;
    }

    .home-sale-empty {
      grid-column: 1 / -1;
      padding: 24px;
      border-radius: 22px;
      background: #ffffff;
      border: 1px dashed rgba(84, 121, 74, 0.3);
      color: #536455;
      line-height: 1.6;
    }

    @media (max-width: 1200px) {
      .home-sale-section {
        padding: 0 18px;
      }

      .home-sale-shell {
        padding: 30px 26px 34px;
      }
    }

    @media (max-width: 900px) {
      .home-sale-section {
        padding: 0 15px;
        margin: 18px auto 14px;
      }

      .home-sale-shell {
        padding: 28px 18px 30px;
        border-radius: 26px;
      }

      .home-sale-content {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 575px) {
      .home-sale-section {
        padding: 0 10px;
      }

      .home-sale-shell {
        padding: 22px 14px 24px;
        border-radius: 22px;
      }

      .home-sale-grid {
        gap: 14px;
      }

      .home-sale-card a,
      .home-sale-more {
        padding: 12px;
        border-radius: 18px;
      }

      .home-sale-thumb {
        border-radius: 14px;
      }
    }

    @media (max-width: 480px) {
      .home-sale-section {
        padding: 0 8px;
      }

      .home-sale-shell {
        padding: 18px 12px 20px;
        border-radius: 20px;
      }

      .home-sale-grid {
        grid-template-columns: 1fr;
      }
    }

    .home-news-section {
      max-width: 1280px;
      margin: 22px auto 18px;
      padding: 0 22px;
    }

    .home-news-shell {
      padding: 34px 40px 38px;
      border-radius: 32px;
      background: linear-gradient(135deg, #ffffff 0%, #f7fbf7 100%);
      border: 1px solid rgba(84, 121, 74, 0.14);
      box-shadow: 0 18px 38px rgba(32, 52, 25, 0.08);
    }

    .home-news-heading {
      margin-bottom: 24px;
    }

    .home-news-heading a {
      position: relative;
      display: inline-block;
      text-decoration: none;
      color: #1f2f23;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      font-weight: 700;
      line-height: 1.12;
    }

    .home-news-heading a::after {
      content: "";
      display: block;
      width: 120px;
      height: 4px;
      margin-top: 10px;
      border-radius: 999px;
      background: #54794a;
    }

    .home-news-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .home-news-item {
      height: 100%;
    }

    .home-news-card {
      display: grid;
      grid-template-columns: 112px minmax(0, 1fr);
      gap: 16px;
      align-items: center;
      height: 100%;
      padding: 16px;
      border-radius: 24px;
      background: #ffffff;
      border: 1px solid rgba(84, 121, 74, 0.12);
      box-shadow: 0 12px 24px rgba(31, 47, 35, 0.08);
      text-decoration: none;
      transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .home-news-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 28px rgba(31, 47, 35, 0.12);
      border-color: rgba(84, 121, 74, 0.22);
    }

    .home-news-thumb {
      width: 100%;
      aspect-ratio: 1 / 1;
      border-radius: 18px;
      overflow: hidden;
      background: #edf3ed;
    }

    .home-news-thumb img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .home-news-body {
      min-width: 0;
    }

    .home-news-title {
      margin: 0 0 6px;
      color: #223126;
      font-size: 1.05rem;
      font-weight: 700;
      line-height: 1.36;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .home-news-meta {
      margin: 0 0 8px;
      color: #6b776d;
      font-size: 0.88rem;
      font-weight: 600;
    }

    .home-news-excerpt {
      margin: 0;
      color: #4d5c50;
      font-size: 0.96rem;
      line-height: 1.55;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    @media (max-width: 1200px) {
      .home-news-section {
        padding: 0 18px;
      }

      .home-news-shell {
        padding: 30px 26px 34px;
      }

      .home-news-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 900px) {
      .home-news-section {
        padding: 0 15px;
        margin: 18px auto 14px;
      }

      .home-news-shell {
        padding: 28px 18px 30px;
        border-radius: 26px;
      }
    }

    @media (max-width: 575px) {
      .home-news-section {
        padding: 0 10px;
      }

      .home-news-shell {
        padding: 22px 14px 24px;
        border-radius: 22px;
      }

      .home-news-grid {
        grid-template-columns: 1fr;
        gap: 14px;
      }

      .home-news-card {
        grid-template-columns: 92px minmax(0, 1fr);
        gap: 14px;
        padding: 14px;
        border-radius: 18px;
      }

      .home-news-thumb {
        border-radius: 14px;
      }

      .home-news-title {
        font-size: 1rem;
      }

      .home-news-excerpt {
        font-size: 0.92rem;
        -webkit-line-clamp: 3;
      }
    }

    @media (max-width: 480px) {
      .home-news-section {
        padding: 0 8px;
      }

      .home-news-shell {
        padding: 18px 12px 20px;
        border-radius: 20px;
      }

      .home-news-heading {
        margin-bottom: 18px;
      }
    }
  </style>
</head>

<body data-page="home">
  <nav class="navigation" id="main-nav"></nav>
<script defer src="../mainfont/layout.js?v=20260329-4"></script>
  <script defer src="../mainfont/main.js?v=20260329-2"></script>

  <div class="slider">
    <div class="slides">
      <div class="slider-arrow prev"><ion-icon name="arrow-back-circle-outline"></ion-icon></div>
      <div class="slider-arrow next"><ion-icon name="arrow-forward-circle-outline"></ion-icon></div>
      <a href="../gioithieu/gioithieu.php">
        <img src="../images/trangchu/TERRAIUM (1).png" alt="Giới thiệu terrarium" />
      </a>
      <a href="../sanpham/sanpham.php">
        <img src="../images/trangchu/TERRAIUM2.png" alt="Bộ sưu tập terrarium" />
      </a>
      <a href="../tintuc/tintuc.php">
        <img src="../images/trangchu/TERRAIUM3.png" alt="Tin tức terrarium" />
      </a>
      <a href="../tintuc/tintuc.php">
        <img src="../images/trangchu/Terrarium – nghệ thuật xanh xóa nhòa khoảng cách thế hệ, kết nối mọi lứa tuổi bằng tình yêu thiên nhiên..png" alt="Terrarium nghệ thuật xanh" />
      </a>
      <a href="../huongdan/huongdan.php">
        <img src="../images/trangchu/TERRAIUM 5.png" alt="Hướng dẫn chăm sóc terrarium" />
      </a>
      <a href="../gioithieu/taisaochon.php">
        <img src="../images/trangchu/Terrarium – chill có gu, sống có chất..png" alt="Terrarium chill có gu" />
      </a>
    </div>
  </div>

  <div class="dots">
    <span class="dot active"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
  </div>

  <script defer src="index.js?v=20260316-11"></script>

  <section class="home-about-section" aria-labelledby="home-about-title">
    <div class="home-about-shell">
      <div class="home-about-copy">
        <span class="home-about-quote" aria-hidden="true">“</span>
        <h2 id="home-about-title">Về chúng tôi</h2>
        <div class="home-about-text" id="home-about-text">
          <p>Thuận Phát Garden bắt đầu từ tình yêu dành cho terrarium, cây kiểng và những góc xanh nhỏ có thể làm dịu nhịp sống mỗi ngày. Từ những chậu cây mini đến các bố cục terrarium hoàn chỉnh, chúng tôi luôn muốn mang thiên nhiên đến gần hơn với từng căn phòng, bàn làm việc và không gian sống.</p>
          <p>Điều chúng tôi theo đuổi không chỉ là một sản phẩm đẹp mắt, mà còn là cảm giác thư thái khi bạn ngắm nhìn một hệ sinh thái thu nhỏ được chăm chút kỹ lưỡng. Mỗi thiết kế đều được lựa chọn cây, phối nền, sắp bố cục và hoàn thiện theo hướng bền vững, dễ chăm sóc và phù hợp với lối sống hiện đại.</p>
          <p>Thuận Phát Garden dành cho những người yêu thiên nhiên, muốn tìm một món quà có chiều sâu, hay đơn giản là cần một góc bình yên giữa guồng sống bận rộn. Chúng tôi tin rằng khi sống gần cây hơn, con người cũng sẽ sống chậm lại, cân bằng hơn và trân trọng không gian của mình hơn.</p>
          <p>Chúng tôi xây dựng văn hóa làm việc dựa trên sự chỉn chu, tinh thần học hỏi và trách nhiệm với từng sản phẩm gửi đến khách hàng. Từ khâu tư vấn, thiết kế đến hướng dẫn chăm sóc sau khi mua, đội ngũ luôn cố gắng đồng hành để bạn không chỉ sở hữu một chậu cây đẹp, mà còn thật sự tận hưởng hành trình sống cùng thiên nhiên.</p>
        </div>
        <div class="home-about-actions">
          <button class="home-about-toggle" id="home-about-toggle" type="button" aria-expanded="false" aria-controls="home-about-text">
            <span class="home-about-toggle-label">Xem thêm</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </button>
        </div>
      </div>
      <div class="home-about-media">
        <img src="../images/trangchu/504020108_634604676270376_4864280801946020309_n.jpg" alt="Khách tham quan terrarium tại Thuận Phát Garden" loading="lazy" />
      </div>
    </div>
  </section>

  <section class="home-sale-section" aria-labelledby="home-sale-title">
    <div class="home-sale-shell">
      <h2 class="home-sale-heading" id="home-sale-title">
        <a href="../sanpham/sanpham.php" title="Giảm giá">Giảm giá</a>
      </h2>
      <div class="home-sale-content">
        <a class="home-sale-hero" href="../sanpham/sanpham.php" title="Giảm giá">
          <img src="../images/trangchu/giamgia.png" alt="Giảm giá" loading="lazy" />
        </a>
        <div class="home-sale-grid">
          <?php if (empty($saleProducts)): ?>
            <div class="home-sale-empty">
              Hiện chưa có sản phẩm giảm giá. Bạn có thể xem thêm các mẫu terrarium đang mở bán tại Thuận Phát Garden.
            </div>
          <?php else: ?>
            <?php foreach ($saleProducts as $saleProduct): ?>
              <?php
              $salePricing = get_product_pricing($saleProduct);
              $saleOriginalPrice = $salePricing['original_price'];
              $saleCurrentPrice = $salePricing['price'];
              $salePercent = $salePricing['discount_percent'];
              ?>
              <article class="home-sale-card">
                <a href="../sanpham/spchitiet.php?id=<?= (int) $saleProduct['id'] ?>" title="<?= htmlspecialchars($saleProduct['ten_sp'], ENT_QUOTES, 'UTF-8') ?>">
                  <div class="home-sale-thumb">
                    <?php if ($salePercent > 0): ?>
                      <span class="home-sale-badge">-<?= $salePercent ?>%</span>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars(normalize_public_path($saleProduct['hinh_chinh']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($saleProduct['ten_sp'], ENT_QUOTES, 'UTF-8') ?>" onerror="this.onerror=null;this.src='../images/avatar.png';" />
                  </div>
                  <div class="home-sale-info">
                    <h3 class="home-sale-title"><?= htmlspecialchars($saleProduct['ten_sp'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <div class="home-sale-price">
                      <span class="home-sale-price-current"><?= htmlspecialchars(format_currency_vnd($saleCurrentPrice), ENT_QUOTES, 'UTF-8') ?></span>
                      <?php if ($saleOriginalPrice > $saleCurrentPrice): ?>
                        <span class="home-sale-price-old"><?= htmlspecialchars(format_currency_vnd($saleOriginalPrice), ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
          <a class="home-sale-more" href="../sanpham/sanpham.php" title="Xem thêm terrarium">
            <span class="home-sale-more-logo">%</span>
            <span>Xem thêm</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="home-news-section" id="m_blog" aria-labelledby="home-news-title">
    <div class="home-news-shell">
      <h2 class="home-news-heading" id="home-news-title">
        <a href="../tintuc/tintuc.php" title="Tin tức">Tin tức</a>
      </h2>
      <div class="home-news-grid">
        <?php foreach ($homeNews as $article): ?>
          <div class="home-news-item">
            <a class="home-news-card" href="<?= htmlspecialchars($article['href'], ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
              <span class="home-news-thumb">
                <img src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" />
              </span>
              <div class="home-news-body">
                <h3 class="home-news-title"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <div class="home-news-meta"><?= htmlspecialchars($article['meta'], ENT_QUOTES, 'UTF-8') ?></div>
                <p class="home-news-excerpt"><?= htmlspecialchars($article['excerpt'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="home-featured-wrap">
    <div class="home-featured-shell">
      <h2>SẢN PHẨM NỔI BẬT</h2>

      <?php if (empty($featuredProducts)): ?>
        <div class="home-featured-empty">
          Chưa có sản phẩm nào. Hãy thêm sản phẩm từ khu admin để bắt đầu bán hàng.
        </div>
      <?php else: ?>
        <div class="home-featured-carousel">
          <button class="home-featured-arrow" id="home-featured-prev" type="button" aria-label="Sản phẩm trước">
            <ion-icon name="chevron-back-outline"></ion-icon>
          </button>
          <div class="home-featured-window">
            <div class="home-featured-track" id="home-featured-track"></div>
          </div>
          <button class="home-featured-arrow" id="home-featured-next" type="button" aria-label="Sản phẩm tiếp theo">
            <ion-icon name="chevron-forward-outline"></ion-icon>
          </button>
        </div>
        <script>
          window.homeFeaturedProducts = <?= json_encode($featuredPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        </script>
      <?php endif; ?>
    </div>
  </section>

  <footer class="site-footer" id="site-footer"></footer>

  <script>
    (function() {
      var aboutText = document.getElementById("home-about-text");
      var aboutToggle = document.getElementById("home-about-toggle");
      var aboutLabel = aboutToggle ? aboutToggle.querySelector(".home-about-toggle-label") : null;
      var aboutResizeTimer = null;
      var isExpanded = false;

      if (!aboutText || !aboutToggle || !aboutLabel) {
        return;
      }

      function getCollapsedHeight() {
        return parseInt(window.getComputedStyle(aboutText).getPropertyValue("--collapsed-height"), 10) || 240;
      }

      function applyState(expanded) {
        var collapsedHeight = getCollapsedHeight();
        var fullHeight = aboutText.scrollHeight;

        aboutText.classList.add("is-ready");

        if (fullHeight <= collapsedHeight + 12) {
          aboutText.classList.add("is-expanded");
          aboutText.style.maxHeight = "none";
          aboutToggle.hidden = true;
          return;
        }

        aboutToggle.hidden = false;
        aboutText.classList.toggle("is-expanded", expanded);
        aboutText.style.maxHeight = (expanded ? fullHeight : collapsedHeight) + "px";
        aboutToggle.setAttribute("aria-expanded", expanded ? "true" : "false");
        aboutLabel.textContent = expanded ? "Thu gọn" : "Xem thêm";
      }

      aboutToggle.addEventListener("click", function() {
        isExpanded = !isExpanded;
        applyState(isExpanded);
      });

      window.addEventListener("resize", function() {
        window.clearTimeout(aboutResizeTimer);
        aboutResizeTimer = window.setTimeout(function() {
          applyState(isExpanded);
        }, 120);
      });

      applyState(false);
    })();

    (function() {
      var products = window.homeFeaturedProducts || [];
      var track = document.getElementById("home-featured-track");
      var prevButton = document.getElementById("home-featured-prev");
      var nextButton = document.getElementById("home-featured-next");
      var viewport = document.querySelector(".home-featured-window");
      var resizeTimer = null;
      var touchStartX = 0;
      var touchDeltaX = 0;

      if (!track || products.length === 0) {
        return;
      }

      var currentStart = 0;

      function getViewportWidth() {
        if (viewport && viewport.clientWidth) {
          return viewport.clientWidth;
        }

        return track.clientWidth || window.innerWidth || 0;
      }

      function shouldUseCompactGrid() {
        return getViewportWidth() <= 480;
      }

      function getCardsPerView() {
        var width = getViewportWidth();

        if (shouldUseCompactGrid()) {
          return 4;
        }

        if (width < 880) {
          return 2;
        }

        if (width < 1000) {
          return 3;
        }

        return 4;
      }

      function getTrackGap() {
        var width = getViewportWidth();

        if (shouldUseCompactGrid()) {
          return 10;
        }

        if (width <= 575) {
          return 12;
        }

        if (width <= 900) {
          return 14;
        }

        if (width <= 1200) {
          return 16;
        }

        return 18;
      }

      function getVisibleCount() {
        return Math.min(getCardsPerView(), products.length);
      }

      function updateLayout(cardCount) {
        var gap = getTrackGap();
        var totalGap = Math.max(cardCount - 1, 0) * gap;
        var availableWidth = getViewportWidth();
        var isCompactGrid = shouldUseCompactGrid();
        var cardWidth = availableWidth > 0
          ? Math.floor((availableWidth - totalGap) / cardCount)
          : 280;

        track.style.gap = gap + "px";
        track.classList.toggle("is-compact-grid", isCompactGrid);

        if (isCompactGrid) {
          track.style.setProperty("--home-featured-card-width", "100%");
          return;
        }

        track.style.setProperty("--home-featured-card-width", cardWidth + "px");
      }

      function getStepSize() {
        if (shouldUseCompactGrid()) {
          return getVisibleCount();
        }

        return 1;
      }

      function updateControls(cardCount) {
        var disableControls = products.length <= cardCount;

        [prevButton, nextButton].forEach(function(button) {
          if (!button) {
            return;
          }

          button.disabled = disableControls;
          button.classList.toggle("is-disabled", disableControls);
        });
      }

      function escapeHtml(value) {
        return String(value)
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#39;");
      }

      function buildCard(product) {
        var badgeHtml = product.isSale
          ? '<span class="home-featured-badge">-' + escapeHtml(String(product.discountPercent)) + '%</span>'
          : '';
        var oldPriceHtml = product.isSale
          ? '<span class="home-featured-price-old">' + escapeHtml(product.originalPrice) + '</span>'
          : '';

        return '' +
          '<article class="home-featured-card">' +
          '<a href="' + escapeHtml(product.href) + '">' +
          '<div class="home-featured-image">' +
          badgeHtml +
          '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '" onerror="this.onerror=null;this.src=\'../images/avatar.png\';">' +
          '</div>' +
          '<div class="home-featured-info">' +
          '<h3>' + escapeHtml(product.name) + '</h3>' +
          '<div class="home-featured-price">' +
          '<span class="home-featured-price-current">' + escapeHtml(product.price) + '</span>' +
          oldPriceHtml +
          '</div>' +
          '</div>' +
          '</a>' +
          '</article>';
      }

      function render() {
        var cardCount = getVisibleCount();
        var html = "";

        updateLayout(cardCount);

        for (var i = 0; i < cardCount; i += 1) {
          html += buildCard(products[(currentStart + i) % products.length]);
        }

        track.innerHTML = html;
        track.style.transition = "none";
        track.style.transform = "translateX(0)";
        updateControls(cardCount);
      }

      function showNext() {
        if (products.length <= getVisibleCount()) {
          return;
        }

        currentStart = (currentStart + getStepSize()) % products.length;
        render();
      }

      function showPrevious() {
        if (products.length <= getVisibleCount()) {
          return;
        }

        currentStart = (currentStart - getStepSize() + products.length) % products.length;
        render();
      }

      function resetTouch() {
        touchStartX = 0;
        touchDeltaX = 0;
      }

      if (prevButton) {
        prevButton.addEventListener("click", function() {
          showPrevious();
        });
      }

      if (nextButton) {
        nextButton.addEventListener("click", function() {
          showNext();
        });
      }

      if (viewport) {
        viewport.addEventListener("touchstart", function(event) {
          if (event.touches.length !== 1) {
            return;
          }

          touchStartX = event.touches[0].clientX;
          touchDeltaX = 0;
        }, {
          passive: true
        });

        viewport.addEventListener("touchmove", function(event) {
          if (!touchStartX || event.touches.length !== 1) {
            return;
          }

          touchDeltaX = event.touches[0].clientX - touchStartX;
        }, {
          passive: true
        });

        viewport.addEventListener("touchend", function() {
          if (Math.abs(touchDeltaX) >= 45) {
            if (touchDeltaX < 0) {
              showNext();
            } else {
              showPrevious();
            }
          }

          resetTouch();
        });

        viewport.addEventListener("touchcancel", resetTouch);
      }

      window.addEventListener("resize", function() {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(function() {
          render();
        }, 120);
      });

      render();
    })();
  </script>
</body>

</html>
















