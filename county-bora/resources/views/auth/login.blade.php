<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>County Bora | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@700;900&family=Inter:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: #FBF9F1;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        /* Hero Container (Left Side) */
        .hero-container {
            position: absolute;
            left: 0; right: 0; top: 0; bottom: 0;
            /* Using bg.jpg in public/images folder */
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), 
                        url('/images/bg.jpg'); 
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            padding-left: 48px;
        }

        /* Left Content Section */
        .left-content {
            width: 480px;
            color: #FFFFFF;
            z-index: 2;
        }

        .county-title {
            font-family: 'Public Sans', sans-serif;
            font-weight: 900;
            font-size: 60px;
            line-height: 60px;
            letter-spacing: -1.5px;
            margin-bottom: 24px;
        }

        .mission-vision-border {
            border-left: 4px solid #C9A900;
            padding-left: 16px;
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mission-vision-label {
            font-family: 'Public Sans';
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 1.2px;
            color: #C9A900;
            text-transform: uppercase;
        }

        .mission-vision-text {
            font-family: 'Inter';
            font-size: 18px;
            line-height: 29px;
            color: rgba(255, 255, 255, 0.9);
        }

        .italic {
            font-style: italic;
        }

        /* Login Card Section */
        .login-card {
            position: absolute;
            right: 48px;
            width: 448px;
            height: 768px;
            background: rgba(251, 249, 241, 0.95);
            box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(12px);
            border-radius: 8px;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 3;
        }

        .login-heading {
            font-family: 'Public Sans';
            font-weight: 700;
            font-size: 30px;
            color: #1B1C17;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-family: 'Inter';
            font-weight: 500;
            font-size: 14px;
            color: #3F4943;
            margin-bottom: 40px;
        }

        .form-label-custom {
            font-family: 'Public Sans';
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 1.1px;
            color: #3F4943;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .input-custom {
            background: #E4E3DA;
            border: none;
            padding: 14px;
            height: 47px;
            font-family: 'Inter';
            font-weight: 500;
            font-size: 16px;
            border-radius: 4px;
            width: 100%;
            color: #1B1C17;
        }

        .forgot-link {
            font-family: 'Public Sans';
            font-weight: 700;
            font-size: 10px;
            color: #005039;
            text-decoration: none;
            text-transform: uppercase;
        }

        .btn-custom {
            background: #C9A900;
            height: 60px;
            border-radius: 4px;
            border: none;
            color: #4C3F00;
            font-family: 'Public Sans';
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            width: 100%;
            margin-top: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            box-shadow: 0px 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .footer-text {
            position: absolute;
            bottom: 48px;
            left: 48px;
            font-family: 'Public Sans';
            font-weight: 600;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="hero-container">
        <div class="left-content">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:128px; margin-bottom:24px;">
            
            <h1 class="county-title">County<br>Bora</h1>
            
            <div class="mission-vision-border">
                <div class="mission-vision-label">Our Mission</div>
                <div class="mission-vision-text italic">"To provide affordable, accessible and sustainable services through efficient management of resources and multi-sectoral approach"</div>
            </div>

            <div class="mission-vision-border">
                <div class="mission-vision-label">Our Vision</div>
                <div class="mission-vision-text">"A city of choice to invest, work and live in"</div>
            </div>
        </div>

        <div class="login-card">
            <h3 class="login-heading">Login</h3>
            <p class="login-subtitle">Enter your credentials to continue</p>

            @if($errors->any())
                <div class="alert alert-danger py-2" style="font-size: 12px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label-custom">National ID / Username</label>
                    <input type="text" name="email" class="input-custom" placeholder="e.g. 12345678" required autofocus>
                </div>

                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <label class="form-label-custom">Password</label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>
                <input type="password" name="password" class="input-custom" placeholder="••••••••" required>

                <button type="submit" class="btn-custom">
                    Login 
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
        </div>

        <div class="footer-text">
            © 2026 NAIROBI CITY COUNTY. ALL RIGHTS RESERVED.
        </div>
    </div>

</body>
</html>