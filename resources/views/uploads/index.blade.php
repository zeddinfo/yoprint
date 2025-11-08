<!DOCTYPE html>
<html>
<head>
    <title>YoPrint CSV Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <h2>YoPrint CSV Upload</h2>

    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload CSV</button>
    </form>

    <hr>

    <h4>Recent Uploads</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>File Name</th>
                <th>Status</th>
                <th>Uploaded At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uploads as $upload)
                <tr id="upload-{{ $upload->id }}">
                    <td>{{ $upload->file_name }}</td>
                    <td class="status">{{ $upload->status }}</td>
                    <td>{{ $upload->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
