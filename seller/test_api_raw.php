<!DOCTYPE html>
<html>
<head>
    <title>Raw API Test</title>
</head>
<body>
    <h2>Raw API Response Test</h2>
    <button onclick="testRawAPI()">Test Raw API Response</button>
    <div id="results"></div>

    <script>
        async function testRawAPI() {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '<p>Testing...</p>';
            
            try {
                const response = await fetch('http://localhost/Core1_ecommerce/seller/api/auth/me.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                // Get the raw response text first
                const rawText = await response.text();
                
                resultsDiv.innerHTML = `
                    <h3>Raw API Response</h3>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Content-Type:</strong> ${response.headers.get('content-type')}</p>
                    <p><strong>Raw Response:</strong></p>
                    <pre style="background: #f5f5f5; padding: 10px; white-space: pre-wrap;">${rawText}</pre>
                `;
                
                // Try to parse as JSON
                try {
                    const jsonData = JSON.parse(rawText);
                    resultsDiv.innerHTML += `
                        <h4>Parsed JSON:</h4>
                        <pre style="background: #e8f5e8; padding: 10px;">${JSON.stringify(jsonData, null, 2)}</pre>
                    `;
                } catch (jsonError) {
                    resultsDiv.innerHTML += `
                        <h4 style="color: red;">JSON Parse Error:</h4>
                        <p style="color: red;">${jsonError.message}</p>
                    `;
                }
                
            } catch (error) {
                resultsDiv.innerHTML = `
                    <h3 style="color: red;">Fetch Error</h3>
                    <p style="color: red;">${error.message}</p>
                `;
            }
        }
    </script>
</body>
</html>