-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th10 25, 2025 lúc 03:21 PM
-- Phiên bản máy phục vụ: 5.7.25
-- Phiên bản PHP: 7.1.26



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qldapm`
--
CREATE DATABASE IF NOT EXISTS `qldapm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `qldapm`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dat_phong`
--

CREATE TABLE `dat_phong` (
  `dat_phong_id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `phong_id` int(11) NOT NULL,
  `ngay_nhan_phong` date NOT NULL,
  `ngay_tra_phong` date NOT NULL,
  `so_luong_khach` int(11) NOT NULL DEFAULT '1',
  `tong_chi_phi_phong` decimal(10,2) DEFAULT '0.00',
  `trang_thai_dat_phong` varchar(20) NOT NULL DEFAULT 'Pending',
  `ngay_dat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Bảng dat_phong
-- Đặt phòng 1: (22-20) = 2 đêm. Loại 1 (300k/đêm) -> 600000.00
INSERT INTO `dat_phong` (`dat_phong_id`, `nguoi_dung_id`, `phong_id`, `ngay_nhan_phong`, `ngay_tra_phong`, `so_luong_khach`, `tong_chi_phi_phong`, `trang_thai_dat_phong`, `ngay_dat`) VALUES
(1, 2, 1, '2025-10-20', '2025-10-22', 1, 600000.00, 'Completed', '2025-10-18 10:00:00'),
-- Đặt phòng 2: (27-25) = 2 đêm. Loại 2 (500k/đêm) -> 1000000.00
(2, 3, 2, '2025-10-25', '2025-10-27', 2, 1000000.00, 'Completed', '2025-10-23 11:00:00'),
-- Đặt phòng 3: (30-28) = 2 đêm. Loại 3 (1000k/đêm) -> 2000000.00
(3, 4, 3, '2025-10-28', '2025-10-30', 2, 2000000.00, 'Pending', '2025-10-26 14:30:00');
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dat_phong_dich_vu`
--

CREATE TABLE `dat_phong_dich_vu` (
  `dat_phong_dich_vu_id` int(11) NOT NULL,
  `dat_phong_id` int(11) NOT NULL,
  `dich_vu_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ngay_su_dung` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tong_chi_phi_dich_vu` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Bảng dat_phong_dich_vu
-- DP 1: Dịch vụ 1 (Ăn sáng 80k) * 2 suất = 160000.00
INSERT INTO `dat_phong_dich_vu` (`dat_phong_dich_vu_id`, `dat_phong_id`, `dich_vu_id`, `so_luong`, `ngay_su_dung`, `tong_chi_phi_dich_vu`) VALUES
(1, 1, 1, 2, '2025-10-20 08:00:00', 160000.00),
-- DP 2: Dịch vụ 3 (Thuê xe 150k) * 1 ngày = 150000.00
(2, 2, 3, 1, '2025-10-25 09:00:00', 150000.00),
-- DP 3: Dịch vụ 2 (Giặt ủi 50k) * 2 lần = 100000.00
(3, 3, 2, 2, '2025-10-28 10:00:00', 100000.00);
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dich_vu`
--

CREATE TABLE `dich_vu` (
  `dich_vu_id` int(11) NOT NULL,
  `ten_dich_vu` varchar(100) NOT NULL,
  `gia` decimal(10,2) NOT NULL,
  `don_vi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Bảng dich_vu (Không thay đổi)
INSERT INTO `dich_vu` (`dich_vu_id`, `ten_dich_vu`, `gia`, `don_vi`) VALUES
(1, 'Ăn sáng buffet', 80000.00, 'suất'),
(2, 'Giặt ủi', 50000.00, 'lần'),
(3, 'Thuê xe máy', 150000.00, 'ngày');
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh_phong`
--

CREATE TABLE `hinh_anh_phong` (
  `hinh_anh_id` int(11) NOT NULL,
  `loai_phong_id` int(11) NOT NULL,
  `url_hinh_anh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoa_don`
--

CREATE TABLE `hoa_don` (
  `hoa_don_id` int(11) NOT NULL,
  `dat_phong_id` int(11) NOT NULL,
  `ngay_xuat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `phi_phong` decimal(10,2) DEFAULT NULL,
  `phi_dich_vu` decimal(10,2) DEFAULT NULL,
  `giam_gia` decimal(10,2) DEFAULT '0.00',
  `thue` decimal(10,2) DEFAULT '0.00',
  `tong_thanh_toan` decimal(10,2) NOT NULL,
  `trang_thai_thanh_toan` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng hoa_don (Thuế = 10% của (Phi phòng + Phi dịch vụ))
-- HD 1: Phi phòng 600k, DV 160k. Tổng: 760k. Thuế 76k. Tổng TT: 836000.00
-- Bảng hoa_don (Đã gộp tất cả 3 bản ghi vào một câu lệnh INSERT duy nhất)
INSERT INTO `hoa_don` (`hoa_don_id`, `dat_phong_id`, `ngay_xuat`, `phi_phong`, `phi_dich_vu`, `giam_gia`, `thue`, `tong_thanh_toan`, `trang_thai_thanh_toan`) VALUES
(1, 1, '2025-10-22 14:30:00', 600000.00, 160000.00, 0.00, 76000.00, 836000.00, 'Đã thanh toán'),
(2, 2, '2025-10-27 10:30:00', 1000000.00, 150000.00, 0.00, 115000.00, 1265000.00, 'Chưa thanh toán'),
(3, 3, '2025-10-30 09:00:00', 2000000.00, 100000.00, 50000.00, 205000.00, 2255000.00, 'Đang xử lý');
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_phong`
--

CREATE TABLE `loai_phong` (
  `loai_phong_id` int(11) NOT NULL,
  `ten_loai` varchar(100) NOT NULL,
  `gia_co_ban` decimal(10,2) NOT NULL,
  `mo_ta` text,
  `suc_chua_toi_da` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng loai_phong (Không thay đổi)
INSERT INTO `loai_phong` (`loai_phong_id`, `ten_loai`, `gia_co_ban`, `mo_ta`, `suc_chua_toi_da`) VALUES
(1, 'Phòng đơn', 300000.00, 'Phòng nhỏ, phù hợp cho 1 người', 1),
(2, 'Phòng đôi', 500000.00, 'Phòng 2 giường lớn, có cửa sổ thoáng mát', 2),
(3, 'Phòng VIP', 1000000.00, 'Phòng sang trọng, có ban công và view đẹp', 3);
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `nguoi_dung_id` int(11) NOT NULL,
  `vai_tro_id` int(11) NOT NULL,
  `ten_dang_nhap` varchar(100) NOT NULL,
  `mat_khau_hash` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `ho_ten` varchar(150) DEFAULT NULL,
  `dien_thoai` varchar(15) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`nguoi_dung_id`, `vai_tro_id`, `ten_dang_nhap`, `mat_khau_hash`, `email`, `ho_ten`, `dien_thoai`, `dia_chi`) VALUES
(1, 1, 'admin', '123', 'admin@luxuryhotel.vn', 'Quản trị viên', '0909000000', 'Văn phòng trung tâm'),
(2, 3, 'nguyenvana', '123', 'vana@gmail.com', 'Nguyễn Văn A', '0909123456', 'An Giang'),
(3, 3, 'lethib', '123', 'lethib@gmail.com', 'Lê Thị B', '0909345678', 'Cần Thơ'),
(4, 3, 'tranvanh', '123', 'tranvanh@gmail.com', 'Trần Văn H', '0911222333', 'Long Xuyên');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong`
--

CREATE TABLE `phong` (
  `phong_id` int(11) NOT NULL,
  `loai_phong_id` int(11) NOT NULL,
  `so_phong` varchar(10) NOT NULL,
  `tang` int(11) DEFAULT NULL,
  `trang_thai` varchar(20) NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng phong (Phòng 4: Đã đặt -> Trang thái 'Booked')
INSERT INTO `phong` (`phong_id`, `loai_phong_id`, `so_phong`, `tang`, `trang_thai`) VALUES
(1, 1, '101', 1, 'Available'),
(2, 2, '201', 2, 'Available'),
(3, 3, '301', 3, 'Available'),
(4, 1, '102', 1, 'Booked'); -- Đã đổi từ 'Available' sang 'Booked'
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `thanh_toan_id` int(11) NOT NULL,
  `hoa_don_id` int(11) NOT NULL,
  `ngay_thanh_toan` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `so_tien` decimal(10,2) NOT NULL,
  `phuong_thuc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Bảng thanh_toan
-- TT 1: Thanh toán cho HD 1 (836000.00)
INSERT INTO `thanh_toan` (`thanh_toan_id`, `hoa_don_id`, `ngay_thanh_toan`, `so_tien`, `phuong_thuc`) VALUES
(1, 1, '2025-10-22 15:00:00', 836000.00, 'Tiền mặt');
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vai_tro`
--

CREATE TABLE `vai_tro` (
  `vai_tro_id` int(11) NOT NULL,
  `ten_vai_tro` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `vai_tro`
--

INSERT INTO `vai_tro` (`vai_tro_id`, `ten_vai_tro`) VALUES
(1, 'Admin'),
(3, 'Customer'),
(2, 'Manager');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `dat_phong`
--
ALTER TABLE `dat_phong`
  ADD PRIMARY KEY (`dat_phong_id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `phong_id` (`phong_id`);

--
-- Chỉ mục cho bảng `dat_phong_dich_vu`
--
ALTER TABLE `dat_phong_dich_vu`
  ADD PRIMARY KEY (`dat_phong_dich_vu_id`),
  ADD KEY `dat_phong_id` (`dat_phong_id`),
  ADD KEY `dich_vu_id` (`dich_vu_id`);

--
-- Chỉ mục cho bảng `dich_vu`
--
ALTER TABLE `dich_vu`
  ADD PRIMARY KEY (`dich_vu_id`),
  ADD UNIQUE KEY `ten_dich_vu` (`ten_dich_vu`);

--
-- Chỉ mục cho bảng `hinh_anh_phong`
--
ALTER TABLE `hinh_anh_phong`
  ADD PRIMARY KEY (`hinh_anh_id`),
  ADD KEY `loai_phong_id` (`loai_phong_id`);

--
-- Chỉ mục cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`hoa_don_id`),
  ADD UNIQUE KEY `dat_phong_id` (`dat_phong_id`);

--
-- Chỉ mục cho bảng `loai_phong`
--
ALTER TABLE `loai_phong`
  ADD PRIMARY KEY (`loai_phong_id`),
  ADD UNIQUE KEY `ten_loai` (`ten_loai`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`nguoi_dung_id`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `vai_tro_id` (`vai_tro_id`);

--
-- Chỉ mục cho bảng `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`phong_id`),
  ADD UNIQUE KEY `so_phong` (`so_phong`),
  ADD KEY `loai_phong_id` (`loai_phong_id`);

--
-- Chỉ mục cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`thanh_toan_id`),
  ADD KEY `hoa_don_id` (`hoa_don_id`);

--
-- Chỉ mục cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  ADD PRIMARY KEY (`vai_tro_id`),
  ADD UNIQUE KEY `ten_vai_tro` (`ten_vai_tro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `dat_phong`
--
ALTER TABLE `dat_phong`
  MODIFY `dat_phong_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dat_phong_dich_vu`
--
ALTER TABLE `dat_phong_dich_vu`
  MODIFY `dat_phong_dich_vu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dich_vu`
--
ALTER TABLE `dich_vu`
  MODIFY `dich_vu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hinh_anh_phong`
--
ALTER TABLE `hinh_anh_phong`
  MODIFY `hinh_anh_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `hoa_don_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loai_phong`
--
ALTER TABLE `loai_phong`
  MODIFY `loai_phong_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `nguoi_dung_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `phong`
--
ALTER TABLE `phong`
  MODIFY `phong_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `thanh_toan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  MODIFY `vai_tro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `dat_phong`
--
ALTER TABLE `dat_phong`
  ADD CONSTRAINT `dat_phong_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`nguoi_dung_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dat_phong_ibfk_2` FOREIGN KEY (`phong_id`) REFERENCES `phong` (`phong_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `dat_phong_dich_vu`
--
ALTER TABLE `dat_phong_dich_vu`
  ADD CONSTRAINT `dat_phong_dich_vu_ibfk_1` FOREIGN KEY (`dat_phong_id`) REFERENCES `dat_phong` (`dat_phong_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dat_phong_dich_vu_ibfk_2` FOREIGN KEY (`dich_vu_id`) REFERENCES `dich_vu` (`dich_vu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hinh_anh_phong`
--
ALTER TABLE `hinh_anh_phong`
  ADD CONSTRAINT `hinh_anh_phong_ibfk_1` FOREIGN KEY (`loai_phong_id`) REFERENCES `loai_phong` (`loai_phong_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_ibfk_1` FOREIGN KEY (`dat_phong_id`) REFERENCES `dat_phong` (`dat_phong_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD CONSTRAINT `nguoi_dung_ibfk_1` FOREIGN KEY (`vai_tro_id`) REFERENCES `vai_tro` (`vai_tro_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `phong`
--
ALTER TABLE `phong`
  ADD CONSTRAINT `phong_ibfk_1` FOREIGN KEY (`loai_phong_id`) REFERENCES `loai_phong` (`loai_phong_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`hoa_don_id`) REFERENCES `hoa_don` (`hoa_don_id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;







