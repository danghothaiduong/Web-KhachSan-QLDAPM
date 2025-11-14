<?php
session_start();
include('connect_db.php'); // Bao gồm file kết nối CSDL

// Hàm kiểm tra định dạng email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Chỉ xử lý khi có yêu cầu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Lấy và làm sạch dữ liệu đầu vào
    $ho_ten = trim($_POST['ho_ten']);       // DỮ LIỆU MỚI
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']); // DỮ LIỆU MỚI
    $dia_chi = trim($_POST['dia_chi']);     // DỮ LIỆU MỚI
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = []; // Mảng lưu trữ các lỗi

    // 2. Validate dữ liệu
    if (empty($ho_ten) || empty($username) || empty($email) || empty($so_dien_thoai) || empty($dia_chi) || empty($password) || empty($confirm_password)) {
        $errors[] = "Vui lòng điền đầy đủ tất cả các trường.";
    }

    if (!isValidEmail($email)) {
        $errors[] = "Địa chỉ email không hợp lệ.";
    }
    
    // Validate cơ bản cho số điện thoại (chỉ chứa số và có độ dài hợp lý)
    if (!preg_match('/^[0-9]{9,15}$/', $so_dien_thoai)) {
        $errors[] = "Số điện thoại không hợp lệ.";
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Tên đăng nhập phải dài từ 3 đến 50 ký tự.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu và Xác nhận mật khẩu không khớp.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    
    // 3. Kiểm tra trùng lặp (chỉ thực hiện nếu không có lỗi cơ bản)
    if (empty($errors)) {
        // Kiểm tra trùng lặp Tên đăng nhập VÀ Email VÀ Số điện thoại
        $check_duplicate_sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ? OR email = ? OR so_dien_thoai = ?";
        $stmt_check = $conn->prepare($check_duplicate_sql);
        
        if ($stmt_check) {
            $stmt_check->bind_param("sss", $username, $email, $so_dien_thoai); // Thêm s cho so_dien_thoai
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $user_found = $result_check->fetch_assoc();
                if ($user_found['ten_dang_nhap'] === $username) {
                    $errors[] = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
                }
                if ($user_found['email'] === $email) {
                    $errors[] = "Email đã được sử dụng cho một tài khoản khác.";
                }
                if ($user_found['so_dien_thoai'] === $so_dien_thoai) {
                    $errors[] = "Số điện thoại đã được sử dụng cho một tài khoản khác.";
                }
            }
            $stmt_check->close();
        } else {
            // Lỗi chuẩn bị câu truy vấn CSDL
            die("Lỗi truy vấn SQL khi kiểm tra trùng lặp: " . $conn->error);
        }
    }

    // 4. Xử lý đăng ký nếu không có lỗi
    if (empty($errors)) {
        // TẠM THỜI: Giữ nguyên để tương thích với process_login.php (CỰC KỲ KHÔNG AN TOÀN)
        $hashed_password = $password; 
        
        // Vai trò mặc định cho người dùng mới là 'Khách hàng' (vai_tro_id = 2 theo giả định)
        $vai_tro_id = 2; 

        // CẬP NHẬT CÂU LỆNH INSERT SQL
        $insert_sql = "INSERT INTO nguoi_dung (ho_ten, ten_dang_nhap, email, so_dien_thoai, dia_chi, mat_khau_hash, vai_tro_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        
        if ($stmt_insert) {
            // CẬP NHẬT BIND_PARAM: ssssssi (7 tham số: 5 chuỗi, 1 chuỗi cho pass, 1 số nguyên)
            $stmt_insert->bind_param("ssssssi", $ho_ten, $username, $email, $so_dien_thoai, $dia_chi, $hashed_password, $vai_tro_id);
            
            if ($stmt_insert->execute()) {
                // Đăng ký thành công, chuyển hướng đến trang đăng nhập CÓ SẴN
                echo "<script>alert('Đăng ký tài khoản thành công! Vui lòng đăng nhập.'); window.location='index.php?page=login';</script>";
                exit();
            } else {
                $errors[] = "Đã xảy ra lỗi khi lưu vào cơ sở dữ liệu: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
            die("Lỗi truy vấn SQL khi thêm người dùng: " . $conn->error);
        }
    }
    
    // 5. Nếu có lỗi, hiển thị cảnh báo và quay lại trang đăng ký
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        // Chuyển hướng về trang đăng ký thông qua index.php
        echo "<script>alert('Lỗi đăng ký:\\n" . $error_message . "'); window.location='index.php?page=register';</script>";
    }

} else {
    // Nếu truy cập trực tiếp bằng GET, chuyển hướng về trang chủ hoặc đăng ký
    header("Location: index.php?page=register");
    exit();
}
?>