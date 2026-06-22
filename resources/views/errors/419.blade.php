<!DOCTYPE html>
<html lang="en" class="semi-dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png" />
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('admin-assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/icons.css') }}" rel="stylesheet">
    
    <title>Kiosk - 419 Page Expired</title>
    <style>
        body {
            background-color: #f7f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .error-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #111111, #dc4646);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #111111;
            margin-bottom: 1rem;
        }
        .error-desc {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-group-custom {
            display: flex;
            gap: 1rem;
        }
        .btn-home {
            background: #111111;
            color: #ffffff;
            border: 2px solid #111111;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-home:hover {
            background: #dc4646;
            border-color: #dc4646;
            color: #ffffff;
            transform: translateY(-2px);
        }
        .btn-back {
            background: transparent;
            color: #111111;
            border: 2px solid #111111;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background: #111111;
            color: #ffffff;
            transform: translateY(-2px);
        }
        .error-img {
            max-height: 380px;
            object-fit: contain;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @media (max-width: 991.98px) {
            .error-card {
                text-align: center;
            }
            .btn-group-custom {
                justify-content: center;
            }
            .error-img {
                margin-top: 2rem;
                max-height: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card card p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="error-code">419</div>
                    <h2 class="error-title">Page Expired</h2>
                    <p class="error-desc">Your session has expired or the security token is invalid. Please refresh the page and try submitting the form again.</p>
                    <div class="btn-group-custom">
                        <a href="{{ url('/') }}" class="btn btn-home shadow-sm"><i class="bx bx-home-alt me-1"></i>Go Home</a>
                        <a href="javascript:history.back()" class="btn btn-back"><i class="bx bx-arrow-back me-1"></i>Go Back</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('admin-assets/images/error-images/419-error.png') }}" class="img-fluid error-img" alt="419 error illustration">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
