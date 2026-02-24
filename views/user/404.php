<?php
$seo = ['title' => 'Page Not Found | ' . APP_NAME];
?>
<section class="min-h-[80vh] flex items-center justify-center px-4 relative overflow-hidden">
    <div class="max-w-2xl w-full text-center relative z-10 py-16 px-8 rounded-3xl backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl">
        <h1 class="text-9xl font-black mb-4 bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent opacity-50">404</h1>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-white leading-tight">
            Lost in the <span class="text-cyan-400">Digital Void?</span>
        </h2>
        <p class="text-lg text-gray-400 mb-10 max-w-md mx-auto">
            The page you are looking for might have been moved, deleted, or never existed in this dimension.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= baseUrl() ?>" class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold transition-all hover:scale-105 active:scale-95 shadow-lg shadow-cyan-500/20">
                <svg class="mr-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to Reality
            </a>
            <a href="<?= baseUrl('contact') ?>" class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-white/10 text-white font-semibold backdrop-blur-md border border-white/10 transition-all hover:bg-white/20">
                Contact Support
            </a>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="absolute top-1/4 -left-20 w-64 h-64 bg-cyan-600/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
</section>
