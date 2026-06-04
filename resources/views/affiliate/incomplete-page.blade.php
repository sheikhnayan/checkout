<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Ready — CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 20px;
        }
        .container-incomplete {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #fff3cd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .icon-wrapper i {
            font-size: 40px;
            color: #ffc107;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 12px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .missing-fields {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: left;
        }
        .missing-fields-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .missing-fields ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .missing-fields li {
            padding: 6px 0;
            color: #555;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .missing-fields li:before {
            content: '✕';
            color: #dc3545;
            font-weight: bold;
            display: inline-block;
            width: 20px;
        }
        .action-text {
            font-size: 14px;
            color: #666;
            margin: 30px 0;
            padding: 20px;
            background: #e7f3ff;
            border-radius: 8px;
            border-left: 4px solid #0066cc;
        }
        .action-text strong {
            color: #0066cc;
        }
        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .footer-text {
            font-size: 12px;
            color: #999;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container-incomplete">
        <div class="icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h1>Page Not Ready</h1>

        @if($type === 'affiliate')
            <p class="subtitle">
                This affiliate page is not yet fully customized and is not accessible to the public.
            </p>
        @elseif($type === 'website')
            <p class="subtitle">
                The venue configuration for this page is incomplete.
            </p>
        @else
            <p class="subtitle">
                This page is not yet ready to be viewed publicly.
            </p>
        @endif

        @if(!empty($missingFields))
        <div class="missing-fields">
            <div class="missing-fields-title">Missing Information:</div>
            <ul>
                @foreach($missingFields as $field)
                    <li>{{ $field }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="action-text">
            <strong>What's next?</strong><br>
            @if($type === 'affiliate')
                The affiliate owner needs to complete their profile customization in their account settings. Once all required information is filled in, this page will be live and accessible to the public.
            @elseif($type === 'website')
                The venue administrator needs to complete the page configuration in their settings. Once all required information is set up, this page will be ready to display.
            @else
                Please contact the affiliate or venue administrator to complete the page setup.
            @endif
        </div>

        <a href="/" class="btn-home">
            <i class="fas fa-home me-2"></i>Return to Home
        </a>

        <p class="footer-text">
            © {{ date('Y') }} CartVIP. All rights reserved.
        </p>
    </div>
</body>
</html>
