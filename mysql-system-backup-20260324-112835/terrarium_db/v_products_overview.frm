TYPE=VIEW
query=select `p`.`id` AS `id`,`p`.`ten_sp` AS `ten_sp`,`p`.`gia` AS `gia`,`p`.`gia_goc` AS `gia_goc`,`p`.`giam_gia_phan_tram` AS `giam_gia_phan_tram`,`p`.`hinh_chinh` AS `hinh_chinh`,`p`.`tinh_trang` AS `tinh_trang`,count(`pi`.`id`) AS `so_anh_phu` from (`terrarium_db`.`products` `p` left join `terrarium_db`.`product_images` `pi` on(`pi`.`product_id` = `p`.`id`)) group by `p`.`id`
md5=4860ea83d33cef4085dac3d689c3e723
updatable=0
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001773665938973002
create-version=2
source=SELECT p.id, p.ten_sp, p.gia, p.gia_goc, p.giam_gia_phan_tram, p.hinh_chinh, p.tinh_trang, COUNT(pi.id) AS so_anh_phu FROM terrarium_db.products p LEFT JOIN terrarium_db.product_images pi ON pi.product_id = p.id GROUP BY p.id
client_cs_name=cp850
connection_cl_name=cp850_general_ci
view_body_utf8=select `p`.`id` AS `id`,`p`.`ten_sp` AS `ten_sp`,`p`.`gia` AS `gia`,`p`.`gia_goc` AS `gia_goc`,`p`.`giam_gia_phan_tram` AS `giam_gia_phan_tram`,`p`.`hinh_chinh` AS `hinh_chinh`,`p`.`tinh_trang` AS `tinh_trang`,count(`pi`.`id`) AS `so_anh_phu` from (`terrarium_db`.`products` `p` left join `terrarium_db`.`product_images` `pi` on(`pi`.`product_id` = `p`.`id`)) group by `p`.`id`
mariadb-version=100432
