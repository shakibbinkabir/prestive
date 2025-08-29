<?php /** @var string $termsText */ /** @var string $termsUrl */ ?>
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold text-gold-400 mb-4">Terms & Conditions</h1>
    <div class="prose prose-invert max-w-none">
        <p><?= nl2br(htmlspecialchars($termsText)) ?></p>
        <p class="mt-4">Full terms: <a class="text-gold-400 underline" href="<?= htmlspecialchars($termsUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($termsUrl) ?></a></p>
    </div>
    <div class="mt-6">
        <a class="text-sm text-gray-300 underline" href="/">Back</a>
    </div>
    <?php $title = $title ?? 'Terms & Conditions'; ?>
</div>
