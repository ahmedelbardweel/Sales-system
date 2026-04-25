<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'نظام إدارة المبيعات' }}</title>

    <!-- PWA / Offline Support -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0070cc">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @livewireStyles
</head>

<body>
    <script>
        // Force unregister service worker to clear potential caching issues
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister();
                }
            });
        }
    </script>

    <!-- TOP NAVBAR -->
    <nav class="navbar">
        <div class="navbar-inner">
            <!-- Logo -->
            <a href="/" class="navbar-logo">
                <span>نظام الإدارة</span>
            </a>

            <!-- Desktop Nav Links -->
            <ul class="navbar-links">
                <li>
                    <a href="/" class="{{ request()->is('/') ? 'nav-active' : '' }}">
                        <i data-lucide="layout-dashboard"></i>
                        الرئيسية
                    </a>
                </li>
                <li>
                    <a href="/products" class="{{ request()->is('products') ? 'nav-active' : '' }}">
                        <i data-lucide="package"></i>
                        المخزون
                    </a>
                </li>
                <li>
                    <a href="/purchases" class="{{ request()->is('purchases') ? 'nav-active' : '' }}">
                        <i data-lucide="shopping-bag"></i>
                        المشتريات
                    </a>
                </li>
                <li>
                    <a href="/customers" class="{{ request()->is('customers') ? 'nav-active' : '' }}">
                        <i data-lucide="users"></i>
                        الزبائن والديون
                    </a>
                </li>
                <li>
                    <a href="/pos" class="{{ request()->is('pos') ? 'nav-active' : '' }}">
                        <i data-lucide="shopping-cart"></i>
                        الكاشير
                    </a>
                </li>
            </ul>

            <!-- Mobile Hamburger -->
            <button class="hamburger" id="hamburger-btn" onclick="toggleMobileNav()">
                <i data-lucide="menu"></i>
            </button>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div class="mobile-nav" id="mobile-nav" style="display:none">
            <a href="/" class="{{ request()->is('/') ? 'nav-active' : '' }}" onclick="closeMobileNav()">
                <i data-lucide="layout-dashboard"></i> الرئيسية
            </a>
            <a href="/products" class="{{ request()->is('products') ? 'nav-active' : '' }}" onclick="closeMobileNav()">
                <i data-lucide="package"></i> المخزون
            </a>
            <a href="/purchases" class="{{ request()->is('purchases') ? 'nav-active' : '' }}"
                onclick="closeMobileNav()">
                <i data-lucide="shopping-bag"></i> المشتريات
            </a>
            <a href="/customers" class="{{ request()->is('customers') ? 'nav-active' : '' }}"
                onclick="closeMobileNav()">
                <i data-lucide="users"></i> الزبائن والديون
            </a>
            <a href="/pos" class="{{ request()->is('pos') ? 'nav-active' : '' }}" onclick="closeMobileNav()">
                <i data-lucide="shopping-cart"></i> الكاشير
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <script>
        // Initialize icons on first load
        lucide.createIcons();

        // Re-initialize icons after every Livewire update/navigation
        document.addEventListener('livewire:init', () => {
            lucide.createIcons();
        });

        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons();
        });

        document.addEventListener('livewire:update', () => {
            lucide.createIcons();
        });

        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons();
        });

        function toggleMobileNav() {
            const nav = document.getElementById('mobile-nav');
            if (nav.style.display === 'none' || nav.style.display === '') {
                nav.style.display = 'flex';
                lucide.createIcons();
            } else {
                nav.style.display = 'none';
            }
        }

        function closeMobileNav() {
            document.getElementById('mobile-nav').style.display = 'none';
        }
    </script>
    @livewireScripts
</body>

</html>