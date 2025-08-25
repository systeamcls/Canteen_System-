<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <style>
        body { font-family: Arial; padding: 50px; text-align: center; background: #f0fdf4; }
        .success { color: #10b981; font-size: 2rem; margin: 20px; }
        .details { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .btn { background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="success">Order Placed Successfully!</div>
    <div class="details">
        <p><strong>Order ID:</strong> {{ $orderGroup->id }}</p>
        <p><strong>Total:</strong> ₱{{ number_format($orderGroup->amount_total / 100, 2) }}</p>
        <p><strong>Status:</strong> {{ $orderGroup->payment_status }}</p>
    </div>
    <a href="{{ route('menu.index') }}" class="btn">Back to Menu</a>
</body>
</html>