# Hệ Thống Quản Lý Khách Sạn (Hotel Management System - PHP)

Dự án website quản lý khách sạn và đặt phòng trực tuyến được xây dựng bằng PHP theo mô hình MVC, cơ sở dữ liệu MySQL và giao diện tối ưu trải nghiệm người dùng.

---

## 📌 Tính Năng Nổi Bật

### 1. Phía Khách Hàng (Client)
- **Xem & Tìm kiếm phòng:** Xem danh sách phòng theo loại, lọc theo ngày nhận/trả phòng, giá và tiện nghi.
- **Chi tiết phòng:** Xem ảnh thực tế, tiện ích, mô tả và đánh giá từ khách hàng trước.
- **Đặt phòng trực tuyến:** Đặt phòng với kiểm tra tình trạng phòng trống tự động (tránh trùng lịch).
- **Dịch vụ khách sạn:** Xem và đăng ký các gói dịch vụ đi kèm (ăn sáng, spa, đưa đón sân bay,...).
- **Hệ thống đánh giá:** Viết đánh giá, chấm điểm sao cho các kỳ nghỉ đã hoàn thành.
- **Quản lý tài khoản:** Đăng ký, đăng nhập, xem thông tin cá nhân và tra cứu lịch sử đặt phòng.

### 2. Phía Quản Trị (Admin & Nhân viên)
- **Dashboard & Báo cáo:** Thống kê doanh thu, trạng thái phòng, lượt đặt phòng mới.
- **Quản lý phòng & loại phòng:** Thêm, sửa, xóa phòng, cập nhật bảng giá và hình ảnh.
- **Quản lý đặt phòng:** Tiếp nhận, xác nhận, thực hiện Check-in / Check-out, hủy đặt phòng.
- **Hóa đơn & Thanh toán:** Tự động tính tiền phòng + dịch vụ, xuất hóa đơn, quản lý trạng thái thanh toán.
- **Phân công ca trực:** Phân công công việc theo ca cho nhân viên.
- **Quản lý nhân viên & Phân quyền:** Quản lý danh sách nhân viên và cấp quyền truy cập theo vai trò.
- **Quản lý khách hàng:** Tra cứu danh sách và lịch sử khách hàng.

---

## 🛠 Công Nghệ Sử Dụng

- **Ngôn ngữ:** PHP 8.x (Hướng đối tượng, PDO, Prepared Statements bảo mật chống SQL Injection, CSRF protection).
- **Cơ sở dữ liệu:** MySQL (Database: `QUANLYKHACHSAN`).
- **Giao diện (Frontend):** HTML5, CSS3, JavaScript, Bootstrap 5, FontAwesome icons.
- **Môi trường chạy khuyến nghị:** Laragon hoặc XAMPP (Apache + MySQL).

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án

### 1. Yêu cầu hệ thống
- PHP >= 8.0
- MySQL >= 5.7 hoặc MariaDB >= 10.3
- Apache (đã bật mod_rewrite)

### 2. Cài đặt trên Laragon / XAMPP
1. **Clone repository về máy:**
   ```bash
   git clone <URL_REPO_CUA_BAN>
   ```
2. Đặt thư mục dự án vào thư mục web server:
   - Với Laragon: `C:\laragon\www\hotel_php`
   - Với XAMPP: `C:\xampp\htdocs\hotel_php`

3. **Cấu hình Cơ sở dữ liệu:**
   - Mở phpMyAdmin hoặc công cụ quản trị MySQL (HeidiSQL, Navicat).
   - Tạo cơ sở dữ liệu mới với tên: `QUANLYKHACHSAN` (collation: `utf8mb4_unicode_ci`).
   - Import file script SQL của dự án vào database.
   - Kiểm tra thông tin kết nối trong tệp `config/database.php`:
     ```php
     private const DB_HOST = 'localhost';
     private const DB_PORT = '3306';
     private const DB_NAME = 'QUANLYKHACHSAN';
     private const DB_USER = 'root';
     private const DB_PASS = '';
     ```

4. **Khởi chạy ứng dụng:**
   - Trang khách hàng: `http://localhost/hotel_php/` (hoặc `http://hotel_php.test/` nếu dùng Laragon Virtual Host)
   - Trang quản trị: `http://localhost/hotel_php/admin/`

---

## 📁 Cấu Trúc Dự Án

```text
hotel_php/
├── admin/               # Router và trang quản trị
├── config/              # Cấu hình CSDL, Auth, Helper functions
├── controllers/         # Các Controller xử lý nghiệp vụ (Admin & Client)
├── css/                 # Stylesheet chính của dự án
├── Images/              # Hình ảnh phòng, dịch vụ
├── models/              # Các Model tương tác CSDL bằng PDO
├── static/              # Tài nguyên tĩnh
├── uploads/             # Thư mục chứa tệp tin tải lên từ người dùng
├── views/               # Giao diện hiển thị (Blade-free PHP Views)
│   ├── admin/           # Views quản trị
│   └── client/          # Views khách hàng
├── index.php            # Router chính cho client
├── .gitignore           # Cấu hình bỏ qua tệp Git
└── README.md            # Tài liệu dự án
```
