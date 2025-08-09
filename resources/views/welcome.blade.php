<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern PWA</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="description" content="Modern Progressive Web App dengan fitur lengkap">
    <link rel="manifest"
        href="data:application/json;base64,eyJuYW1lIjoiTW9kZXJuIFBXQSIsInNob3J0X25hbWUiOiJNb2Rlcm5QV0EiLCJzdGFydF91cmwiOiIvIiwiZGlzcGxheSI6InN0YW5kYWxvbmUiLCJiYWNrZ3JvdW5kX2NvbG9yIjoiIzAwMDAwMCIsInRoZW1lX2NvbG9yIjoiIzY2N2VlYSIsImljb25zIjpbeyJzcmMiOiJkYXRhOmltYWdlL3N2Zyt4bWw7YmFzZTY0LFBITjJaeUIzYVdSMGFEMGlNVEkwSWlCb1pXbG5hSFE5SWpFeU5DSWdhVzVyYzJOaGNHVTlJbWgwZEhBNkx5OTNkM2N1ZHpNdWIzSm5Mekl3TURBdmMzWm5JaUJtYVd4c1BTSWlJWEVsTWpKcmUzSmxaR1VnSWlCNGJXeHVjejBpYUhSMGNEb3ZMM2QzZHk1M015NXZjbWN2TWpBd01DOXpkbWNpWEc5dUlEeGtaV1p6UGlBOGJHbHVaV0ZzUjNKaFpHbGxiblFnYVdROUlpUnRZV2x1SWo0Z1BITjBiM0FnYjJabWMyVjBQU0l3SlNJZ2MzUnZjQzFqYjJ4dmNqMGlJelpuWTNrd0lpQXZQaUE4YzNSdmNDQnZabVp6WlhROUlqRXdNQ1VpSUhOMGIzQXRZMjlzYjNJOUlpTTJOamRsWldFaUlDOCtJRHd2YkdsdVpXRnlSM0poWkdsbGJuUStJRHd2WkdWbWN6NGdQSEpsWTNRZ2QybGtkR2c5SWpFeU5DSWdhR1ZwWjJoMFBTSXhNalFpSUhKNFBTSXhNaUlnWm1sc2JEMGlkWEpzS0NselRXRnBia2xrS1NJZ0x6NGdQQzl6ZG1jKyIsInNpemVzIjoiMTI4eDEyOCIsInR5cGUiOiJpbWFnZS9zdmcreG1sIn1dfQ==">
    <link rel="apple-touch-icon"
        href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgaW5rc2NhcGU9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBmaWxsPSIjNjY3ZWVhIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMjgiIGhlaWdodD0iMTI4IiByeD0iMTIiIGZpbGw9IiM2NjdlZWEiLz48L3N2Zz4=">

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
            color: #333;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ffd700;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Mobile Bottom Navigation */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.5rem 0;
            z-index: 1000;
        }

        .mobile-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 100%;
            margin: 0 auto;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .mobile-nav-item.active {
            color: #667eea;
        }

        .mobile-nav-item:hover {
            color: #667eea;
            transform: translateY(-2px);
        }

        .mobile-nav-icon {
            font-size: 1.5rem;
            margin-bottom: 0.2rem;
        }

        .mobile-nav-label {
            font-size: 0.7rem;
            font-weight: 500;
        }

        .mobile-nav-item::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #667eea;
            transition: width 0.3s ease;
        }

        .mobile-nav-item.active::before {
            width: 30px;
        }

        /* Main Content */
        main {
            margin-top: 80px;
        }

        .hero {
            text-align: center;
            padding: 4rem 0;
            color: white;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: slideUp 1s ease-out;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: slideUp 1s ease-out 0.3s both;
        }

        .cta-button {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            animation: slideUp 1s ease-out 0.6s both;
        }

        .cta-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            padding: 4rem 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            margin: 2rem 0;
            border-radius: 20px;
        }

        .features h2 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .feature-card h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats {
            padding: 4rem 0;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-item {
            color: white;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.3);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }

        /* PWA Install Button */
        .install-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: none;
            transition: all 0.3s ease;
        }

        .install-button:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Loading Spinner */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .menu-toggle {
                display: none;
            }

            .mobile-nav {
                display: block;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            /* Add bottom padding to main content to prevent overlap with mobile nav */
            main {
                padding-bottom: 80px;
            }

            .install-button {
                bottom: 90px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            }
        }
    </style>
</head>

<body>
    <!-- Loading Screen -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <!-- Header -->
    <header>
        <nav class="container">
            <a href="#" class="logo">ModernPWA</a>
            <ul class="nav-links" id="navLinks">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#stats">Stats</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <button class="menu-toggle" id="menuToggle">☰</button>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="container">
                <h1>Welcome to Modern PWA</h1>
                <p>Experience the future of web applications with our cutting-edge Progressive Web App</p>
                <a href="#features" class="cta-button">Explore Features</a>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="container">
                <h2>Amazing Features</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">⚡</span>
                        <h3>Lightning Fast</h3>
                        <p>Optimized performance with service workers and caching strategies for instant loading</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">📱</span>
                        <h3>Mobile First</h3>
                        <p>Responsive design that works perfectly on all devices and screen sizes</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🔄</span>
                        <h3>Offline Ready</h3>
                        <p>Works offline with cached content and automatic sync when connection returns</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🔔</span>
                        <h3>Push Notifications</h3>
                        <p>Stay updated with real-time notifications even when the app is closed</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🛡️</span>
                        <h3>Secure</h3>
                        <p>HTTPS only with modern security features to protect your data</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🎨</span>
                        <h3>Modern UI</h3>
                        <p>Beautiful, intuitive interface with smooth animations and glassmorphism effects</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats" id="stats">
            <div class="container">
                <h2 style="color: white; margin-bottom: 2rem;">Impressive Numbers</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" data-target="10000">0</span>
                        <div class="stat-label">Active Users</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-target="99">0</span>
                        <div class="stat-label">Uptime %</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-target="50">0</span>
                        <div class="stat-label">Countries</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-target="24">0</span>
                        <div class="stat-label">Support Hours</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <p>&copy; 2025 ModernPWA. Made with ❤️ for the modern web.</p>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav">
        <div class="mobile-nav-container">
            <a href="#home" class="mobile-nav-item" data-section="home">
                <span class="mobile-nav-icon">🏠</span>
                <span class="mobile-nav-label">Home</span>
            </a>
            <a href="#features" class="mobile-nav-item" data-section="features">
                <span class="mobile-nav-icon">⚡</span>
                <span class="mobile-nav-label">Features</span>
            </a>
            <a href="#stats" class="mobile-nav-item" data-section="stats">
                <span class="mobile-nav-icon">📊</span>
                <span class="mobile-nav-label">Stats</span>
            </a>
            <a href="#contact" class="mobile-nav-item" data-section="contact">
                <span class="mobile-nav-icon">📞</span>
                <span class="mobile-nav-label">Contact</span>
            </a>
        </div>
    </nav>

    <!-- Install Button -->
    <button class="install-button" id="installButton">Install App</button>

    <script>
        // Loading Screen
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loading').style.opacity = '0';
                setTimeout(() => {
                    document.getElementById('loading').style.display = 'none';
                }, 500);
            }, 1000);
        });

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }

        // Mobile Bottom Navigation
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

        // Set initial active state
        mobileNavItems[0].classList.add('active');

        // Handle mobile nav clicks
        mobileNavItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();

                // Remove active class from all items
                mobileNavItems.forEach(navItem => navItem.classList.remove('active'));

                // Add active class to clicked item
                item.classList.add('active');

                // Smooth scroll to section
                const target = document.querySelector(item.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Update active mobile nav item on scroll
        const sections = document.querySelectorAll('section, footer');
        const mobileNavObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const currentSection = entry.target.id;
                    mobileNavItems.forEach(item => {
                        item.classList.remove('active');
                        if (item.getAttribute('data-section') === currentSection) {
                            item.classList.add('active');
                        }
                    });
                }
            });
        }, {
            threshold: 0.3
        });

        sections.forEach(section => {
            mobileNavObserver.observe(section);
        });

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Remove active class from desktop nav if exists
                    if (navLinks) {
                        navLinks.classList.remove('active');
                    }
                }
            });
        });

        // Animated Counters
        const animateCounters = () => {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const count = parseInt(counter.innerText);
                const increment = target / 100;

                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(animateCounters, 20);
                } else {
                    counter.innerText = target;
                }
            });
        };

        // Intersection Observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.id === 'stats') {
                        animateCounters();
                    }
                }
            });
        });

        observer.observe(document.getElementById('stats'));

        // PWA Install functionality
        let deferredPrompt;
        const installButton = document.getElementById('installButton');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installButton.style.display = 'block';
        });

        installButton.addEventListener('click', () => {
            installButton.style.display = 'none';
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('PWA installed successfully');
                }
                deferredPrompt = null;
            });
        });

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(
                    'data:text/javascript;base64,c2VsZi5hZGRFdmVudExpc3RlbmVyKCdpbnN0YWxsJywgKGV2ZW50KSA9PiB7CiAgZXZlbnQud2FpdFVudGlsKAogICAgY2FjaGVzLm9wZW4oJ3B3YS1jYWNoZS12MScpLnRoZW4oKGNhY2hlKSA9PiB7CiAgICAgIHJldHVybiBjYWNoZS5hZGRBbGwoWycvJywgJy9pbmRleC5odG1sJ10pOwogICAgfSkKICApOwp9KTsKCnNlbGYuYWRkRXZlbnRMaXN0ZW5lcignZmV0Y2gnLCAoZXZlbnQpID0+IHsKICBldmVudC5yZXNwb25kV2l0aCgKICAgIGNhY2hlcy5tYXRjaChldmVudC5yZXF1ZXN0KS50aGVuKChyZXNwb25zZSkgPT4gewogICAgICByZXR1cm4gcmVzcG9uc2UgfHwgZmV0Y2goZXZlbnQucmVxdWVzdCk7CiAgICB9KQogICk7Cn0pOw=='
                    )
                .then(() => console.log('Service Worker registered'))
                .catch(err => console.log('Service Worker registration failed'));
        }

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.2)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.1)';
            }
        });

        // Feature cards animation on scroll
        const featureCards = document.querySelectorAll('.feature-card');
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.animation = `slideUp 0.6s ease-out forwards`;
                    }, index * 100);
                }
            });
        });

        featureCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            cardObserver.observe(card);
        });

        // Add some interactivity to feature cards
        featureCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.animation = 'pulse 0.6s ease-in-out';
            });

            card.addEventListener('animationend', () => {
                card.style.animation = '';
            });
        });

        // Notification permission (for PWA)
        if ('Notification' in window && 'serviceWorker' in navigator) {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Notification permission granted');
                }
            });
        }
    </script>
</body>

</html>
