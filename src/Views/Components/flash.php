<?php
$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
?>

<?php if (!empty($flash)): ?>
    <div x-data="{ show: true }" x-show="show" class="fixed top-4 right-4 z-50 space-y-2">
        <?php foreach ($flash as $type => $message): ?>
            <?php
            $bgColor = match($type) {
                'success' => 'bg-green-600',
                'error' => 'bg-red-600',
                'warning' => 'bg-yellow-600',
                'info' => 'bg-blue-600',
                default => 'bg-gray-600'
            };
            ?>
            <div class="<?= $bgColor ?> text-white px-6 py-4 rounded-lg shadow-lg max-w-md">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium"><?= htmlspecialchars($message) ?></p>
                    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>