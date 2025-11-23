<!DOCTYPE html>
<html>
<head>
    <title>Register | Youton</title>
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
        h2 { text-align: center; }
        input {
            width: 100%; padding: 12px; margin-bottom: 15px;
            border-radius: 8px; border: 1px solid #ccc;
        }
        button {
            width: 100%; padding: 12px;
            background: #111827; color: white; border: none;
            border-radius: 8px; cursor: pointer; font-size: 16px;
        }
        a { display: block; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>

<div class="auth-box">
    <h2>Create Your Youton Account</h2>

    @if($errors->any())
        <div style="color: red; margin-bottom: 10px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

        <button type="submit">Register</button>

        <a href="{{ route('login') }}">Already have an account? Login</a>
    </form>
</div>

</body>
</html>
