<!-- AI Assistant Chat Widget -->
<div id="ai-assistant" class="position-fixed bottom-0 end-0 m-3" style="z-index: 1050;">
    <div class="card shadow" style="width: 350px; display: none;" id="chat-widget">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">AI Assistant</h6>
            <button class="btn btn-sm btn-outline-light" onclick="toggleChat()">×</button>
        </div>
        <div class="card-body" style="height: 300px; overflow-y: auto;" id="chat-messages">
            <div class="message ai-message mb-2">
                <small class="text-muted">AI Assistant</small>
                <div class="bg-light p-2 rounded">
                    Hi! I'm here to help with your payment questions. How can I assist you today?
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="input-group">
                <input type="text" class="form-control" id="chat-input" placeholder="Ask me anything...">
                <button class="btn btn-primary" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>
    
    <!-- Chat Toggle Button -->
    <button class="btn btn-primary rounded-circle shadow" id="chat-toggle" onclick="toggleChat()" style="width: 60px; height: 60px;">
        <i class="bi bi-chat-dots"></i>
    </button>
</div>

<script>
function toggleChat() {
    const widget = document.getElementById('chat-widget');
    const toggle = document.getElementById('chat-toggle');
    
    if (widget.style.display === 'none') {
        widget.style.display = 'block';
        toggle.style.display = 'none';
    } else {
        widget.style.display = 'none';
        toggle.style.display = 'block';
    }
}

function sendMessage() {
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const query = input.value.trim();
    
    if (!query) return;
    
    // Add user message
    const userMessage = document.createElement('div');
    userMessage.className = 'message user-message mb-2 text-end';
    userMessage.innerHTML = `
        <small class="text-muted">You</small>
        <div class="bg-primary text-white p-2 rounded d-inline-block">
            ${query}
        </div>
    `;
    messages.appendChild(userMessage);
    
    input.value = '';
    
    // Show typing indicator
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'message ai-message mb-2';
    typingIndicator.id = 'typing-indicator';
    typingIndicator.innerHTML = `
        <small class="text-muted">AI Assistant</small>
        <div class="bg-light p-2 rounded">
            <span class="typing-dots">Typing...</span>
        </div>
    `;
    messages.appendChild(typingIndicator);
    messages.scrollTop = messages.scrollHeight;
    
    // Send to AI service
    fetch('{{ route("student.ai-assistant.get-assistance") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ query: query })
    })
    .then(response => response.json())
    .then(data => {
        // Remove typing indicator
        document.getElementById('typing-indicator').remove();
        
        // Add AI response
        const aiMessage = document.createElement('div');
        aiMessage.className = 'message ai-message mb-2';
        aiMessage.innerHTML = `
            <small class="text-muted">AI Assistant</small>
            <div class="bg-light p-2 rounded">
                ${data.response}
                ${data.suggested_actions ? `
                    <div class="mt-2">
                        <small class="text-muted">Suggested actions:</small>
                        ${data.suggested_actions.map(action => `<span class="badge bg-secondary me-1">${action}</span>`).join('')}
                    </div>
                ` : ''}
            </div>
        `;
        messages.appendChild(aiMessage);
        messages.scrollTop = messages.scrollHeight;
    })
    .catch(error => {
        document.getElementById('typing-indicator').remove();
        console.error('Error:', error);
    });
}

// Allow Enter key to send message
document.getElementById('chat-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Load payment insights on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("student.ai-assistant.payment-insights") }}')
        .then(response => response.json())
        .then(data => {
            if (data.recommendations && data.recommendations.length > 0) {
                const messages = document.getElementById('chat-messages');
                const insightMessage = document.createElement('div');
                insightMessage.className = 'message ai-message mb-2';
                insightMessage.innerHTML = `
                    <small class="text-muted">AI Insights</small>
                    <div class="bg-info text-white p-2 rounded">
                        <strong>Personalized Recommendations:</strong><br>
                        ${data.recommendations.map(rec => `• ${rec}`).join('<br>')}
                    </div>
                `;
                messages.appendChild(insightMessage);
            }
        });
});
</script>

<style>
.typing-dots::after {
    content: '...';
    animation: typing 1.5s infinite;
}

@keyframes typing {
    0%, 60% { content: '...'; }
    20% { content: '.'; }
    40% { content: '..'; }
}

[data-theme="dark"] #chat-widget .card {
    background-color: var(--bs-dark) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

[data-theme="dark"] #chat-widget .bg-light {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] #chat-widget .text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
}
</style>