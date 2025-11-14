<?php
if (!isset($conn) || $conn->connect_error) {
     include('connect_db.php');
}


// BƯỚC 2: Truy vấn dữ liệu
// admin_bills.php (BƯỚC 2: Truy vấn dữ liệu)

$sql = "
SELECT 
    hd.hoa_don_id, 
    nd.ho_ten AS ten_khach_hang, 
    hd.ngay_xuat, 
    hd.tong_thanh_toan, 
    hd.trang_thai_thanh_toan,
    dp.dat_phong_id
FROM 
    hoa_don hd
JOIN 
    dat_phong dp ON hd.dat_phong_id = dp.dat_phong_id
JOIN 
    nguoi_dung nd ON dp.nguoi_dung_id = nd.nguoi_dung_id
ORDER BY 
    hd.ngay_xuat DESC,  -- PHẢI LÀ DESC để MỚI NHẤT lên trên
    hd.hoa_don_id DESC
";
$result = $conn->query($sql);

?>

<style>
    .page-header {
        color: #333;
        font-size: 24px;
        margin-bottom: 15px;
    }
    .data-table-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .data-table th, .data-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .data-table th {
        background-color: #f5f5f5;
        color: #555;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
    }
    .data-table tr:hover {
        background-color: #f9f9f9;
    }
    /* Action Buttons */
    .action-button-group {
        display: flex;
        align-items: center; 
        gap: 5px;
        flex-wrap: nowrap;
        justify-content: center; 
        height: 100%;
        width: 100%; 
    }
    .action-button-group a {
        display: inline-block;
        margin-right: 5px;
        padding: 6px 10px;
        border-radius: 4px;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        line-height: 1;
    }
    .btn-view { background-color: #007bff; }
    .btn-edit { background-color: #ffc107; color: #333; }
    .btn-delete { background-color: #dc3545; }
    .currency { font-weight: 600; color: #008000; }
    
    /* Status Badges */
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
        color: #fff;
        text-transform: capitalize;
    }
    .status-Da-thanh-toan { background-color: #28a745; } 
    .status-Chua-thanh-toan { background-color: #dc3545; } 
    .status-Dang-xu-ly { background-color: #ffc107; color: #333; } 
</style>

<h2 class="page-header">Danh sách Hóa đơn</h2>
    
<div class="data-table-container">
    <button onclick="window.location.href='admin_index.php?page=add_bill';" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; margin-bottom: 15px; cursor: pointer;">
        <i class="fas fa-plus"></i> Tạo Hóa đơn mới
    </button>
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: center;">ID HĐ</th>
                <th style="text-align: center;">Khách hàng</th>
                <th style="text-align: center;">Ngày xuất</th>
                <th style="text-align: center;">Tổng tiền</th>
                <th style="text-align: center;">Trạng thái TT</th>
                <th style="width: 170px;text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Xử lý class trạng thái (loại bỏ dấu tiếng Việt để tránh lỗi CSS)
                    $status_class = str_replace(' ', '-', $row['trang_thai_thanh_toan']);
                    
                    // THAY THẾ TOÀN BỘ KÝ TỰ CÓ DẤU để khớp với CSS (Ví dụ: 'Đang-xử-lý' -> 'Dang-xu-ly')
                    $status_class = str_replace(
                        ['Đã', 'Chưa', 'Đang', 'toán', 'xử', 'lý'],
                        ['Da', 'Chua', 'Dang', 'toan', 'xu', 'ly'],
                        $status_class
                    );
                    // Định dạng tiền tệ
                    $total_amount_formatted = number_format($row['tong_thanh_toan'], 0, ',', '.') . ' VNĐ';
                    
                    echo "<tr>";
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row['hoa_don_id']) . "</td>";
                    echo "<td style='text-align: center;'>" . htmlspecialchars($row['ten_khach_hang']) . "</td>";
                    echo "<td style='text-align: center;'>" . date('d-m-Y H:i', strtotime($row['ngay_xuat'])) . "</td>";
                    echo "<td class='currency' style='text-align: center;'>" . $total_amount_formatted . "</td>";
                    echo "<td style='text-align: center;'><span class='status-badge status-" . $status_class . "'>" . htmlspecialchars($row['trang_thai_thanh_toan']) . "</span></td>";
                    echo "<td class='action-button-group'>";
                    echo "<a href='view_bill.php?id=" . $row['hoa_don_id'] . "' class='btn-view' title='Xem chi tiết'><i class='fas fa-eye'></i></a>";
                    echo "<a href='edit_bill.php?id=" . $row['hoa_don_id'] . "' class='btn-edit' title='Cập nhật trạng thái'><i class='fas fa-pen-to-square'></i></a>";
                    echo "<a href='delete_bill.php?id=" . $row['hoa_don_id'] . "' class='btn-delete' title='Xóa hóa đơn' onclick='return confirm(\"Xóa HĐ " . $row['hoa_don_id'] . "?\");'><i class='fas fa-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align: center;'>Không có hóa đơn nào được tìm thấy.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>