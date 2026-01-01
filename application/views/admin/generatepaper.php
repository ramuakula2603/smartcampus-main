<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap');

:root {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --accent-color: #ec4899;
    --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
    --gradient-secondary: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%);
    --gradient-light: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.08) 50%, rgba(236, 72, 153, 0.08) 100%);
    --bg-light: #f1f5f9;
    --bg-white: #ffffff;
    --bg-card: #fafbfc;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --success-color: #22c55e;
    --danger-color: #f43f5e;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 25px rgba(0,0,0,0.1), 0 8px 10px rgba(0,0,0,0.04);
    --shadow-2xl: 0 25px 50px rgba(0,0,0,0.15);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-smooth: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    overflow-x: hidden;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* PDF Message Styles */
.pdf-message {
    margin: 15px 0 !important;
}

.pdf-message .message-content iframe {
    width: 100%;
    min-height: 600px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

/* Responsive PDF iframe */
@media (max-width: 991px) {
    .pdf-message .message-content iframe {
        min-height: 500px;
    }
}

@media (max-width: 767px) {
    .pdf-message .message-content iframe {
        min-height: 400px;
    }
}

@media (max-width: 575px) {
    .pdf-message .message-content iframe {
        min-height: 300px;
    }
}

.pdf-message .message-content {
    max-width: 100%;
    overflow: visible;
}

/* Override any parent styles that might hide elements */
#chatSidebar,
.chat-sidebar {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.chat-header {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.sidebar-header {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Full Page Container */
.content-wrapper {
    margin-left: 0 !important;
    padding: 0 !important;
    min-height: 100vh !important;
    background: var(--gradient-primary) !important;
    position: relative !important;
    overflow: hidden !important;
    width: 100% !important;
    height: 100vh !important;
}

/* Animated Background Pattern */
.content-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
    animation: backgroundMove 20s ease-in-out infinite;
    pointer-events: none;
}

@keyframes backgroundMove {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.chat-container {
    display: flex !important;
    height: calc(100vh - 60px) !important;
    width: calc(100% - 250px) !important;
    background: var(--bg-white) !important;
    overflow: hidden !important;
    position: fixed !important;
    top: 60px !important;
    left: 250px !important;
    right: 0 !important;
    bottom: 0 !important;
    z-index: 10 !important;
    border-radius: 16px 0 0 0;
    box-shadow: var(--shadow-xl);
}

/* Responsive Chat Container */
@media (max-width: 991px) {
    .chat-container {
        width: 100% !important;
        left: 0 !important;
        height: calc(100vh - 60px) !important;
    }
}

@media (max-width: 767px) {
    .chat-container {
        width: 100% !important;
        left: 0 !important;
        top: 0 !important;
        height: 100vh !important;
    }
}

/* Left Sidebar - Conversation List */
.chat-sidebar {
    width: 320px !important;
    min-width: 280px !important;
    max-width: 320px !important;
    background: var(--bg-light) !important;
    border-right: 1px solid var(--border-color) !important;
    display: flex !important;
    flex-direction: column !important;
    box-shadow: 4px 0 12px rgba(0,0,0,0.05) !important;
    z-index: 11 !important;
    position: relative !important;
    height: 100% !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.sidebar-header {
    padding: 28px 24px !important;
    background: var(--gradient-primary) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    position: relative !important;
    z-index: 1 !important;
    flex-shrink: 0 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.sidebar-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
}

.sidebar-header h3 {
    margin: 0 0 24px 0;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-family: 'Poppins', sans-serif;
}

.sidebar-header h3 i {
    font-size: 26px;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.search-box {
    position: relative;
    margin-bottom: 18px;
}

.search-box input {
    width: 100%;
    padding: 12px 18px 12px 44px;
    border: none;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    color: white;
    font-size: 14px;
    font-weight: 500;
    outline: none;
    transition: var(--transition);
    backdrop-filter: blur(10px);
    border: 2px solid transparent;
}

.search-box input:focus {
    background: rgba(255,255,255,0.3);
    box-shadow: 0 0 0 4px rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
}

.search-box input::placeholder {
    color: rgba(255,255,255,0.85);
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.9);
    font-size: 16px;
    transition: var(--transition);
}

.search-box input:focus + i {
    color: white;
    transform: translateY(-50%) scale(1.1);
}

.new-chat-btn {
    width: 100%;
    padding: 14px 24px;
    background: rgba(255,255,255,0.25);
    color: white;
    border: 2px solid rgba(255,255,255,0.4);
    border-radius: 12px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 700;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
}

.new-chat-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.new-chat-btn:hover::before {
    width: 300px;
    height: 300px;
}

.new-chat-btn:hover {
    background: rgba(255,255,255,0.4);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    border-color: rgba(255,255,255,0.6);
}

.new-chat-btn:active {
    transform: translateY(-1px);
}

.new-chat-btn i {
    font-size: 18px;
    position: relative;
    z-index: 1;
    animation: rotate 3s linear infinite;
}

@keyframes rotate {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(90deg); }
    50% { transform: rotate(180deg); }
    75% { transform: rotate(270deg); }
}

.new-chat-btn span {
    position: relative;
    z-index: 1;
}

.conversation-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 12px;
    background: #ffffff;
    min-height: 0;
}

.conversation-item {
    padding: 16px 14px;
    margin-bottom: 8px;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    background: var(--bg-light);
    border: 2px solid transparent;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.conversation-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 0;
    background: var(--gradient-primary);
    border-radius: 0 4px 4px 0;
    transition: height 0.3s ease;
}

.conversation-item:hover::before {
    height: 60%;
}

.conversation-item:hover {
    background: #ffffff;
    transform: translateX(6px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border-color: #e1e4e8;
}

.conversation-item.active {
    background: var(--gradient-light);
    border: 2px solid var(--primary-color);
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.25), 0 2px 8px rgba(139, 92, 246, 0.15);
    transform: translateX(6px);
}

.conversation-item.active::before {
    height: 80%;
    background: var(--gradient-primary);
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.conversation-title {
    font-weight: 600;
    font-size: 13.5px;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 6px;
}

.conversation-title::before {
    content: '📚';
    font-size: 14px;
}

.conversation-time {
    font-size: 10px;
    color: #95a5a6;
    font-weight: 400;
}

.conversation-preview {
    font-size: 12px;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    font-weight: 400;
}

.conversation-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--gradient-primary);
    color: white;
    border-radius: 10px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {
    0%, 100% { transform: scale(1); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4); }
    50% { transform: scale(1.1); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.6); }
}

/* Main Chat Area */
.chat-main {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    background: #ffffff !important;
    position: relative !important;
    min-height: 0 !important;
    overflow: hidden !important;
    width: calc(100% - 280px) !important;
    height: 100% !important;
}

/* Responsive Chat Main */
@media (max-width: 991px) {
    .chat-main {
        width: 100% !important;
    }
}

@media (max-width: 767px) {
    .chat-main {
        width: 100% !important;
    }
}

.chat-header {
    padding: 24px 32px !important;
    background: var(--gradient-primary) !important;
    color: white !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15) !important;
    z-index: 50 !important;
    position: relative !important;
    flex-shrink: 0 !important;
    min-height: 88px !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 100% !important;
    margin-top: 0 !important;
}

.chat-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
}

.chat-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.chat-avatar {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    border: 3px solid rgba(255,255,255,0.4);
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
    transition: var(--transition);
    backdrop-filter: blur(10px);
}

.chat-avatar:hover {
    transform: rotate(5deg) scale(1.05);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

.chat-title-info h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -0.5px;
    font-family: 'Poppins', sans-serif;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chat-title-info p {
    margin: 6px 0 0 0;
    font-size: 13px;
    opacity: 0.95;
    font-weight: 500;
    letter-spacing: 0.3px;
}

.generate-pdf-btn {
    padding: 14px 28px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: 2px solid rgba(255,255,255,0.5);
    border-radius: 12px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 700;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 12px;
    backdrop-filter: blur(10px);
    font-family: 'Poppins', sans-serif;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.generate-pdf-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.2);
    transition: left 0.5s ease;
}

.generate-pdf-btn:hover::before {
    left: 100%;
}

.generate-pdf-btn:hover {
    background: rgba(255,255,255,0.35);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    border-color: rgba(255,255,255,0.8);
}

.generate-pdf-btn:active {
    transform: translateY(-1px) scale(0.98);
}

.generate-pdf-btn i {
    font-size: 18px;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

.chat-messages {
    flex: 1 1 auto !important;
    padding: 30px;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    background: linear-gradient(to bottom, #fafbfc 0%, #f1f5f9 100%);
    background-image:
        radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.03) 0%, transparent 60%);
    position: relative;
    min-height: 0 !important;
    height: 0 !important;
    max-height: 100% !important;
}

.message {
    margin-bottom: 16px;
    display: flex;
    align-items: flex-end;
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.user {
    justify-content: flex-end;
}

.message.agent {
    justify-content: flex-start;
}

.message-content {
    max-width: 65%;
    padding: 14px 18px;
    border-radius: var(--radius-md);
    position: relative;
    word-wrap: break-word;
    box-shadow: var(--shadow-md);
    line-height: 1.6;
    font-size: 14.5px;
    font-weight: 400;
    transition: var(--transition);
}

.message-content:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.message.agent .message-content {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: var(--text-dark);
    border-radius: 4px var(--radius-md) var(--radius-md) var(--radius-md);
    border-left: 4px solid var(--primary-color);
}

.message.agent .message-content::before {
    content: '';
    position: absolute;
    left: -4px;
    top: 0;
    width: 4px;
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 4px 0 0 4px;
}

.message.user .message-content {
    background: var(--gradient-primary);
    color: white;
    border-radius: var(--radius-md) 4px var(--radius-md) var(--radius-md);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    border-right: 4px solid rgba(255,255,255,0.3);
}

.message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    margin: 0 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.12);
}

.message.agent .message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: 2px solid #ffffff;
}

.message.user .message-avatar {
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
    color: white;
    border: 2px solid #ffffff;
}

.message-time {
    font-size: 10px;
    margin-top: 4px;
    opacity: 0.7;
    font-weight: 400;
}

.message.user .message-time {
    text-align: right;
    color: rgba(255,255,255,0.9);
}

.message.agent .message-time {
    color: #95a5a6;
}

.typing-indicator {
    display: none;
    padding: 20px 30px;
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
    background: rgba(102, 126, 234, 0.05);
}

.typing-indicator.active {
    display: flex;
    align-items: center;
    gap: 12px;
}

.typing-dots {
    display: flex;
    gap: 5px;
}

.typing-dots span {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--gradient-primary);
    animation: typing 1.4s infinite;
    box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.5;
    }
    30% {
        transform: translateY(-12px);
        opacity: 1;
    }
}

.chat-input-area {
    padding: 20px 28px !important;
    background: var(--bg-white) !important;
    border-top: 2px solid var(--border-color) !important;
    box-shadow: 0 -4px 12px rgba(0,0,0,0.06) !important;
    flex-shrink: 0 !important;
    position: relative !important;
    z-index: 50 !important;
    overflow: visible !important;
}

.chat-input-wrapper {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    background: var(--bg-light);
    border-radius: var(--radius-lg);
    padding: 10px 16px;
    box-shadow: var(--shadow-sm);
    border: 2px solid var(--border-color);
    transition: var(--transition);
}

.chat-input-wrapper:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
    background: var(--bg-white);
    transform: translateY(-2px);
}

.attach-btn {
    width: 38px;
    height: 38px;
    background: transparent;
    color: #6c757d;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    font-size: 18px;
    flex-shrink: 0;
}

.attach-btn:hover {
    background: var(--gradient-light);
    color: var(--primary-color);
    transform: rotate(45deg) scale(1.05);
}

.chat-input {
    flex: 1;
    padding: 10px 6px;
    border: none;
    font-size: 13.5px;
    font-weight: 400;
    outline: none;
    resize: none;
    max-height: 120px;
    font-family: 'Inter', sans-serif;
    line-height: 1.5;
    background: transparent;
    color: #2d3748;
}

.chat-input::placeholder {
    color: #adb5bd;
    font-weight: 400;
}

.send-btn {
    width: 48px;
    height: 48px;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.send-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.send-btn:hover::before {
    width: 200px;
    height: 200px;
}

.send-btn:hover {
    transform: scale(1.15) rotate(15deg);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.send-btn:active {
    transform: scale(1.05) rotate(0deg);
}

.send-btn i {
    position: relative;
    z-index: 1;
}

.send-btn:disabled {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
    cursor: not-allowed;
    transform: scale(1);
    box-shadow: none;
    opacity: 0.6;
}

.send-btn:disabled:hover {
    transform: scale(1);
}

.mobile-toggle {
    display: none;
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 65px;
    height: 65px;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.5);
    z-index: 1000;
    font-size: 26px;
    transition: var(--transition-smooth);
}

.mobile-toggle:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 32px rgba(99, 102, 241, 0.6), 0 4px 12px rgba(139, 92, 246, 0.4);
}

.mobile-toggle:active {
    transform: scale(0.95);
}

/* Scrollbar Styling */
.conversation-list::-webkit-scrollbar,
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.conversation-list::-webkit-scrollbar-track,
.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.conversation-list::-webkit-scrollbar-thumb,
.chat-messages::-webkit-scrollbar-thumb {
    background: #bdc3c7;
    border-radius: 3px;
}

.conversation-list::-webkit-scrollbar-thumb:hover,
.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #95a5a6;
}

/* Responsive Design - Robust Media Queries */

/* Large Desktop (1920px and above) */
@media (min-width: 1920px) {
    .chat-sidebar {
        width: 420px;
    }

    .sidebar-header {
        padding: 30px 25px;
    }

    .sidebar-header h3 {
        font-size: 24px;
    }

    .conversation-item {
        padding: 20px 18px;
    }

    .chat-header {
        padding: 25px 40px;
    }

    .chat-messages {
        padding: 40px;
    }

    .message-content {
        font-size: 15.5px;
        max-width: 55%;
    }

    .chat-input-area {
        padding: 30px 40px;
    }
}

/* Desktop (1200px - 1919px) */
@media (min-width: 1200px) and (max-width: 1919px) {
    .chat-sidebar {
        width: 280px;
    }

    .message-content {
        max-width: 60%;
    }
}

/* Laptop/Tablet Landscape (992px - 1199px) */
@media (min-width: 992px) and (max-width: 1199px) {
    .chat-sidebar {
        width: 340px;
        min-width: 300px;
    }

    .sidebar-header {
        padding: 20px 18px;
    }

    .sidebar-header h3 {
        font-size: 20px;
    }

    .conversation-item {
        padding: 16px 14px;
    }

    .conversation-title {
        font-size: 14px;
    }

    .chat-header {
        padding: 18px 25px;
    }

    .chat-avatar {
        width: 50px;
        height: 50px;
        font-size: 22px;
    }

    .chat-title-info h2 {
        font-size: 18px;
    }

    .message-content {
        max-width: 65%;
    }

    .chat-messages {
        padding: 25px;
    }
}

/* Tablet Portrait (768px - 991px) */
@media (min-width: 768px) and (max-width: 991px) {
    .chat-container {
        width: 100% !important;
        left: 0 !important;
    }
    
    .chat-sidebar {
        width: 300px;
        min-width: 280px;
        position: fixed;
        left: -100%;
        z-index: 1001;
        transition: left 0.3s ease;
    }
    
    .chat-sidebar.active {
        left: 0;
    }
    
    .chat-main {
        width: 100% !important;
    }

    .sidebar-header {
        padding: 18px 15px;
    }

    .sidebar-header h3 {
        font-size: 19px;
    }

    .new-chat-btn {
        padding: 10px 18px;
        font-size: 14px;
    }

    .conversation-item {
        padding: 15px 12px;
    }

    .conversation-title {
        font-size: 14px;
    }

    .conversation-preview {
        font-size: 12px;
    }

    .chat-header {
        padding: 16px 20px;
    }

    .chat-avatar {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }

    .chat-title-info h2 {
        font-size: 17px;
    }

    .chat-title-info p {
        font-size: 12px;
    }

    .generate-pdf-btn {
        padding: 10px 18px;
        font-size: 13px;
    }

    .message-content {
        max-width: 70%;
        font-size: 14px;
    }

    .message-avatar {
        width: 38px;
        height: 38px;
    }

    .chat-messages {
        padding: 20px;
    }

    .chat-input-area {
        padding: 20px;
    }

    .chat-input-wrapper {
        padding: 8px 15px;
    }
}

/* Mobile Landscape & Large Phones (576px - 767px) */
@media (min-width: 576px) and (max-width: 767px) {
    .chat-container {
        width: 100% !important;
        left: 0 !important;
        top: 0 !important;
        height: 100vh !important;
    }

    .chat-sidebar {
        position: fixed;
        left: -100%;
        width: 75%;
        max-width: 340px;
        height: 100vh;
        z-index: 1001;
        transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 20px rgba(0,0,0,0.2);
    }

    .chat-sidebar.active {
        left: 0;
    }
    
    .chat-main {
        width: 100% !important;
    }

    .mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-header {
        padding: 15px 18px;
    }

    .chat-header-left {
        gap: 12px;
    }

    .chat-avatar {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .chat-title-info h2 {
        font-size: 16px;
    }

    .chat-title-info p {
        font-size: 11px;
    }

    .generate-pdf-btn {
        padding: 8px 12px;
        font-size: 12px;
    }

    .generate-pdf-btn span {
        display: none;
    }

    .message-content {
        max-width: 80%;
        padding: 12px 16px;
        font-size: 14px;
    }

    .message-avatar {
        width: 36px;
        height: 36px;
        margin: 0 10px;
    }

    .chat-messages {
        padding: 18px;
    }

    .chat-input-area {
        padding: 18px;
    }

    .attach-btn {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }

    .send-btn {
        width: 46px;
        height: 46px;
    }
}

/* Mobile Portrait (up to 575px) */
@media (max-width: 575px) {
    .content-wrapper {
        padding: 0 !important;
    }

    .chat-container {
        width: 100% !important;
        left: 0 !important;
        top: 0 !important;
        height: 100vh !important;
        border-radius: 0;
    }

    .chat-sidebar {
        position: fixed;
        left: -100%;
        width: 85%;
        max-width: 300px;
        height: 100vh;
        z-index: 1001;
        transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 20px rgba(0,0,0,0.25);
    }

    .chat-sidebar.active {
        left: 0;
    }
    
    .chat-main {
        width: 100% !important;
    }

    .sidebar-header {
        padding: 16px 14px;
    }

    .sidebar-header h3 {
        font-size: 18px;
    }

    .search-box input {
        padding: 10px 15px 10px 40px;
        font-size: 13px;
    }

    .new-chat-btn {
        padding: 10px 16px;
        font-size: 13px;
    }

    .conversation-list {
        padding: 12px 8px;
    }

    .conversation-item {
        padding: 14px 12px;
        margin-bottom: 6px;
    }

    .conversation-title {
        font-size: 13px;
    }

    .conversation-preview {
        font-size: 11px;
    }

    .conversation-time {
        font-size: 10px;
    }

    .mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        bottom: 20px;
        right: 20px;
        font-size: 24px;
    }

    .chat-header {
        padding: 14px 16px;
    }

    .chat-header-left {
        gap: 10px;
    }

    .chat-avatar {
        width: 38px;
        height: 38px;
        font-size: 16px;
        border-width: 2px;
    }

    .chat-title-info h2 {
        font-size: 15px;
    }

    .chat-title-info p {
        font-size: 11px;
        margin-top: 3px;
    }

    .generate-pdf-btn {
        padding: 8px 10px;
        font-size: 16px;
        min-width: 40px;
    }

    .generate-pdf-btn span {
        display: none;
    }

    .chat-messages {
        padding: 15px 12px;
    }

    .message {
        margin-bottom: 18px;
    }

    .message-content {
        max-width: 85%;
        padding: 11px 14px;
        font-size: 13.5px;
        border-radius: 14px;
    }

    .message.agent .message-content {
        border-radius: 3px 14px 14px 14px;
    }

    .message.user .message-content {
        border-radius: 14px 3px 14px 14px;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        font-size: 13px;
        margin: 0 8px;
    }

    .message-time {
        font-size: 10px;
    }

    .typing-indicator {
        padding: 15px 12px;
        font-size: 13px;
    }

    .typing-dots span {
        width: 8px;
        height: 8px;
    }

    .chat-input-area {
        padding: 15px 12px;
    }

    .chat-input-wrapper {
        padding: 6px 12px;
        gap: 8px;
    }

    .chat-input {
        padding: 10px 6px;
        font-size: 14px;
        max-height: 100px;
    }

    .attach-btn {
        width: 38px;
        height: 38px;
        font-size: 17px;
    }

    .send-btn {
        width: 42px;
        height: 42px;
        font-size: 16px;
    }
}

/* Extra Small Devices (up to 375px) */
@media (max-width: 375px) {
    .sidebar-header h3 {
        font-size: 17px;
    }

    .conversation-title {
        font-size: 12px;
    }

    .chat-title-info h2 {
        font-size: 14px;
    }

    .chat-avatar {
        width: 35px;
        height: 35px;
        font-size: 15px;
    }

    .message-content {
        max-width: 90%;
        font-size: 13px;
        padding: 10px 12px;
    }

    .message-avatar {
        width: 30px;
        height: 30px;
        font-size: 12px;
        margin: 0 6px;
    }

    .mobile-toggle {
        width: 55px;
        height: 55px;
        font-size: 22px;
        bottom: 15px;
        right: 15px;
    }
}

/* Mobile Sidebar Backdrop */
.sidebar-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 998;
    backdrop-filter: blur(4px);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.sidebar-backdrop.active {
    display: block;
    opacity: 1;
}

/* Welcome Message Enhanced Styles */
.message-content strong {
    color: var(--primary-color);
    font-weight: 700;
}

.message.agent .message-content strong {
    display: inline-block;
    margin-bottom: 8px;
    font-size: 16px;
    background: var(--gradient-light);
    padding: 4px 12px;
    border-radius: 6px;
}

.message-content ul, .message-content ol {
    margin: 12px 0;
    padding-left: 20px;
}

.message-content li {
    margin: 6px 0;
    line-height: 1.6;
}

/* Success/Error Message Styles */
.message.success .message-content {
    background: linear-gradient(135deg, var(--success-color) 0%, #16a34a 100%);
    color: white;
    border-left-color: var(--success-color);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.message.error .message-content {
    background: linear-gradient(135deg, var(--danger-color) 0%, #e11d48 100%);
    color: white;
    border-left-color: var(--danger-color);
    box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3);
}

/* Loading Skeleton */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: var(--radius-sm);
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Enhanced Scrollbar */
.conversation-list::-webkit-scrollbar,
.chat-messages::-webkit-scrollbar {
    width: 8px;
}

.conversation-list::-webkit-scrollbar-track,
.chat-messages::-webkit-scrollbar-track {
    background: var(--bg-light);
    border-radius: 10px;
}

.conversation-list::-webkit-scrollbar-thumb,
.chat-messages::-webkit-scrollbar-thumb {
    background: var(--gradient-primary);
    border-radius: 10px;
    border: 2px solid var(--bg-light);
    transition: var(--transition);
}

.conversation-list::-webkit-scrollbar-thumb:hover,
.chat-messages::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
    border-color: #ffffff;
}

/* Tooltip Enhancement */
[title] {
    position: relative;
    cursor: help;
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: linear-gradient(135deg, var(--success-color) 0%, #16a34a 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.badge-warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #ea580c 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.badge-danger {
    background: linear-gradient(135deg, var(--danger-color) 0%, #e11d48 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(244, 63, 94, 0.3);
}

.badge-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.badge-info {
    background: var(--gradient-secondary);
    color: white;
    box-shadow: 0 2px 8px rgba(20, 184, 166, 0.3);
}

/* Dark Mode (Optional - can be toggled with JavaScript) */
.dark-mode .content-wrapper {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;
}

.dark-mode .chat-container {
    background: #0f1419;
}

.dark-mode .chat-sidebar {
    background: #1a1f2e;
    border-right-color: #2d3748;
}

.dark-mode .sidebar-header {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
}

.dark-mode .search-box input {
    background: rgba(255,255,255,0.15);
}

.dark-mode .conversation-list {
    background: #1a1f2e;
}

.dark-mode .conversation-item {
    background: #252d3d;
    border-color: #2d3748;
}

.dark-mode .conversation-item:hover {
    background: #2d3748;
}

.dark-mode .conversation-item.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
    border-color: #667eea;
}

.dark-mode .conversation-title {
    color: #e2e8f0;
}

.dark-mode .conversation-preview {
    color: #a0aec0;
}

.dark-mode .conversation-time {
    color: #718096;
}

.dark-mode .chat-main {
    background: #0f1419;
}

.dark-mode .chat-header {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
}

.dark-mode .chat-messages {
    background: linear-gradient(to bottom, #1a1f2e 0%, #252d3d 100%);
}

.dark-mode .message.agent .message-content {
    background: #2d3748;
    color: #e2e8f0;
    border-left-color: #667eea;
}

.dark-mode .message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.dark-mode .typing-indicator {
    background: rgba(102, 126, 234, 0.1);
    color: #a0aec0;
}

.dark-mode .chat-input-area {
    background: #1a1f2e;
    border-top-color: #2d3748;
}

.dark-mode .chat-input-wrapper {
    background: #252d3d;
    border-color: #2d3748;
}

.dark-mode .chat-input-wrapper:focus-within {
    border-color: #667eea;
    background: #2d3748;
}

.dark-mode .chat-input {
    color: #e2e8f0;
}

.dark-mode .chat-input::placeholder {
    color: #718096;
}

.dark-mode .attach-btn {
    color: #a0aec0;
}

.dark-mode .attach-btn:hover {
    background: rgba(102, 126, 234, 0.2);
    color: #667eea;
}

/* Smooth Transitions */
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

.message,
.conversation-item,
.chat-input-wrapper,
button {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

</style>

<div class="content-wrapper" style="min-height: 100vh; padding: 0; margin: 0;">
    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <div class="chat-container">
        <!-- Left Sidebar - Conversation List (25%) -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="sidebar-header">
                <h3><i class="fa fa-comments"></i> Conversations</h3>

                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" placeholder="Search conversations..." id="searchConversations">
                </div>

                <button class="new-chat-btn" onclick="startNewChat()">
                    <i class="fa fa-plus"></i> New Conversation
                </button>
            </div>

            <div class="conversation-list" id="conversationList">
                <!-- Conversation Items will be dynamically added here -->
            </div>
        </div>

        <!-- Main Chat Area (75%) -->
        <div class="chat-main">
            <!-- Chat Header with Subject and Generate PDF Button -->
            <div class="chat-header">
                <div class="chat-header-left">
                    <div class="chat-avatar">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="chat-title-info">
                        <h2>Mathematics - Class 10</h2>
                        <p>Algebra, Geometry, Trigonometry</p>
                    </div>
                </div>
                <button class="generate-pdf-btn" onclick="generatePDF()">
                    <i class="fa fa-file-pdf-o"></i>
                    <span>PDF</span>
                </button>
            </div>

            <!-- Chat Messages Area -->
            <div class="chat-messages" id="chatMessages">
                <!-- Welcome Message -->
                <div class="message agent" data-message-type="agent">
                    <div class="message-avatar">AI</div>
                    <div style="flex: 1;">
                        <div class="message-content">
                            <strong>Welcome to Generate Paper AI Assistant!</strong><br><br>
                            I can help you with:<br>
                            • Generate question papers for any subject<br>
                            • Create individual questions with different difficulty levels<br>
                            • Manage and organize your question banks<br>
                            • Export papers in PDF format with custom formatting<br><br>
                            How can I assist you today?
                        </div>
                        <div class="message-time">Just now</div>
                    </div>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div class="typing-indicator" id="typingIndicator">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                AI is typing...
            </div>

            <!-- Chat Input Area -->
            <div class="chat-input-area">
                <div class="chat-input-wrapper">
                    <button class="attach-btn" onclick="attachFile()">
                        <i class="fa fa-paperclip"></i>
                    </button>
                    <textarea
                        class="chat-input"
                        id="chatInput"
                        placeholder="Type your message... (e.g., 'Generate 25 marks paper on Algebra')"
                        rows="1"
                        onkeypress="handleKeyPress(event)"
                    ></textarea>
                    <button class="send-btn" id="sendBtn" onclick="sendMessage()">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Toggle Button -->
<button class="mobile-toggle" id="mobileToggle" onclick="toggleSidebar()">
    <i class="fa fa-comments"></i>
</button>

<!-- Hidden File Input for Attachments -->
<input type="file" id="fileInput" style="display: none;" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png" onchange="handleFileUpload(event)">

<script>
// Base URL for AJAX calls
const BASE_URL = '<?php echo base_url(); ?>';

// School settings for PDF template
const SCHOOL_NAME = '<?php echo isset($sch_setting) && !empty($sch_setting->name) ? addslashes($sch_setting->name) : "AMARAVATHI JUNIOR COLLEGE"; ?>';
const SCHOOL_ADDRESS = '<?php echo isset($sch_setting) && !empty($sch_setting->address) ? addslashes($sch_setting->address) : ""; ?>';
const LOGO_URL = '<?php echo isset($logo_url) && !empty($logo_url) ? addslashes($logo_url) : ""; ?>';

// Load existing conversations from server on page load
document.addEventListener('DOMContentLoaded', function() {
    try {
        loadConversationsFromServer();
    } catch (e) {
        console.error('Error initializing conversations on load', e);
    }
});

// Handle Enter key press
// Enter -> send message (Shift+Enter keeps newline behavior)
// Ctrl+Enter -> send message (kept for compatibility)
function handleKeyPress(event) {
    if (event.key === 'Enter') {
        // Plain Enter or Ctrl+Enter should send the message
        if (!event.shiftKey || event.ctrlKey) {
            event.preventDefault();
            sendMessage();
        }
    }
}

// Send message function - Save to database with static reply
async function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();

    if (!message) return;

    // Initialize conversation if not exists
    if (!currentConversationId) {
        conversationCounter++;
        currentConversationId = 'conv_' + Date.now();
        
        // Add to sidebar
        const conversationList = document.getElementById('conversationList');
        const newConvItem = document.createElement('div');
        newConvItem.className = 'conversation-item active';
        newConvItem.setAttribute('data-conversation-id', currentConversationId);
        newConvItem.onclick = function() { loadConversation(currentConversationId); };

        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

        newConvItem.innerHTML = `
            <div class="conversation-header">
                <span class="conversation-title">New Conversation ${conversationCounter}</span>
                <span class="conversation-time">${timeStr}</span>
            </div>
            <div class="conversation-preview">${message.substring(0, 50)}${message.length > 50 ? '...' : ''}</div>
        `;

        if (conversationList.firstChild) {
            conversationList.insertBefore(newConvItem, conversationList.firstChild);
        } else {
            conversationList.appendChild(newConvItem);
        }

        // Update header
        document.querySelector('.chat-title-info h2').textContent = 'New Conversation ' + conversationCounter;
        document.querySelector('.chat-title-info p').textContent = 'Start by describing what you need';
    }

    // Add user message to chat
    addMessage('user', message);
    input.value = '';
    input.style.height = 'auto';

    // Show typing indicator
    const typingIndicator = document.getElementById('typingIndicator');
    typingIndicator.classList.add('active');

    try {
        // Call controller to save message
        const response = await fetch(BASE_URL + 'admin/generatepaper/save_message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'user_message=' + encodeURIComponent(message) + '&conversation_id=' + encodeURIComponent(currentConversationId || 'default')
        });

        // Hide typing indicator
        typingIndicator.classList.remove('active');

        if (response.ok) {
            const data = await response.json();

            if (data.status === 'success') {
                const aiText = data.ai_reply || '';

                // Heuristic: detect if this looks like a question paper
                const isPaper =
                    aiText.includes('Question Paper') ||
                    aiText.includes('Section A:') ||
                    aiText.includes('Section B:') ||
                    aiText.includes('Total Marks');

                if (isPaper) {
                    // Embed HTML question paper template inside chat using an iframe
                    const messagesContainer = document.getElementById('chatMessages');
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message agent pdf-message';

                    const nowInner = new Date();
                    const timeStrInner = nowInner.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    const iframeName = 'paperFrame_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);

                    messageDiv.innerHTML = `
                        <div class="message-avatar">AI</div>
                        <div style="flex: 1;">
                            <div class="message-content" style="padding: 0;">
                                <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
                                    <h4 style="margin: 0 0 15px 0; color: #667eea;">
                                        <i class="fa fa-file-text-o"></i> Question Paper Preview
                                    </h4>
                                    <div style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden; background: white;">
                                        <iframe name="${iframeName}" style="width: 100%; height: 600px; border: none; display: block;"></iframe>
                                    </div>
                                </div>
                            </div>
                            <div class="message-time">${timeStrInner}</div>
                        </div>
                    `;

                    messagesContainer.appendChild(messageDiv);

                    // Scroll to bottom
                    setTimeout(() => {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }, 100);

                    // Post the paper text to the preview endpoint, targeting the iframe
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = BASE_URL + 'admin/generatepaper/preview';
                    form.target = iframeName;

                    const inputField = document.createElement('input');
                    inputField.type = 'hidden';
                    inputField.name = 'paper_text';
                    inputField.value = aiText;

                    form.appendChild(inputField);
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } else {
                    // Regular chat response
                    addMessage('agent', aiText);
                }

                console.log('Message saved with ID:', data.id);

                // Update conversation preview in sidebar
                if (currentConversationId) {
                    const convItem = document.querySelector(`[data-conversation-id="${currentConversationId}"]`);
                    if (convItem) {
                        const preview = convItem.querySelector('.conversation-preview');
                        if (preview) {
                            preview.textContent = message.substring(0, 50) + (message.length > 50 ? '...' : '');
                        }
                    }
                }
            } else {
                // Handle error response
                addMessage('agent', '❌ Error: ' + data.message);
                console.error('Save error:', data.message);
            }
        } else {
            // Handle HTTP error
            typingIndicator.classList.remove('active');
            addMessage('agent', '❌ Sorry, I encountered an error while processing your request. Please try again.');
            console.error('HTTP error:', response.status, response.statusText);
        }
    } catch (error) {
        // Hide typing indicator
        typingIndicator.classList.remove('active');

        // Show error message
        addMessage('agent', '❌ Unable to save message. Please check your connection and try again.');
        console.error('Error saving message:', error);
    }
}

// Make send/keypress available to inline handlers
window.sendMessage = sendMessage;
window.handleKeyPress = handleKeyPress;

// Get current conversation ID
function getCurrentConversationId() {
    const activeConv = document.querySelector('.conversation-item.active');
    if (activeConv) {
        return activeConv.getAttribute('data-conversation-id') || 'default';
    }
    return 'default';
}

// Add message to chat
function addMessage(type, content) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;

    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const messageId = 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

    if (type === 'agent') {
        messageDiv.innerHTML = `
            <div class="message-avatar">AI</div>
            <div style="flex: 1;">
                <div class="message-content">${content.replace(/\n/g, '<br>')}</div>
                <div class="message-time">${timeStr}</div>
            </div>
        `;
        messageDiv.setAttribute('data-message-id', messageId);
        messageDiv.setAttribute('data-message-type', 'agent');
    } else {
        messageDiv.innerHTML = `
            <div style="flex: 1;">
                <div class="message-content">${content.replace(/\n/g, '<br>')}</div>
                <div class="message-time">${timeStr}</div>
            </div>
            <div class="message-avatar">You</div>
        `;
    }

    messagesContainer.appendChild(messageDiv);
    // Scroll to bottom with smooth behavior
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
}

// Conversation counter
let conversationCounter = 0;
let currentConversationId = null;

// Load conversations list from backend and render sidebar
async function loadConversationsFromServer() {
    const conversationList = document.getElementById('conversationList');
    if (!conversationList) return;

    try {
        const resp = await fetch(BASE_URL + 'admin/generatepaper/get_conversations');
        if (!resp.ok) {
            console.error('Failed to load conversations list');
            return;
        }

        const data = await resp.json();
        if (!data || data.status !== 'success' || !Array.isArray(data.conversations)) {
            console.error('Unexpected conversations response', data);
            return;
        }

        conversationList.innerHTML = '';

        if (data.conversations.length === 0) {
            // No conversations yet; keep default welcome message
            currentConversationId = null;
            return;
        }

        // Render each conversation item
        data.conversations.forEach((conv, index) => {
            const item = document.createElement('div');
            item.className = 'conversation-item';
            item.setAttribute('data-conversation-id', conv.conversation_id);

            const titleText = conv.title || ('Conversation ' + (index + 1));
            const previewText = conv.preview || '';
            const timeText = conv.last_time || '';

            item.innerHTML = `
                <div class="conversation-header">
                    <span class="conversation-title">${titleText}</span>
                    <span class="conversation-time">${timeText}</span>
                </div>
                <div class="conversation-preview">${previewText}</div>
            `;

            item.onclick = function() {
                loadConversation(conv.conversation_id);
            };

            conversationList.appendChild(item);
        });

        // Update counter so new conversations continue numbering
        conversationCounter = data.conversations.length;

        // Auto-load the most recent conversation (first in list)
        const firstConv = data.conversations[0];
        if (firstConv && firstConv.conversation_id) {
            await loadConversation(firstConv.conversation_id);
        }
    } catch (e) {
        console.error('Error loading conversations from server', e);
    }
}

// Start new chat
function startNewChat() {
    conversationCounter++;
    const newConvId = 'conv_' + Date.now();
    currentConversationId = newConvId;

    // Clear chat messages
    const messagesContainer = document.getElementById('chatMessages');
    messagesContainer.innerHTML = `
        <div class="message agent">
            <div class="message-avatar">AI</div>
            <div>
                <div class="message-content">
                    <strong>New Conversation Started!</strong><br><br>
                    How can I help you generate papers today?
                </div>
                <div class="message-time">Just now</div>
            </div>
        </div>
    `;

    // Update header
    document.querySelector('.chat-title-info h2').textContent = 'New Conversation ' + conversationCounter;
    document.querySelector('.chat-title-info p').textContent = 'Start by describing what you need';

    // Remove active class from all conversations
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });

    // Add new conversation to sidebar
    const conversationList = document.getElementById('conversationList');
    const newConvItem = document.createElement('div');
    newConvItem.className = 'conversation-item active';
    newConvItem.setAttribute('data-conversation-id', newConvId);
    newConvItem.onclick = function() { loadConversation(newConvId); };

    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

    newConvItem.innerHTML = `
        <div class="conversation-header">
            <span class="conversation-title">New Conversation ${conversationCounter}</span>
            <span class="conversation-time">${timeStr}</span>
        </div>
        <div class="conversation-preview">New conversation started...</div>
    `;

    // Insert at the top of the list
    conversationList.insertBefore(newConvItem, conversationList.firstChild);

    // Close sidebar on mobile
    if (window.innerWidth <= 768) {
        toggleSidebar();
    }
}

// Load conversation
async function loadConversation(conversationId) {
    currentConversationId = conversationId;

    // Remove active class from all items
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });

    // Add active class to clicked item
    const clickedItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
    if (clickedItem) {
        clickedItem.classList.add('active');

        // Update header based on conversation
        const title = clickedItem.querySelector('.conversation-title').textContent;
        document.querySelector('.chat-title-info h2').textContent = title;
        document.querySelector('.chat-title-info p').textContent = 'Continue your conversation';
    }

    const messagesContainer = document.getElementById('chatMessages');
    messagesContainer.innerHTML = `
        <div class="message agent">
            <div class="message-avatar">AI</div>
            <div>
                <div class="message-content">
                    Loading conversation...
                </div>
                <div class="message-time">Just now</div>
            </div>
        </div>
    `;

    try {
        const resp = await fetch(BASE_URL + 'admin/generatepaper/get_conversation_messages?conversation_id=' + encodeURIComponent(conversationId));
        if (!resp.ok) {
            console.error('Failed to load conversation messages');
            return;
        }
        const data = await resp.json();
        if (!data || data.status !== 'success' || !Array.isArray(data.messages)) {
            console.error('Unexpected conversation messages response', data);
            return;
        }

        messagesContainer.innerHTML = '';
        data.messages.forEach(row => {
            if (row.user_message) {
                addMessage('user', row.user_message);
            }
            if (row.ai_reply) {
                addMessage('agent', row.ai_reply);
            }
        });
    } catch (e) {
        console.error('Error loading conversation messages', e);
    }

    // Close sidebar on mobile
    if (window.innerWidth <= 768) {
        toggleSidebar();
    }
}

// Generate PDF function - trigger PDF from embedded HTML template in chat
function generatePDF() {
    // Find the latest embedded question paper iframe inside chat
    const iframes = document.querySelectorAll('.pdf-message iframe');
    if (!iframes || iframes.length === 0) {
        alert('No question paper preview found. Please generate a paper first.');
        return;
    }

    const iframe = iframes[iframes.length - 1];

    try {
        const iframeDoc = iframe.contentWindow && iframe.contentWindow.document;
        if (!iframeDoc) {
            alert('Unable to access question paper preview. Please try again.');
            return;
        }

        const pdfBtn = iframeDoc.getElementById('downloadPdfBtn');
        if (pdfBtn) {
            pdfBtn.click();
        } else {
            alert('PDF option is not available in the current preview.');
        }
    } catch (e) {
        console.error('Error triggering PDF inside iframe:', e);
        alert('Unable to generate PDF from the preview. Please try again.');
    }
}

// Create PDF template HTML with college header
function createPDFTemplate(schoolName, schoolAddress, logoUrl, content) {
    // Debug: Log content
    console.log('Creating PDF template with content length:', content.length);
    console.log('Content preview:', content.substring(0, 300));
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Escape content and convert newlines to <br>
    // First escape HTML, then convert newlines
    // Helper to build structured HTML from plain text paper content
    function buildStructuredPaperHtml(rawText) {
        const lines = (rawText || '').split(/\r?\n/).map(l => l.trim());
        const parts = [];
        let inList = false;
        let hasTitle = false;

        function closeList() {
            if (inList) {
                parts.push('</ol>');
                inList = false;
            }
        }

        lines.forEach((line) => {
            if (!line) {
                // Empty line – just close list if needed and skip
                closeList();
                return;
            }

            // First non-empty line as exam title (ignore leading blank lines)
            if (!hasTitle) {
                // If it already looks like a markdown heading (e.g. ### Title), strip leading #'s
                const headingMatch = line.match(/^#{1,6}\s+(.*)$/);
                const titleText = headingMatch ? headingMatch[1].trim() : line;
                parts.push('<h1 class="exam-title">' + escapeHtml(titleText) + '</h1>');
                hasTitle = true;
                return;
            }

            // Markdown-style headings for inner sections (e.g. ###, ####)
            const mdHeading = line.match(/^(#{2,6})\s+(.*)$/);
            if (mdHeading) {
                closeList();
                const level = mdHeading[1].length; // number of #
                const text = mdHeading[2].trim();
                const safeText = escapeHtml(text);
                // Map ### to h2, #### to h3, deeper all as h3 for simplicity
                if (level === 2 || level === 3) {
                    parts.push('<h2 class="section-title">' + safeText + '</h2>');
                } else {
                    parts.push('<h3 class="section-title">' + safeText + '</h3>');
                }
                return;
            }

            // Meta lines like "Class: ..." or "Total Marks: ..." or "Instructions: ..."
            if (/^(Class|Grade)\s*:/i.test(line)) {
                const value = line.split(/:/)[1] || '';
                parts.push('<p class="exam-meta"><strong>Class:</strong> ' + escapeHtml(value.trim()) + '</p>');
                return;
            }
            if (/^Total\s*Marks\s*:/i.test(line)) {
                const value = line.split(/:/)[1] || '';
                parts.push('<p class="exam-meta"><strong>Total Marks:</strong> ' + escapeHtml(value.trim()) + '</p>');
                return;
            }
            if (/^Instructions\s*:/i.test(line)) {
                const value = line.split(/:/)[1] || '';
                parts.push('<p class="exam-instructions"><strong>Instructions:</strong> ' + escapeHtml(value.trim()) + '</p>');
                return;
            }

            // Section headers: e.g., "Section A: Objective Questions (20 marks)"
            if (/^Section\s+/i.test(line)) {
                closeList();
                parts.push('<h2 class="section-title">' + escapeHtml(line) + '</h2>');
                return;
            }

            // Numbered questions: "1.", "1)", "1.1" etc
            const questionMatch = line.match(/^(\d+(?:\.\d+)*)[\.)]\s*(.*)$/);
            if (questionMatch) {
                if (!inList) {
                    parts.push('<ol class="question-list">');
                    inList = true;
                }
                let qText = (questionMatch[2] || '').trim();
                if (!qText) {
                    qText = line;
                }
                // Basic markdown bold (**text**) inside questions
                qText = qText.replace(/\*\*(.+?)\*\*/g, '<strong>$1<\/strong>');
                parts.push('<li>' + qText + '</li>');
                return;
            }

            // Fallback: normal paragraph text (also support basic markdown bold)
            closeList();
            let paragraph = escapeHtml(line);
            paragraph = paragraph.replace(/\*\*(.+?)\*\*/g, '<strong>$1<\/strong>');
            parts.push('<p>' + paragraph + '</p>');
        });

        closeList();

        return parts.join('\n');
    }

    // Build structured HTML content from the plain text
    let escapedContent = buildStructuredPaperHtml(content);

    const escapedSchoolName = escapeHtml(schoolName);
    const escapedSchoolAddress = schoolAddress ? escapeHtml(schoolAddress) : '';
    const escapedLogoUrl = logoUrl ? escapeHtml(logoUrl) : '';
    
    console.log('Original content length:', content.length);
    console.log('Escaped content length:', escapedContent.length);
    console.log('Escaped content preview:', escapedContent.substring(0, 300));
    
    const htmlTemplate = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    font-size: 12pt;
                    line-height: 1.8;
                    color: #000;
                }
                .header-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .header-table td {
                    padding: 10px;
                    vertical-align: middle;
                }
                .logo-cell {
                    width: 20%;
                    text-align: left;
                }
                .logo-img {
                    max-width: 100px;
                    max-height: 100px;
                }
                .school-info {
                    width: 60%;
                    text-align: center;
                }
                .school-name {
                    font-size: 20pt;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .paper-title {
                    font-size: 18pt;
                    font-weight: bold;
                    margin-top: 5px;
                }
                .school-address {
                    font-size: 10pt;
                    margin-top: 5px;
                }
                .divider {
                    border-top: 2px solid #000;
                    margin: 15px 0;
                }
                .content {
                    padding: 15px 0;
                    text-align: left;
                    font-size: 12pt;
                    line-height: 1.8;
                }
                .exam-title {
                    text-align: center;
                    font-size: 18pt;
                    font-weight: 700;
                    margin-bottom: 5px;
                    text-transform: uppercase;
                }
                .exam-meta,
                .exam-instructions {
                    font-size: 11pt;
                    margin: 4px 0;
                }
                .exam-instructions {
                    margin-top: 8px;
                    margin-bottom: 10px;
                }
                .section-title {
                    font-size: 14pt;
                    font-weight: 600;
                    margin: 14px 0 6px 0;
                    text-transform: none;
                }
                .question-list {
                    margin: 6px 0 10px 24px;
                    padding-left: 16px;
                }
                .question-list li {
                    margin: 4px 0;
                    line-height: 1.6;
                }
                .content p {
                    margin: 10px 0;
                    line-height: 1.8;
                }
                .content br {
                    display: block;
                    margin: 3px 0;
                }
                .footer {
                    margin-top: 30px;
                    padding-top: 10px;
                    border-top: 1px solid #ddd;
                    text-align: center;
                    font-size: 9pt;
                    color: #666;
                }
                /* Small-screen responsiveness for preview/iframe */
                @media (max-width: 600px) {
                    body {
                        padding: 10px;
                        font-size: 11pt;
                    }
                    .content {
                        padding: 10px 0;
                    }
                }
            </style>
        </head>
        <body>
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        ${escapedLogoUrl ? '<img src="' + escapedLogoUrl + '" class="logo-img" alt="Logo" />' : ''}
                    </td>
                    <td class="school-info">
                        <div class="school-name">${escapedSchoolName}</div>
                        <div class="paper-title">QUESTION PAPER</div>
                        ${escapedSchoolAddress ? '<div class="school-address">' + escapedSchoolAddress + '</div>' : ''}
                    </td>
                    <td class="logo-cell"></td>
                </tr>
            </table>
            <div class="divider"></div>
            <div class="content" id="question-content">${escapedContent}</div>
            <div class="footer">Generated on ${new Date().toLocaleString()}</div>
        </body>
        </html>
    `;
    
    console.log('HTML template length:', htmlTemplate.length);
    return htmlTemplate;
}

// Generate PDF client-side
function generateClientSidePDF(htmlContent, filename) {
    // Create temporary container
    const tempDiv = document.createElement('div');
    
    // Parse the HTML content properly
    // If it's a full HTML document, extract body content
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlContent, 'text/html');
    
    // Get body content or use the full HTML if body is not found
    let bodyContent = '';
    if (doc.body && doc.body.innerHTML) {
        bodyContent = doc.body.innerHTML;
        // Also copy styles from head
        const styles = doc.head.querySelectorAll('style');
        styles.forEach(style => {
            const styleTag = document.createElement('style');
            styleTag.textContent = style.textContent;
            tempDiv.appendChild(styleTag);
        });
    } else {
        // Fallback: try to extract body content manually
        const bodyMatch = htmlContent.match(/<body[^>]*>([\s\S]*)<\/body>/i);
        if (bodyMatch && bodyMatch[1]) {
            bodyContent = bodyMatch[1];
            // Extract styles
            const styleMatch = htmlContent.match(/<style[^>]*>([\s\S]*)<\/style>/gi);
            if (styleMatch) {
                styleMatch.forEach(styleText => {
                    const styleTag = document.createElement('style');
                    styleTag.textContent = styleText.replace(/<\/?style[^>]*>/gi, '');
                    tempDiv.appendChild(styleTag);
                });
            }
        } else {
            // Last fallback: use the HTML as is
            bodyContent = htmlContent;
        }
    }
    
    tempDiv.innerHTML = bodyContent;
    
    // Verify content is present
    const contentCheck = tempDiv.querySelector('.content') || tempDiv.querySelector('#question-content');
    if (!contentCheck || !contentCheck.textContent || contentCheck.textContent.trim().length === 0) {
        console.error('WARNING: Content is empty after parsing!');
        console.log('Body content length:', bodyContent.length);
        console.log('Body content preview:', bodyContent.substring(0, 500));
    }
    tempDiv.style.position = 'absolute';
    tempDiv.style.left = '-9999px';
    tempDiv.style.width = '210mm';
    tempDiv.style.padding = '20px';
    tempDiv.style.backgroundColor = '#fff';
    document.body.appendChild(tempDiv);
    
    // Debug: Check if content is in the DOM
    setTimeout(() => {
        const contentDiv = tempDiv.querySelector('.content') || tempDiv.querySelector('#question-content');
        if (contentDiv) {
            const textLength = contentDiv.textContent ? contentDiv.textContent.length : 0;
            const htmlLength = contentDiv.innerHTML ? contentDiv.innerHTML.length : 0;
            console.log('Content div found, text length:', textLength);
            console.log('Content div HTML length:', htmlLength);
            if (textLength > 0) {
                console.log('Content div preview:', contentDiv.textContent.substring(0, 200));
            } else {
                console.error('Content div is empty!');
                console.log('Content div innerHTML:', contentDiv.innerHTML.substring(0, 500));
            }
        } else {
            console.error('Content div not found in template!');
            console.log('TempDiv HTML:', tempDiv.innerHTML.substring(0, 1000));
        }
    }, 200);
    
    // Load html2pdf.js if not already loaded
    if (typeof html2pdf === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        script.onload = function() {
            createPDF(tempDiv, filename);
        };
        script.onerror = function() {
            document.body.removeChild(tempDiv);
            addMessage('agent', '❌ Failed to load PDF library. Please try again.');
        };
        document.head.appendChild(script);
    } else {
        createPDF(tempDiv, filename);
    }
    
    function createPDF(element, filename) {
        // Wait for images to load before generating PDF
        const images = element.querySelectorAll('img');
        let imagesLoaded = 0;
        const totalImages = images.length;
        
        if (totalImages === 0) {
            generatePDFNow(element, filename);
        } else {
            images.forEach(img => {
                if (img.complete) {
                    imagesLoaded++;
                    if (imagesLoaded === totalImages) {
                        generatePDFNow(element, filename);
                    }
                } else {
                    img.onload = function() {
                        imagesLoaded++;
                        if (imagesLoaded === totalImages) {
                            generatePDFNow(element, filename);
                        }
                    };
                    img.onerror = function() {
                        imagesLoaded++;
                        if (imagesLoaded === totalImages) {
                            generatePDFNow(element, filename);
                        }
                    };
                }
            });
        }
        
        function generatePDFNow(element, filename) {
            const opt = {
                margin: [15, 15, 15, 15],
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            html2pdf().set(opt).from(element).outputPdf('blob').then(function(pdfBlob) {
            // Remove temp div
            if (document.body.contains(element)) {
                document.body.removeChild(element);
            }
            
            // Create PDF URL
            const pdfUrl = URL.createObjectURL(pdfBlob);
            
            // Display PDF in chat
            displayPDFInChat(pdfUrl, filename);
            
            // Remove loading message
            const loadingMessages = document.querySelectorAll('.message.agent');
            if (loadingMessages.length > 0) {
                const lastMessage = loadingMessages[loadingMessages.length - 1];
                if (lastMessage.textContent.includes('Generating PDF')) {
                    lastMessage.remove();
                }
            }
        }).catch(err => {
            if (document.body.contains(element)) {
                document.body.removeChild(element);
            }
            addMessage('agent', '❌ Error generating PDF: ' + err.message);
            console.error('PDF Generation Error:', err);
        });
        }
    }
}

// Display PDF in chat window
function displayPDFInChat(pdfUrl, filename) {
    const messagesContainer = document.getElementById('chatMessages');
    const pdfMessageDiv = document.createElement('div');
    pdfMessageDiv.className = 'message agent pdf-message';
    
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    pdfMessageDiv.innerHTML = `
        <div class="message-avatar">AI</div>
        <div style="flex: 1;">
            <div class="message-content" style="padding: 0;">
                <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
                    <h4 style="margin: 0 0 15px 0; color: #667eea; display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fa fa-file-pdf-o"></i> Question Paper PDF Generated</span>
                        <a href="${pdfUrl}" download="${filename}"
                           style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: #667eea; color: #fff; text-decoration: none;">
                            <i class="fa fa-download"></i>
                        </a>
                    </h4>
                    <div style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden; background: white;">
                        <iframe 
                            src="${pdfUrl}" 
                            style="width: 100%; height: 600px; border: none; display: block;"
                            type="application/pdf">
                        </iframe>
                    </div>
                    <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
                        <button onclick="this.closest('.pdf-message').remove(); URL.revokeObjectURL('${pdfUrl}');" 
                                style="padding: 6px 12px; background: #dc3545; color: white; border: none; 
                                       border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 12px;">
                            <i class="fa fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
            <div class="message-time">${timeStr}</div>
        </div>
    `;
    
    messagesContainer.appendChild(pdfMessageDiv);
    
    // Scroll to bottom
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
}

// Helper function to convert base64 to blob
function base64ToBlob(base64, mimeType) {
    const byteCharacters = atob(base64);
    const byteNumbers = new Array(byteCharacters.length);
    for (let i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    const byteArray = new Uint8Array(byteNumbers);
    return new Blob([byteArray], { type: mimeType });
}

// Attach file function
function attachFile() {
    document.getElementById('fileInput').click();
}

// Handle file upload
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Show uploading message
    addMessage('user', `📎 Uploading file: ${file.name}...`);

    // Create FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('conversation_id', currentConversationId || 'default');

    // Upload file via AJAX
    fetch(BASE_URL + 'admin/generatepaper/upload_file', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            addMessage('agent', `✅ File "${file.name}" uploaded successfully! I'll analyze it and help you generate questions based on this content.`);
        } else {
            addMessage('agent', `❌ Error uploading file: ${data.message || 'Unknown error'}`);
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        addMessage('agent', `❌ Failed to upload file. Please try again.`);
    });

    // Reset file input
    event.target.value = '';
}

// Toggle sidebar for mobile
function toggleSidebar() {
    const sidebar = document.getElementById('chatSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    sidebar.classList.toggle('active');
    backdrop.classList.toggle('active');
}

// Search conversations
document.addEventListener('DOMContentLoaded', function() {
    // Prevent scrolling on window, body, and chat containers
    // Only allow scrolling in .chat-messages and .conversation-list
    function preventScroll(e) {
        const target = e.target;
        const chatMessages = document.querySelector('.chat-messages');
        const conversationList = document.querySelector('.conversation-list');
        
        // Allow scrolling in chat messages
        if (chatMessages && (chatMessages.contains(target) || target === chatMessages)) {
            return;
        }
        
        // Allow scrolling in sidebar conversation list
        if (conversationList && (conversationList.contains(target) || target === conversationList)) {
            return;
        }
        
        // Prevent all other scrolling
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
    
    // Prevent wheel, touchmove, and scroll events only on chat containers
    ['wheel', 'touchmove', 'scroll'].forEach(eventType => {
        document.querySelector('.content-wrapper')?.addEventListener(eventType, preventScroll, { passive: false });
        document.querySelector('.chat-container')?.addEventListener(eventType, preventScroll, { passive: false });
        document.querySelector('.chat-main')?.addEventListener(eventType, preventScroll, { passive: false });
    });
    
    // Prevent arrow keys and page up/down from scrolling only within chat containers
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer) {
        chatContainer.addEventListener('keydown', function(e) {
            if (['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].includes(e.key)) {
                const chatMessages = document.querySelector('.chat-messages');
                const conversationList = document.querySelector('.conversation-list');
                const chatInput = document.getElementById('chatInput');
                const activeElement = document.activeElement;
                
                // Allow if focus is in chat messages or conversation list
                if (chatMessages && (chatMessages.contains(activeElement) || activeElement === chatMessages)) {
                    return;
                }
                if (conversationList && (conversationList.contains(activeElement) || activeElement === conversationList)) {
                    return;
                }

                // Allow normal typing (including space) in chat input
                if (chatInput && (activeElement === chatInput || chatInput.contains(activeElement))) {
                    return;
                }
                
                // Prevent scrolling elsewhere within chat container
                e.preventDefault();
            }
        });
    }
    
    const searchInput = document.getElementById('searchConversations');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const conversations = document.querySelectorAll('.conversation-item');

            conversations.forEach(conv => {
                const title = conv.querySelector('.conversation-title').textContent.toLowerCase();
                const preview = conv.querySelector('.conversation-preview').textContent.toLowerCase();

                if (title.includes(searchTerm) || preview.includes(searchTerm)) {
                    conv.style.display = 'block';
                } else {
                    conv.style.display = 'none';
                }
            });
        });
    }

    // Auto-resize textarea
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            const sidebar = document.getElementById('chatSidebar');
            const toggle = document.getElementById('mobileToggle');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebar && toggle && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
                if (backdrop) {
                    backdrop.classList.remove('active');
                }
            }
        }
    });
});
</script>
