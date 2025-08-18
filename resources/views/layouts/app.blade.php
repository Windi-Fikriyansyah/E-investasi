<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Platform Investasi Modern - Bangun Masa Depan Finansial Anda">
    <title>SmartNiuVolt | Platform Investasi Modern</title>
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #f0f7ff;
            --accent: #60a5fa;
            --accent-dark: #3b82f6;
            --text: #1e293b;
            --text-light: #64748b;
            --text-muted: #94a3b8;
            --white: #ffffff;
            --gray: #e2e8f0;
            --gray-light: #f8fafc;
            --dark-gray: #94a3b8;
            --gradient: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            --shadow-large: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
            --rounded-xs: 0.25rem;
            --rounded-sm: 0.5rem;
            --rounded-md: 0.75rem;
            --rounded-lg: 1rem;
            --rounded-xl: 1.5rem;
            --rounded-full: 9999px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.5;
            padding-top: 70px;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Header Styles - More minimal */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            color: var(--text);
            padding: 0 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(37, 99, 235, 0.1);
            box-shadow: var(--shadow-subtle);
        }

        .header-content {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1200px;
        }

        .btn-back {
            position: absolute;
            left: 0;
            background: var(--gray-light);
            border: 1px solid var(--gray);
            font-size: 1.125rem;
            color: var(--text);
            cursor: pointer;
            padding: 0;
            border-radius: var(--rounded-full);
            transition: all 0.2s ease;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back:hover {
            background-color: var(--gray);
            transform: translateY(-1px);
            box-shadow: var(--shadow-soft);
        }

        header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.025em;
        }

        /* Main Content */
        main {
            padding: 1.5rem 1rem 5rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Balance Card - More minimal and clean */
        .balance-card {
            background: var(--gradient);
            color: white;
            border-radius: var(--rounded-xl);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .balance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .balance-header h3 {
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .balance-header i {
            font-size: 1.25rem;
            opacity: 0.6;
        }

        .balance-amount {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            letter-spacing: -0.025em;
        }

        .balance-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            position: relative;
        }

        .balance-actions .btn {
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: var(--rounded-lg);
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
            letter-spacing: -0.025em;
        }

        .btn-primary {
            background-color: rgba(255, 255, 255, 0.95);
            color: var(--primary);
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }

        .btn-outline {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 500;
        }

        .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Profile Menu - Clean and minimal */
        .profile-menu {
            background: white;
            border-radius: var(--rounded-xl);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: 1px solid var(--gray);
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 1.25rem 1.5rem;
            text-decoration: none;
            color: var(--text);
            border-bottom: 1px solid var(--gray);
            transition: all 0.2s ease;
            position: relative;
        }

        .menu-item:last-child {
            border-bottom: none;
        }

        .menu-item:hover {
            background-color: var(--gray-light);
        }

        .menu-item:active {
            transform: scale(0.99);
        }

        .menu-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
            box-shadow: var(--shadow-subtle);
        }

        .menu-icon i {
            font-size: 1rem;
        }

        .menu-content {
            flex: 1;
            min-width: 0;
        }

        .menu-content h4 {
            font-size: 0.925rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.025em;
        }

        .menu-content p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        .menu-item i.fa-chevron-right {
            color: var(--text-muted);
            font-size: 0.75rem;
            transition: all 0.2s ease;
        }

        .menu-item:hover i.fa-chevron-right {
            color: var(--primary);
            transform: translateX(2px);
        }

        /* Bottom Navigation - Cleaner */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1078ca;
            backdrop-filter: blur(20px);
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 1rem 0;
            box-shadow: 0 -1px 10px rgba(37, 99, 235, 0.1);
            z-index: 1000;
            border-top: 1px solid rgba(37, 99, 235, 0.1);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--text-light);
            font-size: 0.75rem;
            padding: 0.5rem;
            transition: all 0.2s ease;
            position: relative;
            border-radius: var(--rounded-sm);
            font-weight: 500;
            color: white !important;
        }




        .nav-item i {
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .balance-card {
                padding: 1.5rem;
            }

            .balance-amount {
                font-size: 1.875rem;
            }

            .balance-actions {
                gap: 0.75rem;
            }

            .menu-item {
                padding: 1rem;
            }
        }

        /* Desktop View */
        @media (min-width: 768px) {
            body {
                padding-top: 0;
                background: #f8fafc;
            }

            header {
                display: none;
            }

            .bottom-nav {
                position: static;
                flex-direction: column;
                width: 80px;
                height: 100vh;
                box-shadow: 1px 0 10px rgba(37, 99, 235, 0.1);
                justify-content: flex-start;
                padding-top: 2rem;
                background: #1078ca;
                border-right: 1px solid var(--gray);
                border-top: none;
            }

            .nav-item {
                margin-bottom: 1.5rem;
                font-size: 0.8rem;
                color: white !important;
            }

            .nav-item i {
                font-size: 1.25rem;
                color: white !important;
            }

            body {
                display: flex;
            }

            main {
                flex-grow: 1;
                padding: 2rem;
                background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <!-- Header for Mobile -->
    <header>
        <div class="header-content">
            <button class="btn-back" onclick="history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1>{{ App\Helpers\RouteHelper::getPageTitle() }}</h1>
        </div>
    </header>


    @include('layouts.sidebar')

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

    <script>
        // Initialize Swiper
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        // Active nav item
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                if (!this.classList.contains('active')) {
                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Handle logout
        function handleLogout(event) {
            event.preventDefault();
            // Add your logout logic here
            console.log('Logout clicked');
        }

        // Add smooth scrolling and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add subtle animations on load
            const elements = document.querySelectorAll('.balance-card, .menu-item');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>
