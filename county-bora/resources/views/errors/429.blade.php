<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Too Many Attempts</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 { color: #d9534f; }
        p { margin: 20px 0; font-size: 1.1em; }
        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Too Many Attempts</h1>
        <p>For security purposes, your account has been temporarily locked due to multiple failed login attempts. Please try again after 12 hours.</p>
        <a href="/login">Return to Login</a>
    </div>
</body>
</html>