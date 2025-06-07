@extends('admin.layout')

@section('title', 'Tài liệu API')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tài liệu API</h3>
                </div>
                <div class="card-body">
                    <div class="api-docs">
                        @foreach($apis as $category => $api)
                            <div class="api-category mb-5">
                                <h2 class="mb-4">{{ $api['title'] }}</h2>
                                
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

pre {
    margin: 0;
    white-space: pre-wrap;
}

code {
    color: #e83e8c;
}
</style>
@endsection 