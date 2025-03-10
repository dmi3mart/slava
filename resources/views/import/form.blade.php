@extends('layouts.default')

@section('content')
    <div class="container">
        <h1>Импорт данных из Excel</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
                @if (session('errors'))
                    <ul>
                        @foreach (session('errors') as $row => $errors)
                            <li>Строка {{ $row }}: {{ implode(', ', $errors) }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Выберите файл (.xlsx, .xls):</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Загрузить</button>
        </form>

        <div id="progress" class="mt-4">
            <h3>Прогресс импорта:</h3>
            <div class="progress">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateProgress() {
                $.ajax({
                    url: "{{ route('import.progress') }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.progress) {
                            $('#progress-bar').css('width', response.progress).text(response.progress);
                        }
                    }
                });
            }

            setInterval(updateProgress, 5000);
        });
    </script>
@endsection
