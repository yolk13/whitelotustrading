</main>

<footer class="w-full py-section-gap px-margin-mobile md:px-margin-desktop bg-deep-royal text-pure-white">
    <div class="max-w-[1280px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-gutter">
        <div class="space-y-6">
            <span class="font-headline-sm text-headline-sm text-vibrant-amber font-bold">White Lotus Trading</span>
            <p class="font-body-md text-body-md text-surface-container-highest opacity-70">Elevating standards in industrial engineering and holistic wellness through ethical global trading.</p>
            <div class="flex space-x-4">
                <a class="w-10 h-10 rounded-full border border-pure-white/20 flex items-center justify-center hover:bg-vibrant-amber hover:text-deep-royal transition-all" href="#"><span class="material-symbols-outlined">share</span></a>
                <a class="w-10 h-10 rounded-full border border-pure-white/20 flex items-center justify-center hover:bg-vibrant-amber hover:text-deep-royal transition-all" href="#"><span class="material-symbols-outlined">public</span></a>
            </div>
        </div>
        <div class="space-y-6">
            <h4 class="font-label-caps text-label-caps uppercase tracking-widest text-vibrant-amber">Quick Links</h4>
            <ul class="space-y-4">
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/">Home</a></li>
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/products">Products</a></li>
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/blog">Blog</a></li>
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/inquiry">Inquiry</a></li>
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/contact">Contact</a></li>
            </ul>
        </div>
        <div class="space-y-6">
            <h4 class="font-label-caps text-label-caps uppercase tracking-widest text-vibrant-amber">Legal</h4>
            <ul class="space-y-4">
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/contact">Privacy Policy</a></li>
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="/contact">Terms of Service</a></li>
                <li><a class="font-body-md text-surface-container-highest hover:text-vibrant-amber underline transition-all" href="#">ISO Certifications</a></li>
            </ul>
        </div>
        <div class="space-y-6">
            <h4 class="font-label-caps text-label-caps uppercase tracking-widest text-vibrant-amber">Inquiry Center</h4>
            <p class="font-body-md text-surface-container-highest opacity-70">Request a project quote or product catalog.</p>
            <form class="flex flex-col sm:flex-row gap-4" method="POST" action="/inquiry">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                <input type="email" name="email" class="flex-grow bg-pure-white/10 border-pure-white/20 rounded-lg px-4 py-3 text-pure-white placeholder:text-pure-white/40 focus:ring-2 focus:ring-vibrant-amber focus:border-transparent outline-none" placeholder="Email Address" required>
                <button type="submit" name="quick_subscribe" class="bg-vibrant-amber text-charcoal-text px-8 py-3 rounded-lg font-label-caps text-label-caps font-bold hover:bg-pure-white transition-all">Subscribe</button>
            </form>
            <div class="pt-8 border-t border-pure-white/10 flex flex-col sm:flex-row justify-between items-center gap-4">
                <span class="font-label-caps text-label-caps text-surface-container-highest opacity-50">&copy; <?= date('Y') ?> White Lotus Trading - F.Z.E. All Rights Reserved.</span>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
