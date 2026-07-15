<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Our QR Codes - Quick Access</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --accent: #ec4899;
            --dark: #1e293b;
            --light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 3rem 0;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: float 25s infinite ease-in-out;
        }

        .shape1 {
            width: 400px;
            height: 400px;
            background: #ff6b9d;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape2 {
            width: 350px;
            height: 350px;
            background: #feca57;
            bottom: 10%;
            right: 5%;
            animation-delay: 5s;
        }

        .shape3 {
            width: 300px;
            height: 300px;
            background: #48dbfb;
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }

        .shape4 {
            width: 250px;
            height: 250px;
            background: #1dd1a1;
            bottom: 30%;
            left: 20%;
            animation-delay: 15s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(50px, -50px) rotate(90deg); }
            50% { transform: translate(-30px, 30px) rotate(180deg); }
            75% { transform: translate(30px, 50px) rotate(270deg); }
        }

        .content-wrapper {
            position: relative;
            z-index: 2;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInDown 1s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            text-shadow: 2px 4px 12px rgba(0,0,0,0.3);
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.95);
            font-weight: 300;
        }

        .verification-container {
            margin-bottom: 3rem;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            max-width: 500px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header i {
            font-size: 3.5rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .form-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #64748b;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group label i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .verified-card {
            border: 3px solid #22c55e;
        }

        .verified-icon {
            color: #22c55e !important;
            background: none !important;
            -webkit-text-fill-color: #22c55e !important;
        }

        .user-info {
            margin: 2rem 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .info-item i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .info-item label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.3rem;
            display: block;
        }

        .info-item p {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .verified-badge-large {
            text-align: center;
            padding: 1rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
        }

        .verified-badge-large i {
            margin-right: 0.5rem;
        }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            animation: fadeInUp 1s ease-out;
        }

        .qr-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .qr-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, var(--primary), var(--accent), var(--secondary));
            border-radius: 25px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .qr-card:hover::before {
            opacity: 1;
        }

        .qr-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 80px rgba(0,0,0,0.35);
        }

        .qr-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .qr-code-container {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-code-container svg {
            max-width: 200px;
            height: auto;
            border-radius: 10px;
        }

        .qr-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.8rem;
        }

        .qr-description {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .qr-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 1rem;
            background: #f1f5f9;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .qr-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .qr-meta-item i {
            color: var(--primary);
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .verified-badge {
            background: #dbeafe;
            color: #1e40af;
        }

        .not-verified-badge {
            background: #fef3c7;
            color: #92400e;
        }

        .scan-instruction {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 600;
            margin-top: 1rem;
        }

        .scan-instruction i {
            font-size: 1.2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }

        .cta-section {
            text-align: center;
            margin-top: 4rem;
            padding: 3rem;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .cta-section h2 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: white;
            color: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-decoration: none;
            display: inline-block;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            color: var(--primary);
            text-decoration: none;
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 3rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: white;
            opacity: 0.5;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: white;
            font-size: 1.2rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .qr-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .qr-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            .form-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
        <div class="shape shape4"></div>
    </div>

    <div class="container content-wrapper">
        <div class="hero-section">
            <h1 class="hero-title">
                <i class="fas fa-qrcode"></i> QR Code Details
            </h1>
            <p class="hero-subtitle">Quick access to all our digital resources in one place</p>
        </div>

        <!-- Verification Section -->
        <div id="verificationSection" class="verification-container">
            <?php if($data->otp_verify == "0"){?>
            <div id="initialForm" class="form-card">
                <div class="form-header">
                    <i class="fas fa-user-check"></i>
                    <h3>Register Your Details</h3>
                    <p>Enter your details to continue</p>
                </div>
                <form action="{{ url('qr_code_submit') }}" method="post">
                    @csrf
                    <input type="hidden" name="update_id" value="{{ $data->id }}" />
                    <div class="form-group">
                        <label for="userName"><i class="fas fa-user"></i> Name</label>
                        <input type="text" id="userName" name="userName" class="form-control" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="userMobile"><i class="fas fa-phone"></i> Mobile Number</label>
                        <input type="tel" id="userMobile" name="mobile" class="form-control" placeholder="Enter your mobile number" required pattern="[0-9]{10}">
                    </div>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-check-circle"></i> Submit
                    </button>
                </form>
            </div>

            <?php }else{?>
            <div id="verifiedUser" class="form-card verified-card">
                <div class="form-header">
                    <i class="fas fa-check-circle verified-icon"></i>
                    <h3>Registered User</h3>
                    <p>Your details have been saved</p>
                </div>
                <div class="user-info">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <label>Name</label>
                            <p id="displayName">{{ $data->username }}</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <label>Mobile</label>
                            <p id="displayMobile">{{ $data->mobile }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
        </div>

        <div class="cta-section">
            <h2><i class="fas fa-mobile-alt"></i> Easy Access</h2>
            <p>Simply point your camera at any QR code above to instantly access our services</p>
            <a href="#" class="btn-cta" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                <i class="fas fa-arrow-up"></i> Back to Top
            </a>
        </div>
    </div>

</body>
</html>