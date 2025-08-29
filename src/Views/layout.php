<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Prestive Club - Membership & Training' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?= \App\Core\CSRF::meta() ?>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'gold': {
                            400: '#D4AF37',
                            500: '#B8941F',
                            600: '#9C7A1A'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-black text-white min-h-screen">
    <nav class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <span class="text-xl font-bold text-gold-400">Prestive Club</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-300 hover:text-white px-3 py-2 rounded-md">Home</a>
                    <?php if (\App\Core\Auth::checkAdmin()): ?>
                        <a href="/admin/dashboard" class="text-gray-300 hover:text-white px-3 py-2 rounded-md">Dashboard</a>
                        <form action="/admin/logout" method="POST" class="inline">
                            <?= \App\Core\CSRF::field() ?>
                            <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 rounded-md">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="/admin/login" class="text-gray-300 hover:text-white px-3 py-2 rounded-md">Admin Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        <?php \App\Core\View::partial('components/flash') ?>
        <?= $content ?>
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-400">
                &copy; 2025 Prestive Club. All rights reserved.
            </p>
            <?php if (APP_DEBUG): ?>
                <div class="mt-4 p-4 bg-yellow-900 bg-opacity-50 rounded-lg">
                    <h3 class="text-yellow-400 font-semibold">Developer Mode</h3>
                    <p class="text-sm text-yellow-300 mt-1">
                        Phase 1 Implementation - Basic MVC scaffold with admin auth, CSRF protection, and draft save APIs.
                    </p>
                    <ul class="text-sm text-yellow-300 mt-2 space-y-1">
                        <li>• Admin login: Use credentials from .env (default: admin@example.com / ChangeMe123!)</li>
                        <li>• Draft APIs: POST /api/membership/draft/save and /api/trainee/draft/save</li>
                        <li>• CSRF protected: Include X-CSRF-Token header for API calls</li>
                        <li>• Rate limited: 60 requests per minute per IP</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </footer>

    <script>
        // Set CSRF token for axios/fetch requests
        if (document.querySelector('meta[name="csrf-token"]')) {
            window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        }
    </script>
</body>
</html>