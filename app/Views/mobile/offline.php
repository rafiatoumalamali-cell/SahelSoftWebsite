<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - SahelSoft</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .offline-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .offline-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .offline-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .offline-message {
            font-size: 1rem;
            margin-bottom: 25px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }

        .offline-tips {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .tip-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .tip-item:last-child {
            margin-bottom: 0;
        }

        .tip-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .retry-button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .retry-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff6b6b;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }

        .cached-content {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 480px) {
            .offline-container {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .offline-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">📱</div>
        <h1 class="offline-title">You're Offline</h1>
        <p class="offline-message">
            It looks like you've lost your internet connection. Don't worry - some content is still available!
        </p>
        
        <div class="status-indicator">
            <span class="status-dot"></span>
            <span>Checking connection...</span>
        </div>

        <div class="offline-tips">
            <div class="tip-item">
                <span class="tip-icon">💾</span>
                <span>Cached content is available</span>
            </div>
            <div class="tip-item">
                <span class="tip-icon">🔄</span>
                <span>Actions will sync when back online</span>
            </div>
            <div class="tip-item">
                <span class="tip-icon">📋</span>
                <span>You can still work on offline tasks</span>
            </div>
        </div>

        <button class="retry-button" onclick="checkConnection()">
            Try Again
        </button>

        <div class="cached-content">
            <strong>Available Offline:</strong> Dashboard, Projects, Messages, and recent data
        </div>
    </div>

    <script>
        let connectionCheckInterval;
        
        function checkConnection() {
            const statusDot = document.querySelector('.status-dot');
            const statusText = document.querySelector('.status-indicator span:last-child');
            
            statusDot.style.background = '#ffd93d';
            statusText.textContent = 'Checking connection...';
            
            fetch('/api/latest-data', { 
                method: 'GET',
                cache: 'no-cache'
            })
            .then(response => {
                if (response.ok) {
                    statusDot.style.background = '#51cf66';
                    statusText.textContent = 'Connection restored!';
                    
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1000);
                } else {
                    throw new Error('Network error');
                }
            })
            .catch(error => {
                statusDot.style.background = '#ff6b6b';
                statusText.textContent = 'Still offline';
                
                // Continue checking
                scheduleNextCheck();
            });
        }
        
        function scheduleNextCheck() {
            // Check every 5 seconds
            connectionCheckInterval = setTimeout(checkConnection, 5000);
        }
        
        // Start checking connection immediately
        checkConnection();
        
        // Also check when user comes back to the tab
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                checkConnection();
            }
        });
        
        // Check when network status changes
        window.addEventListener('online', () => {
            checkConnection();
        });
        
        window.addEventListener('offline', () => {
            const statusDot = document.querySelector('.status-dot');
            const statusText = document.querySelector('.status-indicator span:last-child');
            statusDot.style.background = '#ff6b6b';
            statusText.textContent = 'Connection lost';
        });
        
        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (connectionCheckInterval) {
                clearTimeout(connectionCheckInterval);
            }
        });
    </script>
</body>
</html>
