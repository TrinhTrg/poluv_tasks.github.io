# 🔧 Hướng dẫn Debug Email trong Telescope

## ❌ Vấn đề hiện tại

Bạn đã nhận được mã xác nhận qua email, nhưng **không thấy email trong Telescope** vì:

1. **MAIL_MAILER=log** - Email chỉ được ghi vào file log, không qua Mail system
2. **Telescope Mail Watcher** chỉ bắt được email khi gửi qua SMTP/array driver

## ✅ Giải pháp

### **Cách 1: Dùng Array Driver (Nhanh nhất, chỉ để test)**

1. Mở file `.env`
2. Thay đổi dòng:
   ```env
   MAIL_MAILER=array
   ```
3. Restart server: `php artisan serve`
4. Test lại flow forgot password
5. Vào Telescope: `http://localhost:8000/telescope/mail`

**Ưu điểm**: Email sẽ xuất hiện trong Telescope ngay lập tức
**Nhược điểm**: Email không thực sự được gửi, chỉ lưu trong memory

---

### **Cách 2: Dùng Mailtrap (Khuyến nghị cho development)**

1. Đăng ký tài khoản miễn phí tại: https://mailtrap.io
2. Tạo inbox mới
3. Copy thông tin SMTP credentials
4. Cập nhật file `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@poluv.com"
   MAIL_FROM_NAME="PoLuv Tasks"
   ```
5. Restart server: `php artisan serve`
6. Test lại flow forgot password
7. Kiểm tra:
   - Telescope: `http://localhost:8000/telescope/mail`
   - Mailtrap inbox: https://mailtrap.io/inboxes

**Ưu điểm**: 
- Email thực sự được gửi qua SMTP
- Có thể xem email với giao diện đẹp trong Mailtrap
- Telescope sẽ bắt được email

---

### **Cách 3: Dùng Gmail SMTP (Cho production)**

1. Bật "2-Step Verification" trong Google Account
2. Tạo "App Password" tại: https://myaccount.google.com/apppasswords
3. Cập nhật file `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@gmail.com
   MAIL_PASSWORD=your_app_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="your_email@gmail.com"
   MAIL_FROM_NAME="PoLuv Tasks"
   ```

---

## 📊 Kiểm tra Telescope

Sau khi thay đổi MAIL_MAILER, hãy:

1. Clear cache: `php artisan config:clear`
2. Test lại flow forgot password
3. Vào Telescope Mail tab: `http://localhost:8000/telescope/mail`
4. Bạn sẽ thấy email với subject: **"Password Reset Code - PoLuv Tasks"**
5. Click vào để xem chi tiết, bao gồm mã 6 chữ số

---

## 🎯 Lưu ý quan trọng

- **Queue Worker**: Nếu bạn muốn dùng lại Job (SendPasswordResetCodeJob), hãy chạy:
  ```bash
  php artisan queue:work
  ```
  
- **Telescope Watcher**: Đảm bảo Mail Watcher được bật trong `config/telescope.php`:
  ```php
  Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
  ```

- **Clear Config**: Sau khi thay đổi .env, luôn chạy:
  ```bash
  php artisan config:clear
  ```

---

## 🐛 Debug thêm

Nếu vẫn không thấy trong Telescope, kiểm tra:

1. **Telescope có đang chạy không?**
   - Vào: `http://localhost:8000/telescope`
   - Nếu lỗi 404, chạy: `php artisan telescope:install`

2. **Mail Watcher có được bật không?**
   - Kiểm tra file `config/telescope.php` dòng 182
   - Phải là: `Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),`

3. **Kiểm tra log file**:
   ```bash
   Get-Content storage\logs\laravel-2025-12-21.log -Tail 100
   ```

---

## 📸 Kết quả mong đợi

Sau khi fix, trong Telescope Mail tab bạn sẽ thấy:

- **Subject**: Password Reset Code - PoLuv Tasks
- **To**: email của bạn
- **Content**: Chứa mã 6 chữ số (ví dụ: 123456)
- **Time**: Thời gian gửi email

Giống như ảnh 2 bạn đã gửi, nhưng thay vì "Reset Password" button sẽ là mã 6 chữ số.
