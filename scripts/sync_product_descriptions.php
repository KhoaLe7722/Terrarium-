<?php
declare(strict_types=1);

require_once __DIR__ . '/../dangky_dangnhap/config.php';

$descriptions = [
    3 => <<<'HTML'
<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Bình Terrarium hình hộp đứng - viền kính trong suốt, thiết kế hiện đại</p>
<p><strong>Chất liệu:</strong> Thuỷ tinh dày 3mm, viền silicon độ đàn hồi cao, được làm thủ công tỉ mỉ, đảm bảo độ bền và độ trong suốt thẩm mỹ.</p>
<p><strong>Kích thước bình (Dài x Rộng x Cao):</strong><br><span style="font-size: 30px;">12 x 12 x 12 cm</span></p>

<p><strong>Thực vật bên trong:</strong></p>
<ul>
  <li>Rêu xanh tươi bản địa (4-10 loại tùy mùa)</li>
  <li>Dương xỉ lá me, cỏ trang trí</li>
  <li>Gỗ mục tạo hiệu ứng cổ điển</li>
  <li>Giá chưa bao gồm mô hình động vật</li>
</ul>

<p><strong>Phụ kiện kèm theo:</strong> Bình xịt chống nấm mốc, hướng dẫn chăm sóc</p>

<p><strong>Sản phẩm được thiết kế:</strong> Trang trí bàn làm việc, phòng ngủ, kệ sách hoặc làm quà tặng sinh nhật, tân gia.</p>

<p><strong>Về sản phẩm:</strong> “Một lần chạm vào vĩnh yên.”<br>
Tông màu lạnh, rêu dày và đá cổ tạo cảm giác thiêng liêng - như một khu rừng tĩnh lặng riêng của bạn.</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Nhiệt độ: 18 - 32&deg;C</li>
  <li>Tưới nước 3-4 ngày/lần trong 3 tuần đầu. Sau đó chỉ tưới khi thấy bình khô.</li>
  <li>Không để nắng trực tiếp. Có thể dùng đèn LED 3-8 tiếng/ngày.</li>
  <li>Luôn đóng nắp bình để giữ ẩm.</li>
</ul>

<p class="thank-you">Thuận Phát Garden xin chân thành cảm ơn Quý Khách!</p>
HTML,
    1 => <<<'HTML'
<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Bình Terrarium hình trứng - thiết kế bo tròn đáng yêu, phù hợp làm quà tặng</p>
<p><strong>Chất liệu:</strong> Thuỷ tinh dày 3mm, bo cong tinh tế, độ trong suốt cao</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size: 30px;">10 x 10 x 12 cm</span></p>

<p><strong>Thực vật bên trong:</strong></p>
<ul>
  <li>Rêu bản địa tươi tốt</li>
  <li>Cỏ và đá nhỏ trang trí</li>
  <li>Có thể kèm mô hình thú mini dễ thương</li>
</ul>

<p><strong>Phụ kiện kèm theo:</strong> Bình xịt, hướng dẫn chăm sóc</p>

<p><strong>Ứng dụng:</strong> Quà tặng sinh nhật, bàn học, kệ sách nhỏ</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tránh nắng trực tiếp</li>
  <li>Tưới 2-3 lần/tuần</li>
  <li>Đậy nắp khi không quan sát để giữ ẩm</li>
</ul>

<p class="thank-you">Thuận Phát Garden cảm ơn bạn đã lựa chọn!</p>
HTML,
    2 => <<<'HTML'
<p><strong>Concept:</strong> Tinh hoa vườn cảnh thu nhỏ - Thuận Phát Garden</p>

<p><strong>Loại bình:</strong> Bình hình trụ trong suốt, thiết kế cân đối và hiện đại, giúp mở rộng chiều sâu thị giác. Khi đặt trong không gian, bình như một khung cửa sổ nhỏ mở ra thế giới thiên nhiên kỳ ảo.</p>

<p><strong>Chất liệu:</strong> Thủy tinh dày cao cấp, đáy bình được gia cố chắc chắn chống trượt, cho cảm giác an tâm khi trưng bày lâu dài trên mọi loại bề mặt, kể cả bàn kính hay kệ gỗ mảnh.</p>

<p><strong>Kích thước bình:</strong><br><span style="font-size: 30px;">14 x 9 cm</span> - tỷ lệ hình trụ hài hòa, vừa vặn trong không gian sống hiện đại. Đường kính 14cm mang lại bề mặt đủ rộng cho tiểu cảnh phát triển, trong khi chiều cao 9cm giúp ánh sáng phân bổ đều và duy trì độ ẩm lý tưởng cho hệ sinh thái thu nhỏ.</p>

<p><strong>Thực vật bên trong:</strong></p>
<ul>
  <li>Rêu xanh bản địa tươi tốt, giữ ẩm tự nhiên, lan mềm theo địa hình</li>
  <li>Rêu đá phủ nền, mang lại sự cổ kính như vườn Nhật trăm tuổi</li>
  <li>Bộ tiểu cảnh <strong>cầu thang đá và cổng Torii Nhật Bản</strong> - biểu tượng cho sự chuyển mình và tĩnh lặng tâm hồn</li>
</ul>

<p><strong>Phụ kiện đi kèm:</strong></p>
<ul>
  <li>Bình xịt chuyên dụng chống mốc - giúp rêu luôn tươi, sạch và không bị úng</li>
  <li>Tài liệu hướng dẫn chăm sóc chi tiết từ Thuận Phát Garden - phù hợp cả với người mới bắt đầu</li>
</ul>

<p><strong>Phong cách thiết kế:</strong> Thiền định (Zen) - tối giản, sâu lắng và hài hoà. Mỗi chi tiết đều mang ý nghĩa, từ viên đá nhỏ đến bậc thang đá, như một lời nhắc nhở về sự tĩnh tại giữa nhịp sống hối hả.</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li><strong>Nhiệt độ lý tưởng:</strong> 18-32&deg;C - phù hợp với khí hậu Việt Nam</li>
  <li><strong>Chế độ tưới:</strong> 2-3 lần mỗi tuần, dùng bình xịt sương để giữ độ ẩm đều</li>
  <li><strong>Chiếu sáng:</strong> Sử dụng đèn LED từ 4-6 tiếng mỗi ngày - nên dùng đèn trắng hoặc vàng ấm để làm nổi bật màu xanh và đường nét tiểu cảnh</li>
</ul>

<p><em>Lưu ý:</em> Không đặt terrarium trực tiếp dưới nắng gắt hoặc nơi có gió mạnh. Nên đặt ở kệ, bàn trà hoặc góc học tập để tăng sự thư giãn và tập trung.</p>

<p class="thank-you"><strong>Thuận Phát Garden</strong> - chúng tôi không chỉ bán terrarium, chúng tôi trao tặng những lát cắt của thiên nhiên, được đóng gói bằng sự tinh tế và tâm huyết.</p>
HTML,
    4 => <<<'HTML'
<p><strong>Concept:</strong> Thuận Phát Garden - Trở về cõi tĩnh lặng</p>

<p><strong>Loại bình:</strong> Terrarium đa giác đứng - khung cảnh 360&deg;, tái hiện một thế giới tĩnh tại bên trong lớp kính</p>

<p><strong>Chất liệu:</strong> Kính cường lực trong suốt kết hợp khung thép đen mờ - vừa hiện đại, vừa hoài cổ, mang lại độ bền và tính thẩm mỹ cao</p>

<p><strong>Kích thước bình:</strong><br>
<span style="font-size: 30px;">16 x 16 x 32 cm</span> - Thiết kế hình trụ đứng chắc chắn, lý tưởng cho bố cục tầng lớp rừng sâu. Đường kính 16cm tạo ra nền rêu rộng và chắc, trong khi chiều cao 32cm mở ra không gian dựng núi non, tượng thiền và tiểu cảnh đền chùa. Sự kết hợp giữa chiều sâu và chiều cao giúp ánh sáng khuếch tán đều, giữ cho môi trường bên trong luôn tươi mát và sống động.
</p>

<p><strong>Thực vật và cảnh quan:</strong></p>
<ul>
  <li>Rêu tươi bản địa, cây lá màu nhỏ</li>
  <li>Gỗ mục nghệ thuật, đá tự nhiên thô mộc</li>
  <li>Tiểu cảnh tượng thiền, đền thờ gỗ - gợi nhớ đến một ngôi làng xa xưa ẩn sâu trong núi</li>
</ul>

<p><strong>Phụ kiện đi kèm:</strong> Bình xịt dưỡng ẩm, hướng dẫn chăm sóc chi tiết</p>

<p><strong>Phong cách tổng thể:</strong> Thiền - Cổ - Mộc. Một không gian trầm mặc thu nhỏ, đưa người xem vào trạng thái an yên ngay giữa đời sống bận rộn</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tưới nhẹ 2-3 lần mỗi tuần để giữ độ ẩm</li>
  <li>Dùng đèn LED ban ngày (4-6 tiếng) để hỗ trợ quang hợp</li>
</ul>

<p class="thank-you">Thuận Phát Garden - cảm ơn bạn đã để rừng trú ngụ nơi tim mình.</p>
HTML,
    6 => <<<'HTML'
<p><strong>Concept:</strong> Thuận Phát Garden</p>

<p><strong>Loại bình:</strong> Terrarium đa giác đứng - không gian mở 360&deg;, giúp ngắm trọn vẻ đẹp tiểu cảnh từ mọi góc nhìn</p>

<p><strong>Chất liệu:</strong> Kính cường lực trong suốt kết hợp khung thép sơn tĩnh điện màu đen - vừa tinh tế vừa bền bỉ với thời gian</p>

<p><strong>Kích thước bình:</strong><br>
<span style="font-size: 30px;">16 x 16 x 32 cm</span> - thiết kế hình trụ đứng cao đầy uy nghiêm, như một cột mốc thiền giữa lòng thiên nhiên. Đường kính 16cm tạo nền đủ rộng để bố trí tiểu cảnh rừng núi sinh động, trong khi chiều cao 32cm mang lại chiều sâu ấn tượng và sự phân tầng tự nhiên cho cây cối, đá và tượng thiền bên trong. Tổng thể tạo nên một thế giới thu nhỏ trầm mặc nhưng sống động - như một ngôi đền giữa rừng sâu.</p>

<p><strong>Thực vật và tiểu cảnh:</strong></p>
<ul>
  <li>Rêu xanh, cây lá màu nhỏ (ưa ẩm, sống tốt trong điều kiện trong nhà)</li>
  <li>Gỗ khô, đá tự nhiên</li>
  <li>Tượng thiền định và mô hình đền thờ mini - gợi lên không khí tôn nghiêm và thanh lọc</li>
</ul>

<p><strong>Phụ kiện kèm theo:</strong> Bình xịt giữ ẩm chuyên dụng + hướng dẫn chăm sóc chi tiết</p>

<p><strong>Phong cách tổng thể:</strong> Thiền viện cổ kính - tĩnh lặng và đầy chiều sâu tâm hồn, như một ngôi làng ẩn mình giữa núi rừng thiêng</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tưới nước 2-3 lần mỗi tuần bằng bình xịt nhẹ</li>
  <li>Chiếu sáng gián tiếp bằng đèn LED từ 4-6 giờ mỗi ngày (không ánh nắng trực tiếp)</li>
</ul>

<p class="thank-you">Thuận Phát Garden chân thành cảm ơn quý khách đã trao niềm tin và tình yêu cho thiên nhiên trong từng chiếc bình nhỏ!</p>
HTML,
    5 => <<<'HTML'
<p><strong>Concept:</strong> Thuận Phát Garden</p>

<p><strong>Loại bình:</strong> Terrarium đa giác đứng - thiết kế mở 360&deg;, dễ dàng quan sát từ mọi phía</p>

<p><strong>Chất liệu:</strong> Kính cường lực kết hợp khung thép sơn tĩnh điện màu đen - bền bỉ, sang trọng</p>

<p><strong>Kích thước bình:</strong><br>
<span style="font-size: 30px;">20 x 20 x 32 cm</span> - tỷ lệ lý tưởng giữa chiều ngang và chiều cao, mang lại cảm giác vững chãi và đầy chiều sâu. Với phần đế rộng 20cm, không gian bên trong được mở rộng đáng kể, cho phép kiến tạo một hệ sinh thái núi rừng phong phú: đá, rêu, gỗ khô và các tượng thiền mini đặt xen kẽ nhau. Chiều cao 32cm giúp tăng hiệu ứng thị giác tầng lớp, đồng thời tạo không gian thở cho cây và tiểu cảnh.</p>

<p><strong>Thực vật và tiểu cảnh:</strong></p>
<ul>
  <li>Rêu xanh mướt, cây nhỏ ưa ẩm</li>
  <li>Gỗ lũa, đá tự nhiên, tượng thiền và đền thờ mini</li>
</ul>

<p><strong>Phụ kiện kèm theo:</strong> Bình xịt giữ ẩm + Hướng dẫn chăm sóc chi tiết</p>

<p><strong>Phong cách tổng thể:</strong> Rừng sâu huyền bí - nơi ngôi đền cổ thiêng lặng giữa thiên nhiên</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tưới 2-3 lần mỗi tuần bằng bình xịt</li>
  <li>Chiếu sáng gián tiếp bằng đèn LED 4-6h/ngày (tránh ánh nắng trực tiếp)</li>
</ul>

<p class="thank-you">Thuận Phát Garden chân thành cảm ơn quý khách đã chọn lựa sản phẩm - nơi thiên nhiên và tĩnh tại gặp gỡ trong từng chiếc bình nhỏ!</p>
HTML,
    7 => <<<'HTML'
<p><strong>Concept:</strong> Thuận Phát Garden</p>

<p><strong>Loại bình:</strong> Terrarium đa giác đứng - khối hình lớn, cho phép xây dựng cảnh quan tầng lớp nhiều lớp</p>

<p><strong>Chất liệu:</strong> Kính cường lực cao cấp, khung thép đen chống gỉ - độ bền và thẩm mỹ song hành</p>

<p><strong>Kích thước bình:</strong><br>
<span style="font-size: 30px;">23 x 23 x 40 cm</span> - đây là mẫu bình lớn nhất trong dòng sản phẩm, với đế rộng 23cm và chiều cao lên đến 40cm, tạo ra một không gian rừng rậm thu nhỏ chân thực. Thiết kế lý tưởng để kết hợp nhiều lớp tiểu cảnh như tượng thiền, cổng đền Torii, bậc thang đá, cây rêu và mô phỏng sườn núi. Khoảng không bên trong giúp các yếu tố phong thủy, thiên nhiên và tĩnh tại giao thoa, tạo nên một thế giới thiền vị sống động.</p>

<p><strong>Thực vật và tiểu cảnh:</strong></p>
<ul>
  <li>Rêu tươi phủ nền, cây cảnh lá nhỏ ưa ẩm</li>
  <li>Đá cuội lớn, gỗ lũa, tượng thiền định và đền thờ gỗ mini</li>
  <li>Tiểu cảnh cầu gỗ, bậc đá, lối mòn dẫn lên chùa</li>
</ul>

<p><strong>Phụ kiện kèm theo:</strong> Bình xịt tạo ẩm chuyên dụng + Hướng dẫn chăm sóc đi kèm</p>

<p><strong>Phong cách tổng thể:</strong> Núi thiêng ẩn tu - nơi không gian mở ra cho sự an yên, thiền định và kết nối với thiên nhiên</p>

<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Xịt nước nhẹ 2-3 lần/tuần (tăng giảm tùy độ ẩm không khí)</li>
  <li>Chiếu sáng bằng đèn LED 4-6 giờ mỗi ngày, đặt nơi mát và tránh ánh nắng trực tiếp</li>
</ul>

<p class="thank-you">Thuận Phát Garden kính chúc quý khách tìm thấy sự tĩnh lặng và cảm hứng từ những khu vườn thiền thu nhỏ mà chúng tôi tạo ra!</p>
HTML,
    8 => <<<'HTML'
<p><strong>Concept:</strong> Nâng tầm trưng bày Terrarium</p>
<p><strong>Loại sản phẩm:</strong> Giá đỡ nhiều tầng kèm đèn LED rọi từ trên</p>
<p><strong>Chất liệu:</strong> Đế gỗ tự nhiên - Đèn LED cổ mềm điều chỉnh linh hoạt</p>
<p><strong>Kích thước tổng thể:</strong><br><span style="font-size: 30px;">Đế gỗ hình vuông, cao khoảng 25-35 cm</span></p>

<p><strong>Tiện ích:</strong></p>
<ul>
  <li>Đèn rọi từ trên chiếu sáng nổi bật tiểu cảnh</li>
  <li>Thiết kế đế nhiều tầng giúp tăng chiều sâu trưng bày</li>
  <li>Phù hợp các bình terrarium cao hoặc độc đáo</li>
</ul>

<p><strong>Đi kèm:</strong> Bộ nguồn và dây cắm USB</p>

<p><strong>Phong cách:</strong> Tối giản - Kiến trúc - Trưng bày chuyên nghiệp</p>

<p><strong>Hướng dẫn sử dụng:</strong></p>
<ul>
  <li>Đặt bình terrarium trên tầng cao nhất</li>
  <li>Chỉnh đèn LED chiếu đúng vị trí cần làm nổi bật</li>
  <li>Cắm điện bằng cổng USB để sử dụng</li>
</ul>

<p class="thank-you">Thuận Phát Garden xin chân thành cảm ơn!</p>
HTML,
];

$stmt = $conn->prepare('UPDATE products SET mo_ta = ? WHERE id = ?');
$updated = 0;

foreach ($descriptions as $id => $description) {
    $stmt->execute([$description, $id]);
    $updated += $stmt->rowCount();
}

echo "Updated descriptions for " . count($descriptions) . " products." . PHP_EOL;
echo "Affected rows: " . $updated . PHP_EOL;
