<?php
// 1. KẾT NỐI DATABASE
// File này được include bởi admin_index.php, 
// nên $conn đã có sẵn. Chúng ta chỉ cần kiểm tra.
if (!isset($conn)) {
    require_once 'connect_db.php';
}

// KHỞI TẠO BIẾN
$ten_loai = "";
$gia_co_ban = "";
$mo_ta = "";
$suc_chua = "";
$loai_phong_id_update = 0;
$is_editing = false;
$message = "";

// URL cơ sở cho trang này (để form và link trỏ về đúng)
$base_url = "admin_index.php?page=loai_phong";

// 2. XỬ LÝ FORM KHI SUBMIT (THÊM MỚI HOẶC CẬP NHẬT)
// Thêm 'form_loai_phong' để xác định đúng form này
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_loai_phong'])) {

    // Lấy dữ liệu từ form
    $ten_loai = $_POST['ten_loai'];
    $gia_co_ban = $_POST['gia_co_ban'];
    $mo_ta = $_POST['mo_ta'];
    $suc_chua = $_POST['suc_chua_toi_da'];
    
    if (isset($_POST['loai_phong_id_update']) && $_POST['loai_phong_id_update'] > 0) {
        // ----- CẬP NHẬT (UPDATE) -----
        $loai_phong_id = $_POST['loai_phong_id_update'];
        $sql = "UPDATE loai_phong SET ten_loai = ?, gia_co_ban = ?, mo_ta = ?, suc_chua_toi_da = ? WHERE loai_phong_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsii", $ten_loai, $gia_co_ban, $mo_ta, $suc_chua, $loai_phong_id);
        
        if ($stmt->execute()) {
            $message = "Cập nhật loại phòng thành công!";
        } else {
            $message = "Lỗi khi cập nhật: " . $stmt->error;
        }
        $stmt->close();
        
        // Xử lý upload ảnh (nếu có ảnh mới)
        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
            $target_dir = "images/phong/"; // Thư mục bạn đã tạo
            $file_name = basename($_FILES["hinh_anh"]["name"]);
            $target_file = $target_dir . $file_name;
            $db_path = $target_file; // Đường dẫn lưu vào CSDL

            if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
                $sql_img = "INSERT INTO hinh_anh_phong (loai_phong_id, url_hinh_anh) VALUES (?, ?)";
                $stmt_img = $conn->prepare($sql_img);
                $stmt_img->bind_param("is", $loai_phong_id, $db_path);
                $stmt_img->execute();
                $stmt_img->close();
                $message .= " Và đã tải ảnh mới thành công.";
            } else {
                $message .= " Nhưng có lỗi khi tải ảnh lên.";
            }
        }
        // Reset biến để form quay về "Thêm mới"
        $is_editing = false;
        $loai_phong_id_update = 0;
        $ten_loai = ""; $gia_co_ban = ""; $mo_ta = ""; $suc_chua = "";

    } else {
        // ----- THÊM MỚI (CREATE) -----
        $conn->begin_transaction();
        try {
            $sql = "INSERT INTO loai_phong (ten_loai, gia_co_ban, mo_ta, suc_chua_toi_da) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsi", $ten_loai, $gia_co_ban, $mo_ta, $suc_chua);
            $stmt->execute();
            
            $loai_phong_id_moi = $conn->insert_id;
            
            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
                $target_dir = "images/phong/";
                $file_name = basename($_FILES["hinh_anh"]["name"]);
                $target_file = $target_dir . $file_name;
                $db_path = $target_file;

                if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
                    $sql_img = "INSERT INTO hinh_anh_phong (loai_phong_id, url_hinh_anh) VALUES (?, ?)";
                    $stmt_img = $conn->prepare($sql_img);
                    $stmt_img->bind_param("is", $loai_phong_id_moi, $db_path);
                    $stmt_img->execute();
                    $stmt_img->close();
                } else {
                    throw new Exception("Lỗi khi di chuyển file ảnh.");
                }
            } else {
                 throw new Exception("Bạn phải chọn ảnh đại diện khi thêm mới.");
            }
            
            $conn->commit();
            $message = "Thêm loại phòng mới và ảnh thành công!";
            $ten_loai = ""; $gia_co_ban = ""; $mo_ta = ""; $suc_chua = "";
            
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Lỗi khi thêm mới: " . $e->getMessage() . " | " . $conn->error;
        }
        $stmt->close();
    }
}

// 3. XỬ LÝ XÓA (DELETE) - ĐÃ SỬA
if (isset($_GET['action']) && $_GET['action'] == 'delete_lp' && isset($_GET['id'])) {
    $loai_phong_id = $_GET['id'];
    $sql = "DELETE FROM loai_phong WHERE loai_phong_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loai_phong_id);
    
    if ($stmt->execute()) {
        $message = "Xóa loại phòng thành công! (Tất cả phòng và ảnh liên quan cũng đã bị xóa)";
    } else {
        $message = "Lỗi khi xóa: " . $stmt->error;
    }
    $stmt->close();
}

// 4. XỬ LÝ SỬA (LẤY THÔNG TIN) - ĐÃ SỬA
if (isset($_GET['action']) && $_GET['action'] == 'edit_lp' && isset($_GET['id'])) {
    $loai_phong_id_update = $_GET['id'];
    $is_editing = true;
    
    $sql = "SELECT * FROM loai_phong WHERE loai_phong_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loai_phong_id_update);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ten_loai = $row['ten_loai'];
        $gia_co_ban = $row['gia_co_ban'];
        $mo_ta = $row['mo_ta'];
        $suc_chua = $row['suc_chua_toi_da'];
    }
    $stmt->close();
}

// 5. LẤY DỮ LIỆU ĐỂ HIỂN THỊ (READ)
$sql_select = "SELECT lp.*, 
                    (SELECT ha.url_hinh_anh FROM hinh_anh_phong ha 
                     WHERE ha.loai_phong_id = lp.loai_phong_id LIMIT 1) as hinh_anh
               FROM loai_phong lp 
               ORDER BY lp.loai_phong_id DESC";
$result_select = $conn->query($sql_select);

?>

<!-- BẮT ĐẦU NỘI DUNG HTML (Đã xóa <html>, <head>, <style>, <body>) -->
<!-- 
    LƯU Ý: Giao diện (CSS class) này có thể không khớp 100%
    với CSS của file admin_index.php. 
    Bạn cần copy CSS ở Bước 2 vào file admin_index.php.
-->
<div class="container-content">
    
    <!-- 
      File admin_index.php của bạn đã có <h2>...</h2> rồi
      nên chúng ta không cần lặp lại <h2>Quản lý Loại Phòng</h2> ở đây
    -->

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Lỗi') !== false ? 'error' : ''; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h3><?php echo $is_editing ? 'Chỉnh sửa Loại Phòng' : 'Thêm Loại Phòng Mới'; ?></h3>
        
        <!-- FORM ĐÃ SỬA: 
            1. Sửa 'action'
            2. Thêm hidden input 'form_loai_phong'
        -->
        <form action="<?php echo $base_url; ?>" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="form_loai_phong" value="1">
            <input type="hidden" name="loai_phong_id_update" value="<?php echo $loai_phong_id_update; ?>">

            <div>
                <label for="ten_loai">Tên loại phòng:</label>
                <input type="text" id="ten_loai" name="ten_loai" value="<?php echo htmlspecialchars($ten_loai); ?>" required>
            </div>
            <div>
                <label for="gia_co_ban">Giá cơ bản:</label>
                <input type="number" id="gia_co_ban" name="gia_co_ban" step="0.01" value="<?php echo htmlspecialchars($gia_co_ban); ?>" required>
            </div>
            <div>
                <label for="suc_chua_toi_da">Sức chứa tối đa (người):</label>
                <input type="number" id="suc_chua_toi_da" name="suc_chua_toi_da" value="<?php echo htmlspecialchars($suc_chua); ?>" required>
            </div>
            <div>
                <label for="mo_ta">Mô tả:</label>
                <textarea id="mo_ta" name="mo_ta"><?php echo htmlspecialchars($mo_ta); ?></textarea>
            </div>
             <div>
                <label for="hinh_anh">Ảnh đại diện:</label>
                <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*" <?php echo $is_editing ? '' : 'required'; ?>>
                <?php if ($is_editing): ?>
                    <small>Để trống nếu không muốn thay đổi ảnh. Upload ảnh mới sẽ *thêm* ảnh mới.</small>
                <?php endif; ?>
            </div>
            
            <button type="submit"><?php echo $is_editing ? 'Cập nhật' : 'Thêm mới'; ?></button>
            <?php if ($is_editing): ?>
                <!-- Sửa link Hủy -->
                <a href="<?php echo $base_url; ?>" class="form-container button cancel">Hủy</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Bảng này sẽ dùng CSS của admin_index.php -->
    <h3>Danh sách Loại Phòng</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên loại</th>
                <th>Giá cơ bản</th>
                <th>Sức chứa</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_select && $result_select->num_rows > 0): ?>
                <?php while($row = $result_select->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['loai_phong_id']; ?></td>
                        <td>
                            <?php if (!empty($row['hinh_anh'])): ?>
                                <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Ảnh" class="thumbnail">
                            <?php else: ?>
                                (Chưa có ảnh)
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['ten_loai']); ?></td>
                        <td><?php echo number_format($row['gia_co_ban']); ?> VNĐ</td>
                        <td><?php echo $row['suc_chua_toi_da']; ?></td>
                        <td>
                            <!-- Sửa link Sửa / Xóa -->
                            <a href="<?php echo $base_url; ?>&action=edit_lp&id=<?php echo $row['loai_phong_id']; ?>" class="btn btn-success">Sửa</a>
                            <a href="<?php echo $base_url; ?>&action=delete_lp&id=<?php echo $row['loai_phong_id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('CẢNH BÁO: Xóa loại phòng sẽ xóa TẤT CẢ các phòng và ảnh liên quan. Bạn chắc chắn?');">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Chưa có loại phòng nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
<!-- KẾT THÚC NỘI DUNG HTML -->