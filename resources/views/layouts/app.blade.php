<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Youton')</title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            background: #f3f4f6;
        }
        .header {
            background: #111827;
            padding: 20px;
            color: white;
            font-size: 28px;
            font-weight: bold;
        }
        .container {
            padding: 40px;
        }
        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
        }
        .btn:hover { background: #1e40af; }
    </style>

    @yield('custom-css')
</head>
<body>

<div class="header">🔥 Youton Dashboard</div>

<div class="container">
    @yield('content')
</div>

</body>
</html>
