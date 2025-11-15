<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div style="width: 400px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 30px;">
        <h2 style="text-align: center; margin-bottom: 25px; color: #333;">Đăng nhập vào hệ thống</h2>
        
        <form method="post" action="process_login.php">
            <div style="margin-bottom: 15px;">
                <label for="username" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Mật khẩu:</label>
                <input type="password" id="password" name="password" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="text-align: center;">
                <input type="submit" value="Đăng nhập" 
                    style="background-color: #007bff; color: white; border: none; padding: 10px 25px; border-radius: 6px; font-size: 15px; cursor: pointer; transition: 0.3s;">
            </div>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #555;">
            Bạn chưa có tài khoản? 
            <a href="index.php?page=register" style="color: #007bff; text-decoration: none; font-weight: 500;">Đăng ký ngay</a>
        </p>
    </div>
</div>
