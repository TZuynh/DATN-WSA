# Hướng dẫn cài đặt và thiết lập dự án

## Yêu cầu hệ thống
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js >= 16.x
- NPM hoặc Yarn

## Các bước cài đặt

### 1. Clone dự án
```bash
git clone https://github.com/TZuynh/DATN-WSA.git
Rename thành project
cd project
```

### 2. Cài đặt dependencies
```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies
npm install
# hoặc
yarn install
```

### 3. Thiết lập môi trường
```bash
# Copy file .env.example thành .env
cp .env.example .env

# Tạo key cho ứng dụng
php artisan key:generate
```

### 4. Cấu hình database
- Tạo database mới trong MySQL
- Cập nhật thông tin database trong file `.env`:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=ten_database
  DB_USERNAME=root
  DB_PASSWORD=
  ```

### 5. Chạy migrations và seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build assets
```bash
npm run dev
# hoặc
yarn dev
```

### 7. Chạy server
```bash
php artisan serve
```

## Truy cập ứng dụng
- Frontend: http://localhost:3000
- Backend: http://localhost:8000

## Tài khoản mặc định
- Email: admin@caothang.edu.vn
- Password: Admin@123

# Hệ thống Quản lý Đồ án Tốt nghiệp

## Tính năng mới: Thêm đề tài từ trang danh sách hội đồng

### Mô tả
Tính năng này cho phép admin thêm đề tài trực tiếp từ trang danh sách hội đồng thông qua modal popup. Đề tài có thể được tạo độc lập hoặc gán vào hội đồng cụ thể.

### Cách sử dụng

#### 1. Thêm đề tài từ trang danh sách hội đồng
1. Truy cập vào trang "Danh sách hội đồng"
2. Nhấn nút "Thêm đề tài" ở góc trên bên phải
3. Điền thông tin trong modal:
   - Tên đề tài (bắt buộc)
   - Đợt báo cáo (bắt buộc)
   - Nhóm (tùy chọn)
   - Hội đồng (tùy chọn) - nếu không chọn, đề tài sẽ được tạo độc lập
   - Mô tả (tùy chọn)
4. Nhấn "Lưu đề tài" để hoàn tất

#### 2. Quản lý đề tài
- Xem danh sách đề tài trong trang quản lý đề tài
- Sửa đề tài bằng nút "Sửa"
- Xem chi tiết đề tài bằng nút "Xem"
- Xóa đề tài bằng nút "Xóa"

### Tính năng kỹ thuật

#### Backend
- **Controller**: `DeTaiController@store` - Xử lý tạo đề tài với JSON response
- **Controller**: `HoiDongController@index` - Truyền dữ liệu cho modal
- **Validation**: Kiểm tra dữ liệu đầu vào cho đề tài
- **Database Transaction**: Đảm bảo tính toàn vẹn dữ liệu
- **JSON Response**: Trả về response phù hợp cho AJAX request

#### Frontend
- **Modal**: Giao diện popup thân thiện với Bootstrap
- **JavaScript**: Xử lý AJAX request và response
- **Form Validation**: Kiểm tra dữ liệu trước khi gửi
- **Responsive**: Tương thích với các thiết bị khác nhau

#### Database
- Tự động tạo mã đề tài
- Liên kết đề tài với hội đồng qua bảng `chi_tiet_de_tai_bao_caos` (nếu có)
- Cập nhật trạng thái đề tài mặc định
- Cập nhật nhóm nếu có

### Lưu ý
- Đề tài có thể được tạo độc lập hoặc gán vào hội đồng
- Đề tài sẽ được tạo với mã tự động
- Trạng thái mặc định của đề tài là "Chờ duyệt"
- Giáo viên sẽ được phân công sau khi tạo đề tài
- Dữ liệu được lưu trong transaction để đảm bảo tính nhất quán
- Modal tự động reset form sau khi đóng

### Routes
- `POST /admin/de-tai` - Tạo đề tài (hỗ trợ cả redirect và JSON response)
- `GET /admin/hoi-dong` - Trang danh sách hội đồng với modal thêm đề tài

### API Response
```json
{
    "success": true,
    "message": "Thêm đề tài thành công",
    "data": {
        "id": 1,
        "ten_de_tai": "Tên đề tài",
        "ma_de_tai": "MDT-12345",
        "trang_thai": 0
    }
}
```
