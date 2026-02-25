/**
 * Mico Sage Tech Agency — Client-side JavaScript
 * Alpine.js components + scroll animations + interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
    // ── Scroll-triggered animations ────────────────────────
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Don't unobserve timeline so it can be re-triggered if wanted, or just trigger once
                if (!entry.target.classList.contains('process-timeline')) {
                    observer.unobserve(entry.target);
                }
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.animate-on-scroll, .process-timeline').forEach(el => {
        observer.observe(el);
    });

    // ── Smooth scroll for anchor links ─────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Close mobile nav if open
                const mobileNav = document.getElementById('mobileNav');
                if (mobileNav) mobileNav.classList.remove('active');
            }
        });
    });

    // ── Navbar background on scroll ────────────────────────
    const navbar = document.querySelector('.navbar-island');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(10, 10, 30, 0.85)';
            } else {
                navbar.style.background = 'rgba(10, 10, 30, 0.65)';
            }
        });
    }

    // ── Mobile nav toggle ──────────────────────────────────
    const hamburger = document.getElementById('navHamburger');
    const mobileNav = document.getElementById('mobileNav');
    if (hamburger && mobileNav) {
        hamburger.addEventListener('click', () => {
            mobileNav.classList.toggle('active');
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileNav.contains(e.target)) {
                mobileNav.classList.remove('active');
            }
        });
    }

    // ── Floating CTA Popup Toggle ──────────────────────────
    const floatingCtaBtn = document.getElementById('floatingCtaBtn');
    const floatingCtaPopup = document.getElementById('floatingCtaPopup');
    if (floatingCtaBtn && floatingCtaPopup) {
        floatingCtaBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevents document click trigger
            floatingCtaPopup.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!floatingCtaBtn.contains(e.target) && !floatingCtaPopup.contains(e.target)) {
                floatingCtaPopup.classList.remove('active');
            }
        });
    }

    // ── Parallax on floating elements ──────────────────────
    const floatingElements = document.querySelectorAll('.floating-code');
    if (floatingElements.length > 0) {
        window.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 2;
            const y = (e.clientY / window.innerHeight - 0.5) * 2;
            floatingElements.forEach((el, i) => {
                const speed = (i + 1) * 5;
                el.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
            });
        });
    }

    // ── Counter animation for stats ────────────────────────
    const statNums = document.querySelectorAll('.stat-num');
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                statObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statNums.forEach(el => statObserver.observe(el));

    function animateCounter(el) {
        const text = el.textContent.trim();
        const match = text.match(/([+]?)(\d+)([+]?)/);
        if (!match) return;

        const prefix = match[1];
        const target = parseInt(match[2]);
        const suffix = match[3];
        const duration = 2000;
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(eased * target);
            el.textContent = prefix + current + suffix;
            if (progress < 1) requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    }

    // ── Product Tabs Filtering ─────────────────────────────
    const tabs = document.querySelectorAll('.product-tab');
    const cards = document.querySelectorAll('.product-card');

    if (tabs.length > 0 && cards.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all
                tabs.forEach(t => t.classList.remove('active'));
                // Add to current
                tab.classList.add('active');

                const targetCategory = tab.getAttribute('data-category');

                cards.forEach(card => {
                    if (targetCategory === 'all' || card.getAttribute('data-category') === targetCategory) {
                        card.style.display = 'block';
                        setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => { card.style.display = 'none'; }, 300);
                    }
                });
            });
        });
    }

    // ── Chatbot Widget Logic ───────────────────────────────
    const chatToggle = document.getElementById('chatbotToggle');
    const chatPanel = document.getElementById('chatbotPanel');
    const chatClose = document.getElementById('chatbotClose');
    const chatMessages = document.getElementById('chatbotMessages');
    const chatOptions = document.getElementById('chatbotOptions');
    const chatInputArea = document.getElementById('chatbotInputArea');
    const chatInput = document.getElementById('chatbotInput');
    const chatSendBtn = document.getElementById('chatbotSendBtn');
    const chatNewBtn = document.getElementById('chatbotNewChat');
    const chatEndBtn = document.getElementById('chatbotEndChat');

    if (chatToggle && chatPanel && window.chatbotData && window.chatbotData.start_node_id) {
        let chatInitialized = false;
        let chatEnded = false;
        const chatData = window.chatbotData;
        let chatTranscript = [];
        let currentInputHandler = null;

        // Toggle Chat
        chatToggle.addEventListener('click', () => {
            chatPanel.classList.add('active');
            chatToggle.style.transform = 'scale(0)';
            if (!chatInitialized) {
                chatInitialized = true;
                initChat();
            }
        });

        chatClose.addEventListener('click', () => {
            chatPanel.classList.remove('active');
            chatToggle.style.transform = 'scale(1)';
        });

        function initChat() {
            // Try to load from localStorage
            const saved = localStorage.getItem('mico_chat_transcript');
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (Array.isArray(data) && data.length > 0) {
                        chatTranscript = data;
                        renderTranscript();
                        return;
                    }
                } catch (e) {
                    console.warn('Failed to parse saved chat:', e);
                }
            }

            // Start fresh if no saved transcript
            loadNode(chatData.start_node_id);
        }

        function renderTranscript() {
            chatMessages.innerHTML = '';
            chatTranscript.forEach(item => {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'chat-msg ' + item.sender;
                msgDiv.innerHTML = item.message.replace(/\n/g, '<br>');
                chatMessages.appendChild(msgDiv);
            });
            scrollToBottom();

            // If the last message was a bot message, we might need to show options or input
            const lastMsg = chatTranscript[chatTranscript.length - 1];
            if (lastMsg && lastMsg.sender === 'bot') {
                // Determine which node this was (best effort)
                // For simplicity, if it ended with a bot message, we'll re-load the node if we can find it
                // Or just show a "New Chat" button
                const btn = document.createElement('button');
                btn.className = 'chat-opt-btn';
                btn.textContent = 'Continue or Start New?';
                btn.style.opacity = '0.7';
                chatOptions.appendChild(btn);
            }
        }

        // ── New Chat ──
        if (chatNewBtn) {
            chatNewBtn.addEventListener('click', () => {
                chatMessages.innerHTML = '';
                chatOptions.innerHTML = '';
                if (chatInputArea) chatInputArea.style.display = 'none';
                chatTranscript = [];
                chatEnded = false;
                if (chatEndBtn) chatEndBtn.disabled = false;
                localStorage.removeItem('mico_chat_transcript');
                if (currentInputHandler && chatSendBtn) {
                    chatSendBtn.removeEventListener('click', currentInputHandler);
                    currentInputHandler = null;
                }
                loadNode(chatData.start_node_id);
            });
        }

        // ── End Chat ──
        if (chatEndBtn) {
            chatEndBtn.addEventListener('click', () => {
                if (chatEnded) return;
                chatEnded = true;
                chatOptions.innerHTML = '';
                if (chatInputArea) chatInputArea.style.display = 'none';
                if (currentInputHandler && chatSendBtn) {
                    chatSendBtn.removeEventListener('click', currentInputHandler);
                    currentInputHandler = null;
                }
                chatEndBtn.disabled = true;
                const endMsg = document.createElement('div');
                endMsg.className = 'chat-msg bot';
                endMsg.innerHTML = '👋 Chat ended. Thank you for chatting with us!';
                chatMessages.appendChild(endMsg);
                chatTranscript.push({ sender: 'bot', message: 'Chat ended.' });
                scrollToBottom();
                saveTranscript();
                localStorage.removeItem('mico_chat_transcript');
            });
        }

        function scrollToBottom() {
            chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' });
        }

        function recordAndShowUserMessage(text) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'chat-msg user';
            msgDiv.textContent = text;
            chatMessages.appendChild(msgDiv);
            chatTranscript.push({ sender: 'user', message: text });
            scrollToBottom();
            saveTranscript();
        }

        function saveTranscript() {
            if (chatTranscript.length === 0) return;
            // Save to localStorage
            localStorage.setItem('mico_chat_transcript', JSON.stringify(chatTranscript));

            fetch(document.body.getAttribute('data-baseurl') + 'api/chatbot_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ transcript: chatTranscript })
            }).catch(err => console.error('Chat save error:', err));
        }

        function handleOptionClick(opt) {
            if (chatEnded) return;
            const allBtns = chatOptions.querySelectorAll('.chat-opt-btn');
            allBtns.forEach(b => b.disabled = true);
            recordAndShowUserMessage(opt.label);
            chatOptions.innerHTML = '';

            if (opt.action === 'goto_node' && opt.target) {
                loadNode(opt.target);
            } else if (opt.action === 'link' && opt.target) {
                window.location.href = opt.target;
            } else if (opt.action === 'call') {
                saveTranscript();
                let target = opt.target;
                if (!target) {
                    const fcb = document.querySelector('.floating-call-btn');
                    if (fcb) target = fcb.getAttribute('href').replace('tel:', '');
                }
                if (target) {
                    window.location.href = `tel:${target}`;
                } else {
                    const failMsg = document.createElement('div');
                    failMsg.className = 'chat-msg bot';
                    failMsg.textContent = 'Phone number not available.';
                    chatMessages.appendChild(failMsg);
                    chatTranscript.push({ sender: 'bot', message: 'Phone number not available.' });
                    scrollToBottom();
                }
            }
        }

        function loadNode(nodeId) {
            if (chatEnded) return;
            const node = chatData.nodes[nodeId];
            if (!node) return;

            // Clear old options and hide input
            chatOptions.innerHTML = '';
            if (chatInputArea) chatInputArea.style.display = 'none';

            // Remove previous input handler to avoid duplicate listeners
            if (currentInputHandler && chatSendBtn) {
                chatSendBtn.removeEventListener('click', currentInputHandler);
                currentInputHandler = null;
            }

            // Show typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'chat-msg bot chat-typing';
            typingIndicator.innerHTML = '<span></span><span></span><span></span>';
            chatMessages.appendChild(typingIndicator);
            scrollToBottom();

            setTimeout(() => {
                typingIndicator.remove();

                // Show bot message
                const msgDiv = document.createElement('div');
                msgDiv.className = 'chat-msg bot';
                msgDiv.innerHTML = node.message.replace(/\n/g, '<br>');
                chatMessages.appendChild(msgDiv);
                chatTranscript.push({ sender: 'bot', message: node.message });
                scrollToBottom();

                // ── Handle reply_type ──
                if (node.reply_type === 'user_input') {
                    // Show text input for user input questions
                    if (chatInputArea) {
                        chatInputArea.style.display = '';
                        chatInput.value = '';
                        chatInput.placeholder = node.input_var_name
                            ? `Enter your ${node.input_var_name.replace(/_/g, ' ')}...`
                            : 'Type your answer...';
                        chatInput.focus();

                        // Find the next_node_id from the first option (if any)
                        const nextNodeId = (node.options && node.options.length > 0 && node.options[0].action === 'goto_node')
                            ? node.options[0].target
                            : null;

                        currentInputHandler = () => {
                            const text = chatInput.value.trim();
                            if (!text) return;
                            recordAndShowUserMessage(text);
                            chatInput.value = '';
                            chatInputArea.style.display = 'none';

                            if (nextNodeId) {
                                loadNode(nextNodeId);
                            } else {
                                // Dead-end: show a thank you message
                                setTimeout(() => {
                                    const ty = document.createElement('div');
                                    ty.className = 'chat-msg bot';
                                    ty.textContent = 'Thank you! Our team will review your response.';
                                    chatMessages.appendChild(ty);
                                    chatTranscript.push({ sender: 'bot', message: 'Thank you! Our team will review your response.' });
                                    scrollToBottom();
                                    saveTranscript();
                                }, 600);
                            }
                        };

                        chatSendBtn.addEventListener('click', currentInputHandler);

                        // Enter key handler
                        const enterHandler = (e) => {
                            if (e.key === 'Enter') {
                                currentInputHandler();
                                chatInput.removeEventListener('keypress', enterHandler);
                            }
                        };
                        chatInput.addEventListener('keypress', enterHandler);
                    }
                } else {
                    // Preset buttons mode — hide input, show option buttons
                    if (chatInputArea) chatInputArea.style.display = 'none';

                    if (node.options && node.options.length > 0) {
                        node.options.forEach((opt, index) => {
                            const btn = document.createElement('button');
                            btn.className = 'chat-opt-btn';
                            btn.style.animationDelay = `${index * 0.1}s`;
                            btn.textContent = opt.label;
                            btn.addEventListener('click', () => handleOptionClick(opt));
                            chatOptions.appendChild(btn);
                        });
                    }
                }
            }, 800);
        }
    }

    // ── Neural Network Animated Background ─────────────────
    const canvas = document.getElementById('neural-bg');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        const labels = ['E-Commerce', 'Delivery App', 'B2B Portal', 'Social', 'CRM', 'ERP', 'AI Agent', 'SaaS', 'Fintech', 'Logistics'];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = document.getElementById('hero').offsetHeight;
        }

        window.addEventListener('resize', resize);
        resize();

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.8;
                this.vy = (Math.random() - 0.5) * 0.8;
                this.radius = Math.random() * 2 + 1;
                // Add label to ~20% of particles
                this.label = Math.random() > 0.8 ? labels[Math.floor(Math.random() * labels.length)] : null;
                // Alternate between Emerald and Gold
                this.color = Math.random() > 0.5 ? '16, 185, 129' : '251, 191, 36';
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                if (this.x < 0 || this.x > width) this.vx *= -1;
                if (this.y < 0 || this.y > height) this.vy *= -1;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${this.color}, 0.8)`;
                ctx.fill();

                if (this.label) {
                    ctx.font = '10px Inter';
                    ctx.fillStyle = `rgba(255, 255, 255, 0.4)`;
                    ctx.fillText(this.label, this.x + 8, this.y + 4);
                }
            }
        }

        // Initialize particles based on screen width
        const particleCount = Math.min(Math.floor(width / 15), 100);
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);

            // Update and draw particles
            particles.forEach(p => {
                p.update();
                p.draw();
            });

            // Draw connections
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 150) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        // Opacity based on distance
                        const alpha = 1 - (distance / 150);
                        // Gradient connection
                        const grad = ctx.createLinearGradient(particles[i].x, particles[i].y, particles[j].x, particles[j].y);
                        grad.addColorStop(0, `rgba(${particles[i].color}, ${alpha * 0.5})`);
                        grad.addColorStop(1, `rgba(${particles[j].color}, ${alpha * 0.5})`);

                        ctx.strokeStyle = grad;
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        animate();
    }

    // ── Custom Modern Dropdown ──────────────────────────────
    document.querySelectorAll('select.modern-select').forEach(select => {
        select.style.display = 'none';

        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-wrapper';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        const trigger = document.createElement('div');
        trigger.className = 'custom-select-trigger form-input';

        // Find default display text
        let defaultText = select.options.length > 0 ? select.options[0].text : 'Select...';
        trigger.innerHTML = `<span class="placeholder">${defaultText}</span><div class="arrow"></div>`;
        wrapper.appendChild(trigger);

        const optionsDiv = document.createElement('div');
        optionsDiv.className = 'custom-options';

        Array.from(select.options).forEach((option, index) => {
            if (index === 0 && option.value === '') {
                return; // Often the placeholder, skip rendering as a clickable option
            }

            const customOption = document.createElement('div');
            customOption.className = 'custom-option';
            customOption.textContent = option.text;
            customOption.dataset.value = option.value;
            if (option.selected) customOption.classList.add('selected');

            customOption.addEventListener('click', function () {
                const span = trigger.querySelector('span');
                span.textContent = this.textContent;
                span.classList.remove('placeholder');
                select.value = this.dataset.value;
                select.dispatchEvent(new Event('change'));

                optionsDiv.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                wrapper.classList.remove('open');
            });

            optionsDiv.appendChild(customOption);
        });

        wrapper.appendChild(optionsDiv);

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                if (w !== wrapper) w.classList.remove('open');
            });
            wrapper.classList.toggle('open');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-select-wrapper').forEach(w => w.classList.remove('open'));
    });
});

