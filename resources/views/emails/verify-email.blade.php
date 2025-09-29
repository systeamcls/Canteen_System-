<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Canteen Central</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #FF6B35 0%, #E55B2B 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 1px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo-text {
            color: white;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .email-tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }
        
        .email-body {
            padding: 50px 40px;
            text-align: center;
        }
        
        .welcome-text {
            font-size: 32px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #1F2937 0%, #FF6B35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .intro-text {
            font-size: 18px;
            color: #6B7280;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .verification-card {
            background: linear-gradient(135deg, #FFF4F1 0%, #FEF2F2 100%);
            border: 2px solid rgba(255, 107, 53, 0.2);
            border-radius: 20px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .verification-text {
            color: #FF6B35;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 25px;
        }
        
        .verify-button {
            background: linear-gradient(135deg, #FF6B35 0%, #E55B2B 100%);
            color: white;
            text-decoration: none;
            padding: 18px 40px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 18px;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 107, 53, 0.4);
        }
        
        .security-note {
            background: #F3F4F6;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            border-left: 4px solid #FF6B35;
        }
        
        .security-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .security-text {
            color: #6B7280;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .alternative-link {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .alternative-text {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .alternative-url {
            background: white;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            padding: 12px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #374151;
        }
        
        .email-footer {
            background: #F9FAFB;
            padding: 40px;
            text-align: center;
            border-top: 1px solid #E5E7EB;
        }
        
        .footer-text {
            color: #9CA3AF;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: #E5E7EB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #6B7280;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: #FF6B35;
            color: white;
        }
        
        .copyright {
            color: #9CA3AF;
            font-size: 12px;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 600px) {
            .email-wrapper {
                padding: 20px 0;
            }
            
            .email-container {
                margin: 0 15px;
                border-radius: 16px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .welcome-text {
                font-size: 24px;
            }
            
            .intro-text {
                font-size: 16px;
            }
            
            .verify-button {
                padding: 16px 30px;
                font-size: 16px;
            }
            
            .email-footer {
                padding: 30px 20px;
            }
            
            .logo-text {
                font-size: 24px;
            }
            
            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo">
                    <div class="logo-icon">CC</div>
                    <div class="logo-text">Canteen Central</div>
                </div>
                <div class="email-tagline">Fresh • Fast • Delicious</div>
            </div>
            
            <!-- Body -->
            <div class="email-body">
                <h1 class="welcome-text">Welcome to Canteen Central!</h1>
                
                <p class="intro-text">
                    Hi {{ $user->name }},<br>
                    Thank you for joining our food ordering community. We're excited to have you on board!
                </p>
                
                <div class="verification-card">
                    <p class="verification-text">
                        🍽️ One more step to start ordering delicious food
                    </p>
                    <a href="{{ $url }}" class="verify-button">
                        Verify My Email Address
                    </a>
                </div>
                
                <div class="security-note">
                    <div class="security-title">
                        🔒 Security Notice
                    </div>
                    <p class="security-text">
                        This verification link will expire in 1 hour for your security. 
                        If you didn't create an account with us, please ignore this email.
                    </p>
                </div>
                
                <div class="alternative-link">
                    <p class="alternative-text">
                        Having trouble with the button above? Copy and paste this link into your browser:
                    </p>
                    <div class="alternative-url">{{ $url }}</div>
                </div>
                
                <p style="color: #6B7280; font-size: 16px; margin-top: 30px;">
                    Once verified, you'll be able to:
                </p>
                <ul style="list-style: none; padding: 0; color: #374151; margin: 20px 0;">
                    <li style="margin: 8px 0;">✅ Browse our delicious menu</li>
                    <li style="margin: 8px 0;">✅ Place and track orders</li>
                    <li style="margin: 8px 0;">✅ Access your order history</li>
                    <li style="margin: 8px 0;">✅ Enjoy fast and convenient ordering</li>
                </ul>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <p class="footer-text">
                    Need help? Contact us at support@canteencentral.com
                </p>
                
                <div class="social-links">
                    <a href="#" class="social-link">📧</a>
                    <a href="#" class="social-link">📱</a>
                    <a href="#" class="social-link">🌐</a>
                </div>
                
                <p class="copyright">
                    © {{ date('Y') }} Canteen Central. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>