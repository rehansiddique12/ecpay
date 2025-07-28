<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff3f3; color: #b71c1c; text-align: center; padding-top: 100px; }
        .error-box { background: #fff; border: 1px solid #f44336; display: inline-block; padding: 40px 60px; border-radius: 8px; box-shadow: 0 2px 8px #f4433620; }
        h1 { font-size: 2.2em; margin-bottom: 20px; }
        p { font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>Account Suspended</h1>
        <p>{{ $error_message }}</p>
    </div>
</body>
</html> 