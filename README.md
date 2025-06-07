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
