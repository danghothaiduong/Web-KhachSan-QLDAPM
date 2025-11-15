<?php
session_start();
include('connect_db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Câu truy vấn
    $sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ? AND mat_khau_hash = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Ghi thông tin vào session
            $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
            $_SESSION['vai_tro_id'] = $user['vai_tro_id'];

            // Chuyển hướng theo vai trò
            if ($user['vai_tro_id'] == 1) { // Admin
                header("Location: admin_index.php");
            } else {
                header("Location: index.php?page=home");
            }
            exit();
        } else {
            echo "<script>alert('Sai tên đăng nhập hoặc mật khẩu!'); window.location='index.php?page=login';</script>";
        }
    } else {
        die("Lỗi truy vấn SQL: " . $conn->error);
    }
}
?>
