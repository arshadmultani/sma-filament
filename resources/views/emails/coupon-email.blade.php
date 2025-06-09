<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kofol Coupon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #079669;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
        }
        .coupon-box {
            background-color: #f3f4f6;
            border: 2px dashed #079669;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            border-radius: 8px;
        }
        .customer-info {
            background-color: #f9fafb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #1a56db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Kofol Swarna Varsha </h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $customerName }},</h2>
            
            <p>Thank you for participating in our Kofol Swarna Varsha! We're excited to provide you with your exclusive coupon.</p>

            <div class="coupon-box">
                <h3>Your Coupon Code</h3>
                <p style="font-size: 24px; font-weight: bold; color: #079669;">
                    {{ $couponCode ?? 'KOFOL-2024-001' }}
                </p>
                <!-- <p>Valid until: {{ $expiryDate ?? 'December 31, 2024' }}</p> -->
            </div>

            <div class="customer-info">
                <p><strong>Address:</strong> {{ $customerAddress }}, {{ $customerTown }}, {{ $headquarterName }}</p>
            </div>

            <!-- <p>To redeem your coupon, please visit any of our participating locations and present this email or the coupon code.</p>

            <div style="text-align: center;">
                <a href="{{ $redeemUrl ?? '#' }}" class="button">Redeem Your Coupon</a>
            </div>

            <p><strong>Terms and Conditions:</strong></p>
            <ul>
                <li>This coupon is valid for one-time use only</li>
                <li>Cannot be combined with other offers</li>
                <li>Subject to availability</li>
                <li>Valid only at participating locations</li>
            </ul>
        </div> -->

        <!-- <div class="footer">
            <p>If you have any questions, please contact our customer service.</p>
            <p>© {{ date('Y') }} Kofol. All rights reserved.</p>
        </div> -->
    </div>
</body>
</html>