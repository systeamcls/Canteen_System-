<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTO Canteen Central</title>
    <style>
        :root {
            --primary: #2c3e50;       /* Dark blue-gray from the text */
            --secondary: #e74c3c;     /* Red accent for buttons */
            --light: #ecf0f1;         /* Light background */
            --text: #2c3e50;          /* Main text color */
            --highlight: #3498db;      /* Blue for highlights */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: white;
            color: var(--text);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px 0;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo img {
            height: 40px;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 700;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .login-btn {
            background-color: var(--secondary);
            color: white;
            padding: 8px 25px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .login-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero {
            padding: 100px 0 60px;
            text-align: center;
            background: linear-gradient(to bottom, #f9f9f9, white);
        }
        
        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 20px;
            color: var(--primary);
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 40px;
            color: var(--text);
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 60px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background-color: var(--secondary);
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-secondary:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Food Categories */
        .categories {
            padding: 60px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .category-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            border-left: 4px solid var(--secondary);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .category-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .category-card p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary);
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 60px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--secondary);
        }
        
        .copyright {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 250px;
            }
            
            .nav-links {
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <img src="https://via.placeholder.com/40x40/2c3e50/ffffff?text=LTO" alt="LTO Canteen Logo">
                    <h1>LTO Canteen Central</h1>
                </div>
                <div class="nav-links">
                    <a href="#">Home</a>
                    <a href="#">Menu</a>
                    <a href="#">About</a>
                    <a href="#" class="login-btn">Login</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Discover Amazing Food at LTO Canteen Central</h1>
            <p>Fresh meals, quick service, and delicious options for everyone - visitors and LTO employees alike.</p>
            <div class="cta-buttons">
                <a href="#" class="btn btn-primary">Quick Order</a>
                <a href="#" class="btn btn-secondary">Browse Menu</a>
            </div>
        </div>
    </section>

    <!-- Food Categories -->
    <section class="categories">
        <div class="container">
            <div class="section-title">
                <h2>Our Featured Categories</h2>
            </div>
            <div class="category-grid">
                <div class="category-card">
                    <h3>Fresh Meals</h3>
                    <p>Hot, cheesy, and made to order</p>
                </div>
                <div class="category-card">
                    <h3>Healthy Meals</h3>
                    <p>Fresh ingredients, endless combinations</p>
                </div>
                <div class="category-card">
                    <h3>Premium Coffee</h3>
                    <p>Artisan brewed, energy boosting</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="#">Home</a>
                <a href="#">Menu</a>
                <a href="#">About Us</a>
                <a href="#">Contact</a>
            </div>
            <p class="copyright">© 2023 LTO Canteen Central. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>