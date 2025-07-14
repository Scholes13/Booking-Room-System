<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fonnte WhatsApp Test</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Fonnte WhatsApp Test</h1>
        
        <div id="status" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-gray-700">Click the button below to send a test message to your WhatsApp group.</p>
        </div>
        
        <div class="flex justify-center space-x-4">
            <button id="sendTestMessage" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                Send Test Message
            </button>
            
            <button id="sendTestLinkMessage" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                Test Clickable Link
            </button>
        </div>
        
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">How to use:</h2>
            <ol class="list-decimal pl-5 space-y-2 text-gray-700">
                <li>Click the "Send Test Message" button above.</li>
                <li>The test will use the Fonnte API to send a message to the configured WhatsApp group ID.</li>
                <li>The result will be displayed in the status area.</li>
                <li>If successful, you should receive a message in your WhatsApp group.</li>
            </ol>
        </div>
    </div>
    
    <script>
        document.getElementById('sendTestMessage').addEventListener('click', function() {
            const statusDiv = document.getElementById('status');
            statusDiv.innerHTML = '<p class="text-blue-600">Sending test message...</p>';
            
            fetch('/test-fonnte', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    statusDiv.innerHTML = `<p class="text-green-600">✅ Success! Message sent to WhatsApp group.</p>
                                          <p class="mt-2 text-gray-700">Response: ${JSON.stringify(data.response)}</p>`;
                } else {
                    statusDiv.innerHTML = `<p class="text-red-600">❌ Error: ${data.error}</p>
                                          <p class="mt-2 text-gray-700">Details: ${JSON.stringify(data.response)}</p>`;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = `<p class="text-red-600">❌ Error: ${error.message}</p>`;
            });
        });

        document.getElementById('sendTestLinkMessage').addEventListener('click', function() {
            const statusDiv = document.getElementById('status');
            statusDiv.innerHTML = '<p class="text-blue-600">Sending test message with clickable link...</p>';
            
            fetch('/test-fonnte-link', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    statusDiv.innerHTML = `<p class="text-green-600">✅ Success! Message with clickable link sent to WhatsApp group.</p>
                                          <p class="mt-2 text-gray-700">Response: ${JSON.stringify(data.response)}</p>`;
                } else {
                    statusDiv.innerHTML = `<p class="text-red-600">❌ Error: ${data.error}</p>
                                          <p class="mt-2 text-gray-700">Details: ${JSON.stringify(data.response)}</p>`;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = `<p class="text-red-600">❌ Error: ${error.message}</p>`;
            });
        });
    </script>
</body>
</html> 