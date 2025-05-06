<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>EGNYTE</title>
    <!-- Includere eventuali fogli di stile o script necessari -->
</head>
<body>
    <div>
        <pre id="result"></pre>
    </div>
    <div>
        <a href="#" onclick="gotoDir('{{ $path }}')">One Level UP</a><br>
        @foreach ($folders as $folder)
            <i class="fa fa-folder-o" aria-hidden="true"></i> <a href="#" onclick="gotoDir('{{ $folder['path'] }}')">{{ $folder['path'] }}</a><br>
        @endforeach
        @foreach ($files as $file)
            <i class="fa fa-file-o" aria-hidden="true"></i> <a href="#" onclick="f_download('{{ $file['path'] }}','{{ $file['entry_id'] }}')">{{ $file['path'] }}</a><br>
        @endforeach
    </div>

    <script>
        function gotoDir(path) {
            // Implementa la logica per navigare nella directory
        }

        function f_download(path, entry_id) {
            // Implementa la logica per il download del file
        }
    </script>
</body>
</html>
