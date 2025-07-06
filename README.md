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
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:CtCG5YwBE/KZY11piXjWCRYG+mVVgnk9CxZ6BNuRBZg=
APP_DEBUG=true
APP_URL=http://project.test
SESSION_DOMAIN=.project.test

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

VITE_PORT=5173
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

## Hoặc có thể sử dụng ứng dụng Laragon
- Mở vào file laragon/www/ bỏ file dự án vào đây
- Mở thư mục C:\Windows\System32\drivers\etc mở file hosts và edit dưới dạng quyền administator
- Sau đó thêm dòng:
127.0.0.1      project.test     #laragon magic!   
127.0.0.1      admin.project.test     #laragon magic!   
127.0.0.1      giangvien.project.test     #laragon magic!
ở cuối file
- Sau đó ấn nút chạy để khởi động Apache và mySql của app Laragon
- Lên trình duyệt gõ project.test sẽ chạy dưới dạng local mà không cần phải chạy lệnh php artisan serve

## Tài khoản mặc định
- Email: admin@caothang.edu.vn
- Password: Admin@123
