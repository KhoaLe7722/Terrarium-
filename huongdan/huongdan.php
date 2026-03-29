<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

function guide_format_price(float|int|string $amount): string
{
    if (function_exists('format_currency_vnd')) {
        return format_currency_vnd($amount);
    }

    return number_format((float) $amount, 0, ',', '.') . 'đ';
}

function guide_product_image_path(?array $product, string $fallback = '../images/avatar.png'): string
{
    if (!$product || empty($product['hinh_chinh'])) {
        return $fallback;
    }

    return normalize_public_path((string) $product['hinh_chinh']);
}

function guide_product_name(?array $product, string $fallback = 'Sản phẩm nổi bật'): string
{
    $name = trim((string) ($product['ten_sp'] ?? ''));
    return $name !== '' ? $name : $fallback;
}

function guide_article_products(array $products): array
{
    $preferred = [];
    $secondary = [];

    foreach ($products as $product) {
        $name = mb_strtolower(trim((string) ($product['ten_sp'] ?? '')), 'UTF-8');
        if ($name === '') {
            continue;
        }

        if (str_contains($name, 'đèn') || str_contains($name, 'led')) {
            continue;
        }

        if (
            str_contains($name, 'terrarium')
            || str_contains($name, 'rêu')
            || str_contains($name, 'cây')
            || str_contains($name, 'sen đá')
        ) {
            $preferred[] = $product;
        } else {
            $secondary[] = $product;
        }
    }

    return array_values(array_merge($preferred, $secondary));
}

function guide_pick_product(array $products, int $index): ?array
{
    return $products[$index] ?? ($products[0] ?? null);
}

$rawProducts = [];
if (function_exists('load_latest_products')) {
    try {
        $rawProducts = load_latest_products($conn, 8);
    } catch (Throwable $exception) {
        $rawProducts = [];
    }
}

$featuredProducts = guide_article_products($rawProducts);
$coverProduct = guide_pick_product($featuredProducts, 0);
$problemProduct = guide_pick_product($featuredProducts, 1);
$wateringProduct = guide_pick_product($featuredProducts, 2);
$placementProduct = guide_pick_product($featuredProducts, 3);
$healthyProduct = guide_pick_product($featuredProducts, 4);

$articleDate = '29/03/2026';
$articleTitle = 'Làm Sao Để Terrarium Luôn Xanh Và Không Bị Mốc? Bí Quyết Chăm Sóc Bền Lâu';
$articleDescription = 'Hướng dẫn chăm terrarium đúng cách cho người mới: kiểm soát độ ẩm, tưới nước vừa đủ, chọn vị trí đặt bình và xử lý nấm mốc để terrarium luôn xanh bền lâu.';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= htmlspecialchars($articleDescription) ?>" />
  <title><?= htmlspecialchars($articleTitle) ?> | Thuận Phát Garden</title>
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
  <link rel="stylesheet" href="huongdan.css?v=20260329-7" />
</head>

<body data-page="guide">
  <nav class="navigation" id="main-nav"></nav>
<script defer src="../mainfont/layout.js?v=20260329-4"></script>
  <script defer src="../mainfont/main.js?v=20260329-2"></script>

  <main class="guide-page">
    <section class="guide-article-page" itemscope itemtype="https://schema.org/Article">
      <meta itemprop="headline" content="<?= htmlspecialchars($articleTitle) ?>">
      <meta itemprop="description" content="<?= htmlspecialchars($articleDescription) ?>">
      <meta itemprop="datePublished" content="2026-03-29">
      <meta itemprop="dateModified" content="2026-03-29">
      <meta itemprop="author" content="Thuận Phát Garden">
      <meta itemprop="image" content="<?= htmlspecialchars(guide_product_image_path($coverProduct)) ?>">

      <div class="container">
        <nav class="guide-breadcrumbs" aria-label="breadcrumb">
          <a href="../trangchu/index.php">Trang chủ</a>
          <span>/</span>
          <a href="../huongdan/huongdan.php">Hướng dẫn</a>
          <span>/</span>
          <span><?= htmlspecialchars($articleTitle) ?></span>
        </nav>
      </div>

      <div class="container guide-main">
        <div class="guide-layout">
          <article class="guide-post">
            <header class="guide-post__header">
              <span class="guide-post__category">Chăm sóc terrarium</span>
              <h1 class="guide-post__title" itemprop="name"><?= htmlspecialchars($articleTitle) ?></h1>
              <p class="guide-post__meta">Đăng bởi: <strong>Thuận Phát Garden - <?= htmlspecialchars($articleDate) ?></strong></p>
            </header>

            <div class="guide-post__hero">
              <img
                src="<?= htmlspecialchars(guide_product_image_path($coverProduct)) ?>"
                alt="<?= htmlspecialchars(guide_product_name($coverProduct, $articleTitle)) ?>"
                loading="eager"
                onerror="this.onerror=null;this.src='../images/avatar.png';">
            </div>

            <section class="guide-toc" aria-labelledby="guide-toc-title">
              <div class="guide-toc__title-wrap">
                <h2 id="guide-toc-title">Mục lục</h2>
                <span>Đọc nhanh nội dung chính của bài viết</span>
              </div>
              <ol>
                <li><a href="#toc_hieu-ve-vi-sinh-va-do-am">Hiểu Về Vi Sinh Và Độ Ẩm</a></li>
                <li><a href="#toc_1-chon-loai-cay-va-reu-phu-hop">Chọn Loại Cây Và Rêu Phù Hợp</a></li>
                <li><a href="#toc_2-cach-tuoi-terrarium-dung-chuan">Cách Tưới Terrarium Đúng Chuẩn</a></li>
                <li><a href="#toc_3-mo-nap-dinh-ky-tho-de-ngan-ngua-moc">Mở Nắp Định Kỳ - "Thở" Để Ngăn Ngừa Mốc</a></li>
                <li><a href="#toc_4-vi-tri-dat-binh-ly-tuong">Vị Trí Đặt Bình Lý Tưởng</a></li>
                <li><a href="#toc_5-ve-sinh-va-cat-tia-dinh-ky">Vệ Sinh Và Cắt Tỉa Định Kỳ</a></li>
                <li><a href="#toc_terrarium-bi-moc-thi-xu-ly-hoa-chat-duoc-khong">Terrarium Bị Mốc Thì Xử Lý Hóa Chất Được Không?</a></li>
                <li><a href="#toc_co-can-dung-den-chuyen-dung-cho-terrarium">Có Cần Dùng Đèn Chuyên Dụng Cho Terrarium?</a></li>
              </ol>
            </section>

            <div class="guide-rte" itemprop="articleBody">
              <p>Trong thế giới nhỏ bé của một lọ <strong>terrarium</strong>, mỗi nhành rêu, hạt sỏi và giọt sương đều kể một câu chuyện riêng. Nhưng để những câu chuyện ấy luôn tươi mới và không bị gián đoạn bởi mốc trắng hay lá úa vàng, bạn cần nắm vững <strong>cách chăm terrarium</strong> đúng kỹ thuật.</p>
              <p>Nếu bạn yêu những góc xanh tinh tế trong không gian sống, việc chăm sóc terrarium không chỉ là một thao tác kỹ thuật mà còn là một thói quen thư giãn. Chỉ cần hiểu đúng môi trường sống của cây và giữ nhịp chăm đều đặn, bạn hoàn toàn có thể duy trì một khu vườn mini xanh mát trong thời gian dài.</p>

              <h2>Vì Sao Terrarium Bị Mốc Hoặc Không Còn Xanh?</h2>

              <h3 id="toc_hieu-ve-vi-sinh-va-do-am">Hiểu Về Vi Sinh Và Độ Ẩm</h3>
              <p>Mốc trắng, vàng lá hay rêu chuyển nâu đều là dấu hiệu hệ sinh thái bên trong bình đang mất cân bằng. Những nguyên nhân thường gặp nhất là:</p>
              <ul>
                <li><strong>Độ ẩm dư thừa:</strong> Nước đọng quá mức tạo môi trường lý tưởng cho nấm mốc phát triển.</li>
                <li><strong>Thông gió kém:</strong> Bình kín không được mở định kỳ khiến không khí bị tù và cây khó trao đổi khí.</li>
                <li><strong>Chọn sai loại cây:</strong> Dùng cây ưa khô cho terrarium kín hoặc cây chưa xử lý kỹ sẽ làm bình nhanh xuống sức.</li>
              </ul>
              <div class="guide-tip-box">
                <strong>Tip:</strong> Các loại rêu như Java Moss, rêu nhung hoặc rêu trắc bá có khả năng chịu ẩm tốt, rất phù hợp để giữ terrarium xanh lâu hơn.
              </div>

              <figure class="guide-inline-media">
                <img
                  src="<?= htmlspecialchars(guide_product_image_path($problemProduct)) ?>"
                  alt="<?= htmlspecialchars('Minh họa terrarium dễ bị mốc với sản phẩm ' . guide_product_name($problemProduct)) ?>"
                  loading="lazy"
                  onerror="this.onerror=null;this.src='../images/avatar.png';">
                <figcaption>Terrarium có thể bị mốc nếu độ ẩm trong bình luôn ở mức quá cao.</figcaption>
              </figure>

              <h2>5 Bước Chăm Sóc Terrarium Để Luôn Xanh Mát</h2>

              <h3 id="toc_1-chon-loai-cay-va-reu-phu-hop">1. Chọn Loại Cây Và Rêu Phù Hợp</h3>
              <p>Một chiếc bình bền đẹp bắt đầu từ việc chọn đúng "cư dân". Với người mới, nên ưu tiên các dòng cây dễ thích nghi và cho tín hiệu rõ khi cần điều chỉnh môi trường:</p>
              <ul>
                <li><strong>Java Moss:</strong> Ít cần ánh sáng mạnh, dễ sống và giữ màu xanh khá ổn định.</li>
                <li><strong>Rêu sao / rêu nhung:</strong> Giữ ẩm tốt, tạo chiều sâu cho bố cục terrarium.</li>
                <li><strong>Fittonia mini (cẩm nhung):</strong> Lá đẹp, ưa ẩm, rất hợp với terrarium kín.</li>
              </ul>

              <h3 id="toc_2-cach-tuoi-terrarium-dung-chuan">2. Cách Tưới Terrarium Đúng Chuẩn</h3>
              <p>Tưới quá tay là lý do hàng đầu khiến <strong>terrarium bị mốc</strong>. Để bình luôn ổn định, bạn nên tuân thủ các nguyên tắc sau:</p>
              <ul>
                <li><strong>Dùng bình xịt phun sương:</strong> Nước thấm đều mà không gây úng gốc.</li>
                <li><strong>Quan sát thành bình:</strong> Nếu hơi nước đọng dày suốt cả ngày, tuyệt đối không tưới thêm.</li>
                <li><strong>Dấu hiệu cần tưới:</strong> Rêu nhạt màu, nền bắt đầu khô hoặc đất mặt se lại.</li>
              </ul>

              <figure class="guide-inline-media">
                <img
                  src="<?= htmlspecialchars(guide_product_image_path($wateringProduct)) ?>"
                  alt="<?= htmlspecialchars('Minh họa tưới terrarium với sản phẩm ' . guide_product_name($wateringProduct)) ?>"
                  loading="lazy"
                  onerror="this.onerror=null;this.src='../images/avatar.png';">
                <figcaption>Tưới terrarium đúng cách giúp cây đủ ẩm mà không bị úng nền.</figcaption>
              </figure>

              <h3 id="toc_3-mo-nap-dinh-ky-tho-de-ngan-ngua-moc">3. Mở Nắp Định Kỳ - "Thở" Để Ngăn Ngừa Mốc</h3>
              <p>Với terrarium kín, việc mở nắp 1 đến 2 lần mỗi tuần, mỗi lần khoảng 15 đến 30 phút sẽ mang lại hiệu quả rất rõ:</p>
              <ul>
                <li>Giảm nguy cơ nấm mốc tích tụ trong bình.</li>
                <li>Giúp cân bằng độ ẩm và cải thiện trao đổi khí cho cây.</li>
              </ul>

              <h3 id="toc_4-vi-tri-dat-binh-ly-tuong">4. Vị Trí Đặt Bình Lý Tưởng</h3>
              <p><strong>Ánh sáng gián tiếp</strong> là chìa khóa quan trọng nhất. Tránh nắng gắt trực tiếp vì mặt kính sẽ hấp nhiệt mạnh, làm cây nhanh suy và nước bốc hơi quá mức. Hãy đặt bình gần cửa sổ có rèm, trên kệ sáng hoặc bàn làm việc có nguồn sáng ổn định.</p>

              <h3 id="toc_5-ve-sinh-va-cat-tia-dinh-ky">5. Vệ Sinh Và Cắt Tỉa Định Kỳ</h3>
              <p>Đừng để lá úa, lá rụng hay mảng rêu hỏng nằm quá lâu trong bình. Hãy dùng nhíp dài gắp bỏ phần hư, lau sạch mặt kính và giữ bố cục gọn gàng để ánh sáng luôn đi sâu vào bên trong.</p>

              <figure class="guide-inline-media">
                <img
                  src="<?= htmlspecialchars(guide_product_image_path($placementProduct)) ?>"
                  alt="<?= htmlspecialchars('Minh họa vị trí đặt terrarium với sản phẩm ' . guide_product_name($placementProduct)) ?>"
                  loading="lazy"
                  onerror="this.onerror=null;this.src='../images/avatar.png';">
                <figcaption>Terrarium đặt đúng vị trí sẽ xanh lâu và ít phát sinh nấm mốc hơn.</figcaption>
              </figure>

              <h2>Bảng Tóm Tắt Bí Quyết Giữ Terrarium Xanh Và Sạch Mốc</h2>
              <p>Dưới đây là bảng đối soát nhanh để bạn kiểm tra sức khỏe khu rừng mini của mình:</p>

              <div class="guide-table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Tình trạng</th>
                      <th>Nguyên nhân</th>
                      <th>Cách chăm sóc &amp; khắc phục</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Mốc trắng xuất hiện</td>
                      <td>Độ ẩm quá cao, bí khí</td>
                      <td>Lau sạch mốc, mở nắp 2 đến 3 tiếng và tạm ngừng tưới.</td>
                    </tr>
                    <tr>
                      <td>Rêu bị vàng hoặc nâu</td>
                      <td>Thiếu ánh sáng hoặc dùng nước máy còn dư clo</td>
                      <td>Chuyển bình tới nơi sáng hơn, ưu tiên nước lọc hoặc nước đã để lắng.</td>
                    </tr>
                    <tr>
                      <td>Lá cây thối nhũn</td>
                      <td>Úng nước ở tầng đáy</td>
                      <td>Dùng khăn giấy thấm bớt nước dư và mở bình thoáng khí hơn.</td>
                    </tr>
                    <tr>
                      <td>Kính đọng nước dày</td>
                      <td>Chênh lệch nhiệt độ hoặc dư ẩm kéo dài</td>
                      <td>Lau kính, giảm lượng tưới và tăng thời gian mở nắp định kỳ.</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <h2>Câu Hỏi Thường Gặp Về Việc Giữ Terrarium Luôn Xanh</h2>

              <h3 id="toc_terrarium-bi-moc-thi-xu-ly-hoa-chat-duoc-khong">Terrarium bị mốc thì xử lý hóa chất được không?</h3>
              <p>Bạn nên hạn chế dùng hóa chất mạnh. Cách an toàn hơn là dùng tăm bông thấm oxy già hoặc cồn loãng lau nhẹ đúng vùng bị mốc, sau đó để bình thoáng khí tự nhiên.</p>

              <h3 id="toc_co-can-dung-den-chuyen-dung-cho-terrarium">Có cần dùng đèn chuyên dụng cho terrarium?</h3>
              <p>Nếu vị trí đặt bình quá tối, một chiếc đèn LED ánh sáng trắng khoảng 6500K sẽ giúp cây duy trì màu xanh tốt hơn và giảm nguy cơ nấm mốc do thiếu sáng.</p>

              <p>Giữ một chiếc <strong>terrarium</strong> luôn xanh thật ra không quá khó nếu bạn quan sát đều và chăm đúng nhịp. Chỉ cần nhớ ba điều cốt lõi: <strong>chọn cây phù hợp - tưới vừa đủ - thông khí định kỳ</strong>.</p>

              <figure class="guide-inline-media">
                <img
                  src="<?= htmlspecialchars(guide_product_image_path($healthyProduct)) ?>"
                  alt="<?= htmlspecialchars('Minh họa terrarium khỏe mạnh với sản phẩm ' . guide_product_name($healthyProduct)) ?>"
                  loading="lazy"
                  onerror="this.onerror=null;this.src='../images/avatar.png';">
                <figcaption>Terrarium xanh lâu khi cây đã thuần môi trường và được chăm theo nhịp ổn định.</figcaption>
              </figure>

              <div class="guide-contact-box">
                <p><strong>Bạn cần hỗ trợ kỹ thuật cho chiếc bình của mình?</strong> Thuận Phát Garden luôn sẵn sàng tư vấn cách xử lý mốc, điều chỉnh ánh sáng và chọn mẫu terrarium dễ chăm hơn cho không gian của bạn.</p>
                <ul>
                  <li><strong>Địa chỉ:</strong> 131 Lý Tự Trọng, Cần Thơ</li>
                  <li><strong>Hotline:</strong> <a href="tel:0839778271">083 977 8271</a></li>
                  <li><strong>Email:</strong> <a href="mailto:thuanphatggarden@gmail.com">thuanphatggarden@gmail.com</a></li>
                  <li><strong>Facebook:</strong> <a href="https://www.facebook.com/thuanphatggarden" target="_blank" rel="noreferrer">facebook.com/thuanphatggarden</a></li>
                </ul>
              </div>
            </div>
          </article>

          <aside class="guide-sidebar">
            <section class="guide-sidebar-card">
              <div class="guide-sidebar-card__header">
                <h2>Gợi ý sản phẩm</h2>
              </div>

              <?php if ($featuredProducts !== []): ?>
                <div class="guide-product-list">
                  <?php foreach (array_slice($featuredProducts, 0, 5) as $product): ?>
                    <?php $pricing = get_product_pricing($product); ?>
                    <a class="guide-product-item" href="../sanpham/spchitiet.php?id=<?= (int) $product['id'] ?>">
                      <div class="guide-product-item__image">
                        <img
                          src="<?= htmlspecialchars(guide_product_image_path($product)) ?>"
                          alt="<?= htmlspecialchars(guide_product_name($product)) ?>"
                          loading="lazy"
                          onerror="this.onerror=null;this.src='../images/avatar.png';">
                      </div>
                      <div class="guide-product-item__content">
                        <h3><?= htmlspecialchars(guide_product_name($product)) ?></h3>
                        <p class="guide-product-item__price"><?= htmlspecialchars(guide_format_price($pricing['price'])) ?></p>
                        <?php if (!empty($pricing['is_sale'])): ?>
                          <p class="guide-product-item__old-price"><?= htmlspecialchars(guide_format_price($pricing['original_price'])) ?></p>
                        <?php endif; ?>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="guide-empty-state">Hiện chưa có sản phẩm để hiển thị. Bạn có thể quay lại sau hoặc xem trực tiếp tại trang sản phẩm.</p>
              <?php endif; ?>
            </section>

          </aside>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
