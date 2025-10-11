<!-- AI Assistant Chat Widget -->
<div id="ai-assistant" class="position-fixed bottom-0 end-0" style="z-index: 1050;">
    <div class="card shadow ai-chat-widget" style="display: none;" id="chat-widget">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">AI Assistant</h6>
            <button class="btn btn-sm btn-outline-light ai-close-btn" onclick="toggleChat()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="card-body ai-chat-body" id="chat-messages">
            <div class="message ai-message mb-2">
                <small class="text-muted">AI Assistant</small>
                <div class="bg-light p-2 rounded">
                    Hi! I'm here to help with your payment questions. How can I assist you today?
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="input-group">
                <input type="text" class="form-control ai-chat-input" id="chat-input" placeholder="Ask me anything...">
                <button class="btn btn-primary ai-send-btn" onclick="sendMessage()">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Chat Toggle Button -->
    <button class="btn btn-primary rounded-circle shadow ai-toggle-btn" id="chat-toggle" onclick="toggleChat()">
        <i class="bi bi-chat-dots"></i>
    </button>
</div>

<script>
function toggleChat() {
    const widget = document.getElementById('chat-widget');
    const toggle = document.getElementById('chat-toggle');
    
    console.log('Toggling chat, current display:', widget.style.display);
    
    if (widget.style.display === 'none' || widget.style.display === '') {
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
    
    console.log('Sending message:', query);
    
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
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('AI Response:', data);
        // Remove typing indicator
        const typingEl = document.getElementById('typing-indicator');
        if (typingEl) typingEl.remove();
        
        // Add AI response
        const aiMessage = document.createElement('div');
        aiMessage.className = 'message ai-message mb-2';
        aiMessage.innerHTML = `
            <small class="text-muted">AI Assistant</small>
            <div class="bg-light p-2 rounded">
                ${data.response || 'Sorry, I encountered an error. Please try again.'}
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
        console.error('AI Assistant Error:', error);
        const typingEl = document.getElementById('typing-indicator');
        if (typingEl) typingEl.remove();
        
        // Add error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'message ai-message mb-2';
        errorMessage.innerHTML = `
            <small class="text-muted">AI Assistant</small>
            <div class="bg-danger text-white p-2 rounded">
                Sorry, I'm currently unavailable. Please contact support@edulink.ac.ke for assistance.
            </div>
        `;
        messages.appendChild(errorMessage);
        messages.scrollTop = messages.scrollHeight;
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Payment insights:', data);
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
        })
        .catch(error => {
            console.error('Payment insights error:', error);
        });
});
</script>

<style>
/* AI Assistant Mobile Responsive Styles */
.ai-chat-widget {
    width: 350px;
    max-width: 350px;
    margin: 1rem;
    border-radius: 12px;
    overflow: hidden;
}

.ai-chat-body {
    height: 300px;
    overflow-y: auto;
    padding: 1rem;
}

.ai-toggle-btn {
    width: 60px;
    height: 60px;
    margin: 1rem;
    font-size: 1.25rem;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.ai-close-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.ai-send-btn {
    min-width: 44px;
    padding: 0.5rem 0.75rem;
}

.ai-chat-input {
    border-radius: 8px 0 0 8px;
    padding: 0.75rem;
}

/* Mobile Responsive Breakpoints */
@media (max-width: 768px) {
    .ai-chat-widget {
        width: calc(100vw - 2rem);
        max-width: calc(100vw - 2rem);
        margin: 0.5rem;
        position: fixed;
        bottom: 80px;
        right: 0;
        left: 0;
        margin-left: auto;
        margin-right: auto;
    }
    
    .ai-chat-body {
        height: 250px;
        padding: 0.75rem;
    }
    
    .ai-toggle-btn {
        width: 56px;
        height: 56px;
        margin: 0.75rem;
        font-size: 1.1rem;
    }
    
    .ai-chat-input {
        font-size: 16px; /* Prevents zoom on iOS */
        padding: 0.625rem;
    }
    
    .ai-send-btn {
        padding: 0.625rem 0.75rem;
    }
}

@media (max-width: 480px) {
    .ai-chat-widget {
        width: calc(100vw - 1rem);
        max-width: calc(100vw - 1rem);
        margin: 0.5rem;
        bottom: 70px;
    }
    
    .ai-chat-body {
        height: 200px;
        padding: 0.5rem;
    }
    
    .ai-toggle-btn {
        width: 50px;
        height: 50px;
        margin: 0.5rem;
        font-size: 1rem;
    }
    
    .card-header h6 {
        font-size: 0.9rem;
    }
    
    .message {
        margin-bottom: 0.75rem !important;
    }
    
    .message div {
        padding: 0.5rem !important;
        font-size: 0.875rem;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .ai-toggle-btn {
        min-width: 48px;
        min-height: 48px;
    }
    
    .ai-close-btn {
        min-width: 40px;
        min-height: 40px;
    }
    
    .ai-send-btn {
        min-width: 48px;
        min-height: 48px;
    }
}

/* Landscape mobile adjustments */
@media (max-width: 768px) and (orientation: landscape) {
    .ai-chat-body {
        height: 180px;
    }
    
    .ai-chat-widget {
        bottom: 60px;
    }
}

/* Animation and transitions */
.ai-chat-widget {
    transition: all 0.3s ease;
    transform-origin: bottom right;
}

.ai-toggle-btn {
    transition: all 0.2s ease;
}

.ai-toggle-btn:hover {
    transform: scale(1.05);
}

.ai-toggle-btn:active {
    transform: scale(0.95);
}

/* Typing animation */
.typing-dots::after {
    content: '...';
    animation: typing 1.5s infinite;
}

@keyframes typing {
    0%, 60% { content: '...'; }
    20% { content: '.'; }
    40% { content: '..'; }
}

/* Dark theme support */
[data-theme="dark"] #chat-widget .card {
    background-color: var(--card-bg) !important;
    border-color: var(--border-color) !important;
}

[data-theme="dark"] #chat-widget .bg-light {
    background-color: var(--bg-secondary) !important;
    color: var(--text-primary) !important;
}

[data-theme="dark"] #chat-widget .text-muted {
    color: var(--text-secondary) !important;
}

[data-theme="dark"] .ai-chat-input {
    background-color: var(--card-bg) !important;
    border-color: var(--border-color) !important;
    color: var(--text-primary) !important;
}

[data-theme="dark"] .ai-chat-input:focus {
    background-color: var(--card-bg) !important;
    border-color: var(--primary-color) !important;
    color: var(--text-primary) !important;
}

/* Scrollbar styling */
.ai-chat-body::-webkit-scrollbar {
    width: 4px;
}

.ai-chat-body::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

.ai-chat-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 2px;
}

.ai-chat-body::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}
</style>