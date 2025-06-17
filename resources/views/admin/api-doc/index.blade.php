@extends('admin.layout')

@section('title', 'Tài liệu API')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Phần Test API -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Test API Login</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Request Body</h4>
                            <textarea id="requestBody" class="form-control mb-3" rows="5" style="font-family: monospace;">{
    "email": "",
    "mat_khau": ""
}</textarea>
                            <button onclick="testApi()" class="btn btn-primary">Test API</button>
                        </div>
                        <div class="col-md-6">
                            <h4>Response</h4>
                            <div class="response-box p-3 bg-light rounded">
                                <div id="tokenBox" class="mb-2" style="display: none;">
                                    <strong>Token:</strong>
                                    <div class="d-flex align-items-center mt-1">
                                        <code id="tokenValue" class="flex-grow-1"></code>
                                        <button onclick="copyToken()" class="btn btn-sm btn-outline-primary ms-2">Copy</button>
                                    </div>
                                </div>
                                <pre><code id="responseBody">Chưa có response</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phần Test API Đề tài -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Test API Đề tài</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Request</h4>
                            <div class="mb-3">
                                <label class="form-label">Method:</label>
                                <select id="deTaiMethod" class="form-select mb-3" onchange="toggleTrangThaiField()">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL:</label>
                                <input type="text" id="deTaiUrl" class="form-control mb-3" value="http://project.test/api/admin/de-tai">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Token:</label>
                                <input type="text" id="deTaiToken" class="form-control mb-3" placeholder="Nhập token">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ID (cho PUT/DELETE):</label>
                                <input type="text" id="deTaiId" class="form-control mb-3" placeholder="Nhập ID đề tài">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Request Body:</label>
                                <textarea id="deTaiRequestBody" class="form-control mb-3" rows="5" style="font-family: monospace;">{
    "ten_de_tai": "",
    "mo_ta": "",
    "y_kien_giang_vien": "",
    "ngay_bat_dau": "",
    "ngay_ket_thuc": "",
    "nhom_id": "",
    "giang_vien_id": "",
    "dot_bao_cao_id": "",
    "vai_tro_id": "",
    "trang_thai": 0
}</textarea>
                            </div>
                            <button onclick="testDeTaiApi()" class="btn btn-primary">Test API</button>
                        </div>
                        <div class="col-md-6">
                            <h4>Response</h4>
                            <div class="response-box p-3 bg-light rounded">
                                <pre><code id="deTaiResponseBody">Chưa có response</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phần Test API Tài khoản -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Test API Tài khoản</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Request</h4>
                            <div class="mb-3">
                                <label class="form-label">Method:</label>
                                <select id="taiKhoanMethod" class="form-select mb-3">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL:</label>
                                <input type="text" id="taiKhoanUrl" class="form-control mb-3" value="http://project.test/api/admin/tai-khoan">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Token:</label>
                                <input type="text" id="taiKhoanToken" class="form-control mb-3" placeholder="Nhập token">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ID (cho PUT/DELETE):</label>
                                <input type="text" id="taiKhoanId" class="form-control mb-3" placeholder="Nhập ID tài khoản">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Request Body:</label>
                                <textarea id="taiKhoanRequestBody" class="form-control mb-3" rows="5" style="font-family: monospace;">{
    "ten": "",
    "email": "",
    "mat_khau": "",
    "vai_tro_id": ""
}</textarea>
                            </div>
                            <button onclick="testTaiKhoanApi()" class="btn btn-primary">Test API</button>
                        </div>
                        <div class="col-md-6">
                            <h4>Response</h4>
                            <div class="response-box p-3 bg-light rounded">
                                <pre><code id="taiKhoanResponseBody">Chưa có response</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tài liệu API -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tài liệu API</h3>
                </div>
                <div class="card-body">
                    <div class="api-docs">
                        @foreach($apis as $category => $api)
                            <div class="api-category mb-5">
                                <h2 class="mb-4">{{ $api['title'] }}</h2>
                                @if(isset($api['description']))
                                    <p class="text-muted mb-4">{{ $api['description'] }}</p>
                                @endif
                                
                                @foreach($api['endpoints'] as $endpoint)
                                    <div class="api-endpoint mb-4">
                                        <div class="endpoint-header d-flex align-items-center mb-3">
                                            <span class="method-badge me-3 {{ strtolower($endpoint['method']) }}">
                                                {{ $endpoint['method'] }}
                                            </span>
                                            <code class="endpoint-url">{{ $endpoint['url'] }}</code>
                                        </div>
                                        
                                        <div class="endpoint-description mb-3">
                                            {{ $endpoint['description'] }}
                                        </div>

                                        @if(isset($endpoint['params']))
                                            <div class="endpoint-params mb-3">
                                                <h5>Tham số:</h5>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Tên</th>
                                                            <th>Mô tả</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($endpoint['params'] as $param => $description)
                                                            <tr>
                                                                <td><code>{{ is_string($param) ? $param : json_encode($param) }}</code></td>
                                                                <td>{{ is_string($description) ? $description : json_encode($description) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        @if(isset($endpoint['headers']))
                                            <div class="endpoint-headers mb-3">
                                                <h5>Headers:</h5>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Tên</th>
                                                            <th>Giá trị</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($endpoint['headers'] as $header => $value)
                                                            <tr>
                                                                <td><code>{{ is_string($header) ? $header : json_encode($header) }}</code></td>
                                                                <td><code>{{ is_string($value) ? $value : json_encode($value) }}</code></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        @if(isset($endpoint['response']))
                                            <div class="endpoint-response">
                                                <h5>Response:</h5>
                                                <div class="response-examples">
                                                    @foreach($endpoint['response'] as $type => $example)
                                                        <div class="response-example mb-3">
                                                            <h6 class="text-capitalize">{{ $type }}:</h6>
                                                            <pre class="bg-light p-3 rounded"><code>{{ is_string($example) ? $example : json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function testApi() {
    try {
        // Lấy và parse JSON từ textarea
        const requestBody = JSON.parse(document.getElementById('requestBody').value);
        
        // Chuẩn bị headers
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        // Thêm CSRF token nếu có
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }
        
        // Gửi request
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(requestBody)
        });

        const data = await response.json();
        
        // Hiển thị response
        document.getElementById('responseBody').textContent = JSON.stringify(data, null, 2);
        
        // Hiển thị token nếu có
        if (data.token_access) {
            document.getElementById('tokenBox').style.display = 'block';
            document.getElementById('tokenValue').textContent = data.token_access;
            // Lưu token
            localStorage.setItem('api_token', data.token_access);
        } else {
            document.getElementById('tokenBox').style.display = 'none';
        }
    } catch (error) {
        document.getElementById('responseBody').textContent = JSON.stringify({
            error: error.message
        }, null, 2);
        document.getElementById('tokenBox').style.display = 'none';
    }
}

function copyToken() {
    const token = document.getElementById('tokenValue').textContent;
    if (token) {
        navigator.clipboard.writeText(token).then(() => {
            alert('Đã copy token vào clipboard!');
        });
    }
}

async function testDeTaiApi() {
    try {
        const method = document.getElementById('deTaiMethod').value;
        let url = document.getElementById('deTaiUrl').value;
        const requestBody = document.getElementById('deTaiRequestBody').value;
        const id = document.getElementById('deTaiId').value;
        const token = document.getElementById('deTaiToken').value;
        
        // Thêm ID vào URL nếu là PUT hoặc DELETE
        if ((method === 'PUT' || method === 'DELETE') && id) {
            url = url + '/' + id;
        }
        
        // Chuẩn bị headers
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        // Thêm token nếu có
        if (!token) {
            throw new Error('Vui lòng nhập token!');
        }
        headers['Authorization'] = `Bearer ${token}`;

        // Thêm CSRF token nếu có
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        // Xử lý URL cho các method khác nhau
        if (method === 'GET' || method === 'DELETE') {
            // Nếu là GET hoặc DELETE, chuyển request body thành query params
            const params = new URLSearchParams();
            const bodyObj = JSON.parse(requestBody);
            Object.keys(bodyObj).forEach(key => {
                if (bodyObj[key]) {
                    params.append(key, bodyObj[key]);
                }
            });
            if (params.toString()) {
                url += '?' + params.toString();
            }
        }
        
        // Gửi request
        const response = await fetch(url, {
            method: method,
            headers: headers,
            body: method === 'GET' || method === 'DELETE' ? null : requestBody
        });

        const data = await response.json();
        
        // Kiểm tra nếu token hết hạn
        if (data.message === 'Unauthenticated.') {
            throw new Error('Token đã hết hạn. Vui lòng đăng nhập lại!');
        }
        
        // Hiển thị response
        document.getElementById('deTaiResponseBody').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('deTaiResponseBody').textContent = JSON.stringify({
            error: error.message
        }, null, 2);
    }
}

function toggleTrangThaiField() {
    const method = document.getElementById('deTaiMethod').value;
    const requestBody = document.getElementById('deTaiRequestBody');
    let bodyObj = JSON.parse(requestBody.value);
    
    if (method === 'POST') {
        // Xóa trường trang_thai nếu là POST
        delete bodyObj.trang_thai;
    } else if (method === 'PUT' && !bodyObj.hasOwnProperty('trang_thai')) {
        // Thêm trường trang_thai nếu là PUT và chưa có
        bodyObj.trang_thai = 0;
    }
    
    requestBody.value = JSON.stringify(bodyObj, null, 4);
}

// Gọi hàm khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    toggleTrangThaiField();
});

async function testTaiKhoanApi() {
    try {
        const method = document.getElementById('taiKhoanMethod').value;
        let url = document.getElementById('taiKhoanUrl').value;
        const requestBody = document.getElementById('taiKhoanRequestBody').value;
        const id = document.getElementById('taiKhoanId').value;
        const token = document.getElementById('taiKhoanToken').value;
        
        // Thêm ID vào URL nếu là PUT hoặc DELETE
        if ((method === 'PUT' || method === 'DELETE') && id) {
            url = url + '/' + id;
        }
        
        // Chuẩn bị headers
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        // Thêm token nếu có
        if (!token) {
            throw new Error('Vui lòng nhập token!');
        }
        headers['Authorization'] = `Bearer ${token}`;

        // Thêm CSRF token nếu có
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        // Xử lý URL cho các method khác nhau
        if (method === 'GET' || method === 'DELETE') {
            // Nếu là GET hoặc DELETE, chuyển request body thành query params
            const params = new URLSearchParams();
            const bodyObj = JSON.parse(requestBody);
            Object.keys(bodyObj).forEach(key => {
                if (bodyObj[key]) {
                    params.append(key, bodyObj[key]);
                }
            });
            if (params.toString()) {
                url += '?' + params.toString();
            }
        }
        
        // Gửi request
        const response = await fetch(url, {
            method: method,
            headers: headers,
            body: method === 'GET' || method === 'DELETE' ? null : requestBody
        });

        const data = await response.json();
        
        // Kiểm tra nếu token hết hạn
        if (data.message === 'Unauthenticated.') {
            throw new Error('Token đã hết hạn. Vui lòng đăng nhập lại!');
        }
        
        // Hiển thị response
        document.getElementById('taiKhoanResponseBody').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('taiKhoanResponseBody').textContent = JSON.stringify({
            error: error.message
        }, null, 2);
    }
}
</script>

<style>
.method-badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: bold;
    color: white;
}

.method-badge.get {
    background-color: #61affe;
}

.method-badge.post {
    background-color: #49cc90;
}

.method-badge.put {
    background-color: #fca130;
}

.method-badge.delete {
    background-color: #f93e3e;
}

.endpoint-url {
    background-color: #f8f9fa;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 1.1em;
}

.api-endpoint {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background-color: white;
}

.api-endpoint:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.response-box {
    min-height: 200px;
    border: 1px solid #dee2e6;
}

#tokenValue {
    background: #f8f9fa;
    padding: 5px;
    border-radius: 4px;
    word-break: break-all;
}

pre {
    margin: 0;
    white-space: pre-wrap;
}

code {
    color: #e83e8c;
}

textarea {
    font-size: 14px;
    line-height: 1.5;
}
</style>
@endpush
@endsection 