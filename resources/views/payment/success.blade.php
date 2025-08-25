<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - LTO Canteen Central</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0fdf4;
            min-height: 100vh;
            padding: 120px 20px 60px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: #10b981;
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-icon svg {
            width: 50px;
            height: 50px;
            color: white;
        }
        
        h1 {
            color: #065f46;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .subtitle {
            color: #047857;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .processing-message {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #d1fae5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #d1fae5;
            border-top: 2px solid #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }
        
        .btn-primary {
            background: #10b981;
            color: white;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .next-steps {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: left;
            border: 1px solid #d1fae5;
        }
        
        .next-steps h3 {
            color: #047857;
            margin-bottom: 15px;
        }
        
        .next-steps ul {
            color: #065f46;
            line-height: 1.6;
            list-style: none;
        }
        
        .next-steps li {
            margin-bottom: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 60px 15px 40px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 200px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Success Icon -->
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <!-- Success Message -->
        <h1>Payment Successful!</h1>
        <p class="subtitle">
            Your payment has been processed successfully. Your order is being prepared.
        </p>

        <!-- Processing Message -->
        <div id="processingMessage" class="processing-message">
            <div class="spinner"></div>
            <span style="color: #047857;">Processing your order details...</span>
        </div>

        <!-- Action Buttons -->
        <div class="buttons">
            <a href="/menu" class="btn btn-primary">Continue Shopping</a>
            <a href="/home" class="btn btn-secondary">Back to Home</a>
        </div>

        <!-- What's Next -->
        <div class="next-steps">
            <h3>What happens next?</h3>
            <ul>
                <li>✓ We've received your payment</li>
                <li>✓ Your order is being processed</li>
                <li>✓ You'll receive an email confirmation shortly</li>
                <li>✓ The kitchen will start preparing your food</li>
            </ul>
        </div>
    </div>

    <script>
        // Check for pending payment and clear processing message
        document.addEventListener('DOMContentLoaded', function() {
            const pendingPayment = sessionStorage.getItem('pendingPayment');
            if (pendingPayment) {
                sessionStorage.removeItem('pendingPayment');
                
                setTimeout(() => {
                    const processingMessage = document.getElementById('processingMessage');
                    if (processingMessage) {
                        processingMessage.style.display = 'none';
                    }
                }, 3000);
            } else {
                const processingMessage = document.getElementById('processingMessage');
                if (processingMessage) {
                    processingMessage.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>