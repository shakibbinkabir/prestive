<div class="min-h-screen flex items-center justify-center bg-black py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Admin Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Access the administrative dashboard
            </p>
        </div>
        <form class="mt-8 space-y-6" action="/admin/login" method="POST">
            <?= \App\Core\CSRF::field() ?>
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-gold-400 focus:border-gold-400 focus:z-10 sm:text-sm"
                           placeholder="Enter your email">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-gold-400 focus:border-gold-400 focus:z-10 sm:text-sm"
                           placeholder="Enter your password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-black bg-gold-400 hover:bg-gold-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-400">
                    Sign In
                </button>
            </div>
        </form>
    </div>
</div>