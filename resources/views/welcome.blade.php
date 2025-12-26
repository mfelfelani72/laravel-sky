<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Laravel Project</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            text-align: center;
        }
        
        .logo {
            font-size: 3rem;
            font-weight: bold;
            color: #ef4444;
            margin-bottom: 2rem;
        }
        
        .title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .description {
            font-size: 1.25rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
            border: 2px solid #3b82f6;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        .btn-secondary {
            background: transparent;
            color: #4b5563;
            border: 2px solid #d1d5db;
        }
        
        .btn-secondary:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .github-btn {
            background: #000;
            color: white;
            border: 2px solid #000;
        }
        
        .github-btn:hover {
            background: #333;
            border-color: #333;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }
        
        .feature {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: left;
        }
        
        .feature h3 {
            color: #111827;
            margin-bottom: 0.5rem;
        }
        
        .feature p {
            color: #6b7280;
            line-height: 1.5;
        }
        
        .footer {
            margin-top: 3rem;
            color: #9ca3af;
            font-size: 0.875rem;
        }
        
        @media (max-width: 640px) {
            .title {
                font-size: 2rem;
            }
            
            .description {
                font-size: 1.125rem;
            }
            
            .buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            Laravel
        </div>
        
        <h1 class="title">Welcome to My Laravel Project</h1>
        
        <p class="description">
            A modern Laravel application built with the latest features and best practices.
            This project includes authentication, API support, and a clean architecture.
        </p>
        
        <div class="buttons">
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                Go to Dashboard
            </a>
            
            <a href="{{ route('login') }}" class="btn btn-secondary">
                Login
            </a>
            
            <a href="https://github.com/YOUR_USERNAME/YOUR_REPOSITORY" target="_blank" class="btn github-btn">
                View on GitHub
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                </svg>
            </a>
        </div>
        
        <div class="features">
            <div class="feature">
                <h3>Modern Stack</h3>
                <p>Built with Laravel 10+, Tailwind CSS, and Vue.js/React for a modern development experience.</p>
            </div>
            
            <div class="feature">
                <h3>Authentication Ready</h3>
                <p>Complete authentication system with registration, login, password reset, and email verification.</p>
            </div>
            
            <div class="feature">
                <h3>API Support</h3>
                <p>RESTful API with Sanctum authentication for building mobile apps and third-party integrations.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} My Laravel Project. All rights reserved.</p>
        </div>
    </div>
</body>
</html>