<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div style="width: 400px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 30px;">
        <h2 style="text-align: center; margin-bottom: 25px; color: #333;">Đăng ký tài khoản</h2>
        
        <form method="post" action="process_register.php">
            
            <div style="margin-bottom: 15px;">
                <label for="ho_ten" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Họ tên:</label>
                <input type="text" id="ho_ten" name="ho_ten" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="username" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Email:</label>
                <input type="email" id="email" name="email" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="so_dien_thoai" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Số điện thoại:</label>
                <input type="text" id="so_dien_thoai" name="so_dien_thoai" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="dia_chi" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Địa chỉ:</label>
                <input type="text" id="dia_chi" name="dia_chi" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Mật khẩu:</label>
                <input type="password" id="password" name="password" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="confirm_password" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="text-align: center;">
                <input type="submit" value="Đăng ký" 
                    style="background-color: #28a745; color: white; border: none; padding: 10px 25px; border-radius: 6px; font-size: 15px; cursor: pointer; transition: 0.3s;">
            </div>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #555;">
            Đã có tài khoản? 
            <a href="index.php?page=login" style="color: #007bff; text-decoration: none; font-weight: 500;">Đăng nhập ngay</a>
        </p>
    </div>
</div>