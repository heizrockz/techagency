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

    if (chatToggle && chatPanel && window.chatbotData && window.chatbotData.start_node_id) {
        let chatInitialized = false;
        const chatData = window.chatbotData;

        // Toggle Chat
        chatToggle.addEventListener('click', () => {
            chatPanel.classList.add('active');
            chatToggle.style.transform = 'scale(0)';
            if (!chatInitialized) {
                chatInitialized = true;
                loadNode(chatData.start_node_id);
            }
        });

        chatClose.addEventListener('click', () => {
            chatPanel.classList.remove('active');
            chatToggle.style.transform = 'scale(1)';
        });

        function loadNode(nodeId) {
            const node = chatData.nodes[nodeId];
            if (!node) return;

            // Clear old options
            chatOptions.innerHTML = '';

            // Show typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'chat-msg bot chat-typing';
            let chatTranscript = []; // Store the full conversation

            // Handle Free Text Input Globally for this Widget
            const chatInput = document.getElementById('chatbotInput');
            const chatSendBtn = document.getElementById('chatbotSendBtn');

            function recordAndShowUserMessage(text) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'chat-msg user';
                msgDiv.textContent = text;
                chatMessages.appendChild(msgDiv);
                chatTranscript.push({ sender: 'user', message: text });
                scrollToBottom();
                saveTranscript(); // Auto-save on every user interaction
            }

            if (chatInput && chatSendBtn) {
                chatSendBtn.addEventListener('click', () => {
                    const text = chatInput.value.trim();
                    if (text) {
                        recordAndShowUserMessage(text);
                        chatInput.value = '';

                        // Simple simulated bot response for free text
                        setTimeout(() => {
                            const reply = "Thank you for your message. We have received it and our team will get back to you shortly.";
                            const msgDiv = document.createElement('div');
                            msgDiv.className = 'chat-msg bot';
                            msgDiv.textContent = reply;
                            chatMessages.appendChild(msgDiv);
                            chatTranscript.push({ sender: 'bot', message: reply });
                            scrollToBottom();
                            saveTranscript();

                            // Clear options since user went off-script
                            chatOptions.innerHTML = '';
                        }, 1000);
                    }
                });

                chatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        chatSendBtn.click();
                    }
                });
            }

            function saveTranscript() {
                if (chatTranscript.length === 0) return;
                fetch(document.body.getAttribute('data-baseurl') + 'api/chatbot_save.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ transcript: chatTranscript })
                }).catch(err => console.error('Chat save error:', err));
            }

            function scrollToBottom() {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            }

            function handleOptionClick(opt, btn) {
                // Disable all buttons instantly
                const allBtns = chatOptions.querySelectorAll('.chat-opt-btn');
                allBtns.forEach(b => b.disabled = true);

                // Record user message
                recordAndShowUserMessage(opt.label);

                // Clear options area
                chatOptions.innerHTML = '';
                scrollToBottom();

                // Handle Action
                if (opt.action === 'goto_node' && opt.target) {
                    loadNode(opt.target);
                } else if (opt.action === 'link' && opt.target) {
                    window.location.href = opt.target;
                } else if (opt.action === 'call') {
                    saveTranscript(); // Save before leaving
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
                const node = chatData.nodes[nodeId];
                if (!node) return;

                // Clear old options
                chatOptions.innerHTML = '';

                // Show typing indicator
                const typingIndicator = document.createElement('div');
                typingIndicator.className = 'chat-msg bot chat-typing';
                typingIndicator.innerHTML = '<span></span><span></span><span></span>';
                chatMessages.appendChild(typingIndicator);
                scrollToBottom();

                // Artificial delay for realism
                setTimeout(() => {
                    typingIndicator.remove();

                    // Show bot message
                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'chat-msg bot';
                    msgDiv.innerHTML = node.message.replace(/\n/g, '<br>');
                    chatMessages.appendChild(msgDiv);
                    chatTranscript.push({ sender: 'bot', message: node.message });
                    scrollToBottom();

                    // Render options if any
                    if (node.options && node.options.length > 0) {
                        node.options.forEach((opt, index) => {
                            const btn = document.createElement('button');
                            btn.className = 'chat-opt-btn';
                            btn.style.animationDelay = `${index * 0.1}s`;
                            btn.textContent = opt.label;


                            btn.addEventListener('click', () => handleOptionClick(opt, btn));
                            chatOptions.appendChild(btn);
                        });
                    }
                }, 800);
            }
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

