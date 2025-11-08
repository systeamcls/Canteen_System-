<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Canteen Central</title>
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Privacy Policy</h1>
            <p>Canteen Central Management System</p>
        </div>

        <div class="content">
            <div class="last-updated">
                <strong>Last Updated:</strong> {{ now()->format('F d, Y') }}
            </div>

            <h2>1. Introduction</h2>
            <p>Welcome to the Canteen Management System ("we," "our," or "the Platform"). We are committed to protecting
                your personal information and your right to privacy. This Privacy Policy explains how we collect, use,
                disclose, and safeguard your information when you use our platform.</p>
            <p>Please read this Privacy Policy carefully. By using the Platform, you agree to the collection and use of
                information in accordance with this policy.</p>

            <h2>2. Information We Collect</h2>

            <h3>2.1 Personal Information You Provide</h3>
            <p>We collect information that you voluntarily provide when you:</p>

            <h4>Account Registration:</h4>
            <ul>
                <li>Full name</li>
                <li>Email address</li>
                <li>Phone number</li>
                <li>Password (encrypted)</li>
                <li>Profile photo (optional)</li>
            </ul>

            <h4>Order Placement:</h4>
            <ul>
                <li>Delivery/contact information</li>
                <li>Payment method details (processed securely through PayMongo)</li>
                <li>Special instructions or preferences</li>
            </ul>

            <h4>Guest Orders:</h4>
            <ul>
                <li>Name</li>
                <li>Phone number</li>
                <li>Email address (optional)</li>
            </ul>

            <h3>2.2 Information Automatically Collected</h3>
            <p>When you use the Platform, we automatically collect:</p>

            <h4>Device Information:</h4>
            <ul>
                <li>IP address</li>
                <li>Browser type and version</li>
                <li>Device type and operating system</li>
                <li>Screen resolution</li>
            </ul>

            <h4>Usage Information:</h4>
            <ul>
                <li>Pages visited</li>
                <li>Time spent on pages</li>
                <li>Click patterns</li>
                <li>Order history</li>
                <li>Search queries</li>
            </ul>

            <h3>2.3 Payment Information</h3>
            <ul>
                <li>Payment processing is handled by <strong>PayMongo</strong>, our secure payment provider</li>
                <li>We do NOT store complete credit card numbers or CVV codes</li>
                <li>We receive confirmation of successful/failed transactions</li>
                <li>Payment method type is stored for record-keeping</li>
            </ul>

            <h2>3. How We Use Your Information</h2>

            <h3>3.1 To Provide Services</h3>
            <p>We use your information to:</p>
            <ul>
                <li>Process and fulfill your orders</li>
                <li>Create and manage your account</li>
                <li>Communicate order status and updates</li>
                <li>Process payments and refunds</li>
                <li>Provide customer support</li>
                <li>Send order confirmations and receipts</li>
            </ul>

            <h3>3.2 To Improve Our Platform</h3>
            <ul>
                <li>Analyze usage patterns and trends</li>
                <li>Understand customer preferences</li>
                <li>Improve user experience and interface</li>
                <li>Develop new features and services</li>
                <li>Perform quality assurance testing</li>
            </ul>

            <h3>3.3 For Communication</h3>
            <p>We may contact you to:</p>
            <ul>
                <li>Send order confirmations and updates</li>
                <li>Respond to your inquiries</li>
                <li>Send administrative messages</li>
                <li>Notify about platform updates or changes</li>
                <li>Send promotional offers (only if you opted in)</li>
            </ul>

            <h3>3.4 For Security and Legal Compliance</h3>
            <ul>
                <li>Prevent fraud and unauthorized access</li>
                <li>Enforce our Terms and Conditions</li>
                <li>Comply with legal obligations</li>
                <li>Protect our rights and property</li>
                <li>Resolve disputes</li>
            </ul>

            <h2>4. How We Share Your Information</h2>

            <h3>4.1 With Vendors</h3>
            <p>When you place an order:</p>
            <ul>
                <li>Vendors receive your name and contact information</li>
                <li>Order details are shared for fulfillment</li>
                <li>Special instructions are visible to vendors</li>
            </ul>

            <h3>4.2 With Payment Processors</h3>
            <ul>
                <li>Payment information is shared with PayMongo for processing</li>
                <li>PayMongo has its own Privacy Policy</li>
                <li>We receive transaction confirmations only</li>
            </ul>

            <h3>4.3 With Service Providers</h3>
            <p>We may share information with trusted third parties who:</p>
            <ul>
                <li>Host our servers</li>
                <li>Provide analytics services</li>
                <li>Assist with customer support</li>
                <li>Help with marketing (if you opted in)</li>
            </ul>
            <p><strong>All service providers are bound by confidentiality agreements.</strong></p>

            <h3>4.4 For Legal Reasons</h3>
            <p>We may disclose information if:</p>
            <ul>
                <li>Required by law or legal process</li>
                <li>To protect our rights or property</li>
                <li>To prevent fraud or illegal activities</li>
                <li>In response to government requests</li>
                <li>To protect user safety</li>
            </ul>

            <h2>5. Data Security</h2>

            <h3>5.1 Security Measures</h3>
            <p>We implement appropriate technical and organizational measures:</p>

            <h4>Technical Security:</h4>
            <ul>
                <li>SSL/TLS encryption for data transmission</li>
                <li>Encrypted password storage</li>
                <li>Secure database access controls</li>
                <li>Regular security audits</li>
                <li>Firewall protection</li>
            </ul>

            <h4>Organizational Security:</h4>
            <ul>
                <li>Limited employee access to personal data</li>
                <li>Confidentiality agreements with staff</li>
                <li>Security training for team members</li>
                <li>Regular security policy reviews</li>
            </ul>

            <h3>5.2 Payment Security</h3>
            <ul>
                <li>Payment data is handled by PCI-DSS compliant PayMongo</li>
                <li>We do NOT store complete credit card information</li>
                <li>Transactions are encrypted end-to-end</li>
            </ul>

            <div class="highlight-box">
                <p><strong>Note:</strong> No system is 100% secure. While we strive to protect your data, we cannot
                    guarantee absolute security.</p>
            </div>

            <h2>6. Your Rights</h2>

            <h3>6.1 Access and Update</h3>
            <ul>
                <li>You can access and update your account information anytime</li>
                <li>Request a copy of your personal data</li>
            </ul>

            <h3>6.2 Deletion</h3>
            <ul>
                <li>Request deletion of your account and data</li>
                <li>Some data may be retained for legal or business purposes</li>
            </ul>

            <h3>6.3 Opt-Out</h3>
            <ul>
                <li>Unsubscribe from marketing emails anytime</li>
                <li>Disable cookies through browser settings</li>
            </ul>

            <h2>7. Children's Privacy</h2>
            <ul>
                <li>Our Platform is not intended for children under 13</li>
                <li>We do not knowingly collect data from children</li>
                <li>Contact us if you believe we have collected child data</li>
            </ul>

            <h2>8. Changes to This Policy</h2>
            <ul>
                <li>We may update this Privacy Policy periodically</li>
                <li>Changes will be posted with a new "Last Updated" date</li>
                <li>Significant changes will be communicated via email</li>
            </ul>

            <div class="contact-box">
                <h3>Contact Us</h3>
                <p>If you have questions about this Privacy Policy, please contact us:</p>
                <p>
                    <strong color="white">Email:</strong> <a href="mailto:kajacms@gmail.com">kajacms@gmail.com</a><br>
                    <strong color="white">Phone:</strong> 09258680512<br>
                    <strong color="white">Address:</strong> LTO Coop Canteen, LTO Central Compound, East Avenue, Quezon
                    City
                </p>
            </div>

            <div class="highlight-box">
                <p><strong color="#111827">By using Canteen Central, you acknowledge that you have read and
                        understood
                        this Privacy
                        Policy.</strong></p>
            </div>
        </div>
    </div>
</body>

</html>
