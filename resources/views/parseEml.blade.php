<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parse EML Content</title>
</head>
<body>
    <div style="margin: 20px;">
        <h1>Parse EML Content</h1>
        <form action="{{ route('parse-eml') }}" method="POST">
            @csrf
            <label for="emlContent">Paste EML Content Here:</label>
            <br>
            <textarea id="emlContent" name="emlContent" rows="10" cols="100" required></textarea>
            <br><br>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>