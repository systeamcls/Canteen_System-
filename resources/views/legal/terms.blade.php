<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Canteen Central</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-family: 'Georgia', 'Times New Roman', serif;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
            color: #1f2937;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.1), 0 10px 10px -5px rgba(249, 115, 22, 0.04);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 48px 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.95;
        }

        .content {
            padding: 48px 40px;
        }

        .last-updated {
            background: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 16px 20px;
            margin-bottom: 32px;
            border-radius: 8px;
            font-size: 0.95em;
            color: #9a3412;
        }

        .last-updated strong {
            color: #7c2d12;
        }

        h2 {
            color: #f97316;
            margin: 40px 0 20px 0;
            font-size: 1.8em;
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 600;
            padding-bottom: 12px;
            border-bottom: 3px solid #fed7aa;
        }

        h3 {
            color: #ea580c;
            margin: 28px 0 16px 0;
            font-size: 1.3em;
            font-weight: 600;
        }

        h4 {
            color: #9a3412;
            margin: 20px 0 12px 0;
            font-size: 1.1em;
            font-weight: 600;
        }

        p {
            margin-bottom: 16px;
            color: #374151;
            font-size: 1em;
        }

        ul {
            margin: 16px 0 24px 24px;
            list-style: none;
        }

        li {
            margin-bottom: 12px;
            color: #374151;
            padding-left: 28px;
            position: relative;
        }

        li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #f97316;
            font-weight: bold;
        }

        strong {
            color: #1f2937;
            font-weight: 600;
        }

        .highlight-box {
            background: #fff7ed;
            border: 2px solid #fed7aa;
            padding: 24px;
            margin: 32px 0;
            border-radius: 12px;
        }

        .highlight-box p {
            margin: 0;
            color: #9a3412;
            font-weight: 500;
        }

        .contact-box {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 32px;
            border-radius: 12px;
            margin: 32px 0;
        }

        .contact-box h3 {
            color: white;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 12px;
        }

        .contact-box p {
            color: #d1d5db;
            margin-bottom: 12px;
        }

        .contact-box a {
            color: #fed7aa;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-box a:hover {
            color: #f97316;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .container {
                border-radius: 0;
            }

            .header {
                padding: 32px 24px;
            }

            .header h1 {
                font-size: 2em;
            }

            .content {
                padding: 32px 24px;
            }

            h2 {
                font-size: 1.5em;
            }
        }

        .back-link {
            display: inline-block;
            background: #f97316;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 24px;
            transition: all 0.2s;
        }

        .back-link:hover {
            background: #ea580c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Terms and Conditions</h1>
            <p>Canteen Central Management System</p>
        </div>

        <div class="content">
            <div class="last-updated">
                <strong>Last Updated:</strong> {{ now()->format('F d, Y') }}
            </div>

            <h2>1. Introduction</h2>
            <p>Welcome to the Canteen Management System ("the Platform"). By accessing or using our platform, you agree
                to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do
                not use our services.</p>

            <h2>2. Definitions</h2>
            <ul>
                <li><strong>"Platform"</strong> refers to the Canteen Management System website and mobile application
                </li>
                <li><strong>"User"</strong> refers to anyone who accesses or uses the Platform</li>
                <li><strong>"Vendor/Tenant"</strong> refers to food stall owners who sell products through the Platform
                </li>
                <li><strong>"Customer"</strong> refers to users who purchase products through the Platform</li>
                <li><strong>"Services"</strong> refers to all features and functionalities provided by the Platform</li>
            </ul>

            <h2>3. User Accounts</h2>

            <h3>3.1 Account Registration</h3>
            <ul>
                <li>You must provide accurate and complete information when creating an account</li>
                <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                <li>You must be at least 13 years old to create an account</li>
                <li>Guest checkout is available for users who prefer not to register</li>
            </ul>

            <h3>3.2 Account Security</h3>
            <ul>
                <li>You are responsible for all activities that occur under your account</li>
                <li>Notify us immediately of any unauthorized use of your account</li>
                <li>We reserve the right to suspend or terminate accounts that violate these terms</li>
            </ul>

            <h2>4. Ordering and Payment</h2>

            <h3>4.1 Order Placement</h3>
            <ul>
                <li>All orders are subject to availability of products</li>
                <li>Prices are displayed in Philippine Peso (PHP)</li>
                <li>Orders are confirmed only after successful payment processing</li>
                <li>You will receive an order confirmation with an order number</li>
            </ul>

            <h3>4.2 Payment Methods</h3>
            <p>We accept the following payment methods:</p>
            <ul>
                <li>GCash</li>
                <li>PayMaya</li>
                <li>Credit/Debit Cards</li>
                <li>Cash (for on-site orders)</li>
            </ul>

            <h3>4.3 Payment Processing</h3>
            <ul>
                <li>All online payments are processed through PayMongo, our secure payment provider</li>
                <li>Payment information is encrypted and handled securely</li>
                <li>We do not store your complete card details on our servers</li>
            </ul>

            <h3>4.4 Pricing</h3>
            <ul>
                <li>All prices are inclusive of applicable taxes unless otherwise stated</li>
                <li>Prices are subject to change without prior notice</li>
                <li>The price at the time of order placement will be honored</li>
            </ul>

            <h2>5. Order Fulfillment</h2>

            <h3>5.1 Service Types</h3>
            <ul>
                <li><strong>Dine-in:</strong> Orders to be consumed at the canteen premises</li>
                <li><strong>Take-away:</strong> Orders to be picked up and consumed elsewhere</li>
            </ul>

            <h3>5.2 Preparation Time</h3>
            <ul>
                <li>Estimated preparation times are provided as guidance only</li>
                <li>Actual preparation times may vary based on order volume and complexity</li>
                <li>You will be notified when your order is ready for pickup</li>
            </ul>

            <h3>5.3 Order Collection</h3>
            <ul>
                <li>Present your order number when collecting your order</li>
                <li>Orders not collected within reasonable time may be cancelled</li>
                <li>Uncollected orders are not eligible for refunds</li>
            </ul>

            <h2>6. Cancellations and Refunds</h2>

            <h3>6.1 Order Cancellation</h3>
            <ul>
                <li>Orders can be cancelled before they enter "processing" status</li>
                <li>Once an order is being prepared, cancellation may not be possible</li>
                <li>Contact the vendor immediately if you need to cancel</li>
            </ul>

            <h3>6.2 Refund Policy</h3>
            <ul>
                <li>Refunds for cancelled orders (before processing) will be processed within 5-7 business days</li>
                <li>Refund method will match the original payment method</li>
                <li>Processing fees may be deducted from refunds where applicable</li>
            </ul>

            <h3>6.3 Order Issues</h3>
            <p>If there's an issue with your order:</p>
            <ul>
                <li>Contact the vendor or administrator immediately</li>
                <li>Provide your order number and details of the issue</li>
                <li>We will investigate and resolve issues on a case-by-case basis</li>
            </ul>

            <h2>7. Product Information</h2>

            <h3>7.1 Accuracy</h3>
            <ul>
                <li>We strive to provide accurate product descriptions and images</li>
                <li>Actual products may vary slightly from images shown</li>
                <li>Vendors are responsible for the accuracy of their product information</li>
            </ul>

            <h3>7.2 Food Safety</h3>
            <ul>
                <li>All vendors must comply with local food safety regulations</li>
                <li>We are not responsible for food quality or preparation by vendors</li>
                <li>Report any food safety concerns immediately</li>
            </ul>

            <h3>7.3 Allergies and Dietary Requirements</h3>
            <ul>
                <li>Check product descriptions for ingredient information</li>
                <li>Contact vendors directly for specific allergy or dietary questions</li>
                <li>We cannot guarantee that products are free from allergens</li>
            </ul>

            <h2>8. User Conduct</h2>
            <p>You agree NOT to:</p>
            <ul>
                <li>Use the Platform for any illegal purposes</li>
                <li>Impersonate any person or entity</li>
                <li>Interfere with or disrupt the Platform's operation</li>
                <li>Attempt to gain unauthorized access to any part of the Platform</li>
                <li>Post false reviews or ratings</li>
                <li>Harass, abuse, or harm vendors or other users</li>
                <li>Use automated systems to access the Platform without permission</li>
            </ul>

            <h2>9. Reviews and Ratings</h2>

            <h3>9.1 Posting Reviews</h3>
            <ul>
                <li>Only customers who have completed orders can leave reviews</li>
                <li>Reviews must be honest and based on your actual experience</li>
                <li>Reviews must not contain offensive, defamatory, or inappropriate content</li>
            </ul>

            <h3>9.2 Review Moderation</h3>
            <ul>
                <li>We reserve the right to remove reviews that violate these terms</li>
                <li>Reviews are the opinions of individual users, not the Platform</li>
            </ul>

            <h2>10. Intellectual Property</h2>

            <h3>10.1 Platform Content</h3>
            <ul>
                <li>All content on the Platform (text, graphics, logos, images) is our property or our licensors'</li>
                <li>You may not copy, reproduce, or distribute Platform content without permission</li>
            </ul>

            <h3>10.2 User Content</h3>
            <ul>
                <li>You retain ownership of content you upload (profile pictures, reviews)</li>
                <li>By uploading content, you grant us a license to use it on the Platform</li>
            </ul>

            <h2>11. Limitation of Liability</h2>

            <h3>11.1 Service Availability</h3>
            <ul>
                <li>We strive for 24/7 availability but do not guarantee uninterrupted service</li>
                <li>We are not liable for service interruptions or technical issues</li>
            </ul>

            <h3>11.2 Food Quality and Safety</h3>
            <ul>
                <li>Vendors are solely responsible for food preparation and quality</li>
                <li>We are not liable for foodborne illness or product defects</li>
            </ul>

            <h3>11.3 Maximum Liability</h3>
            <ul>
                <li>Our liability is limited to the amount paid for the specific order in question</li>
                <li>We are not liable for indirect, incidental, or consequential damages</li>
            </ul>

            <h2>12. Privacy and Data Protection</h2>
            <p>Your privacy is important to us. Please review our <a href="{{ route('legal.privacy') }}"
                    style="color: #f97316; text-decoration: none; font-weight: 600;">Privacy Policy</a> to understand how
                we collect, use, and protect your personal information.</p>

            <h2>13. Dispute Resolution</h2>

            <h3>13.1 Governing Law</h3>
            <ul>
                <li>These Terms are governed by the laws of the Philippines</li>
                <li>Disputes will be resolved in the courts of Quezon City</li>
            </ul>

            <div class="contact-box">
                <h3>Contact Information</h3>
                <p>If you have questions about these Terms and Conditions, please contact us:</p>
                <p>
                    <strong color="white">Email:</strong> <a href="mailto:kajacms@gmail.com">kajacms@gmail.com</a><br>
                    <strong color="white">Phone:</strong> 09258680512<br>
                    <strong color="white">Address:</strong> LTO Coop Canteen, LTO Central Compound, East Avenue, Quezon
                </p>
            </div>

            <div class="highlight-box">
                <p><strong color="#111827">By using Canteen Central, you acknowledge that you have read, understood, and
                        agree to be
                        bound by these Terms and Conditions.</strong></p>
            </div>
        </div>
    </div>
</body>

</html>
