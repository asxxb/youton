<!DOCTYPE html>
<html>
<head>
    <title>Login | Youton</title>
    <style>
        body {
            font-family: Arial;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .auth-box {
            background: white;
            padding: 35px;
            width: 400px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        h2 { margin-bottom: 20px; text-align: center; }
        input {
            width: 100%; padding: 12px; margin-bottom: 15px;
            border-radius: 8px; border: 1px solid #ccc;
        }
        button {
            width: 100%; padding: 12px;
            background: #2563eb; color: white; border: none;
            border-radius: 8px; font-size: 16px; cursor: pointer;
        }
        a { display: block; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>

<div class="auth-box">
    <h2>Login to Youton</h2>

    @if($errors->any())
        <div style="color: red; margin-bottom: 10px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />

        <button type="submit">Login</button>

        <a href="{{ route('register') }}">Create an account</a>
    </form>
</div>

</body>
</html>
