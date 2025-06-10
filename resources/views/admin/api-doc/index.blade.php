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
                                                                <td><code>{{ $param }}</code></td>
                                                                <td>{{ $description }}</td>
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
                                                                <td><code>{{ $header }}</code></td>
                                                                <td><code>{{ $value }}</code></td>
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
                                                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($example, JSON_PRETTY_PRINT) }}</code></pre>
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
        if (data.token) {
            document.getElementById('tokenBox').style.display = 'block';
            document.getElementById('tokenValue').textContent = data.token;
            // Lưu token
            localStorage.setItem('api_token', data.token);
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