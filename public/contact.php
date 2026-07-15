<?php

$pageTitle = 'Contact Us | White Lotus Trading';

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="mb-12 text-center">
        <span class="font-label-caps text-label-caps text-vibrant-amber uppercase tracking-widest block mb-2">Get in Touch</span>
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal">Contact Us</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        <div class="space-y-8">
            <div>
                <h2 class="font-headline-md text-headline-md text-deep-royal mb-6">Our Office</h2>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-deep-royal/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-deep-royal">location_on</span>
                        </div>
                        <div>
                            <h4 class="font-headline-sm text-body-lg text-deep-royal font-bold">Address</h4>
                            <p class="font-body-md text-body-md text-on-surface-variant">White Lotus Trading - F.Z.E.<br>Dubai, United Arab Emirates</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-vibrant-amber/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-vibrant-amber">call</span>
                        </div>
                        <div>
                            <h4 class="font-headline-sm text-body-lg text-deep-royal font-bold">Phone</h4>
                            <p class="font-body-md text-body-md text-on-surface-variant">+971 4 000 0000</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-deep-royal/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-deep-royal">mail</span>
                        </div>
                        <div>
                            <h4 class="font-headline-sm text-body-lg text-deep-royal font-bold">Email</h4>
                            <p class="font-body-md text-body-md text-on-surface-variant">info@whitelotustrading.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-divider-gray pt-8">
                <h2 class="font-headline-md text-headline-md text-deep-royal mb-4">Business Hours</h2>
                <div class="space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <p>Sunday - Thursday: 9:00 AM - 6:00 PM</p>
                    <p>Friday - Saturday: Closed</p>
                </div>
            </div>
        </div>

        <div class="glass-card p-8 md:p-12 rounded-2xl" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-6">Send a Message</h3>
            <p class="font-body-md text-body-md text-on-surface-variant mb-6">Alternatively, <a href="/inquiry" class="text-deep-royal underline font-bold">use our inquiry form</a> for product-specific requests.</p>
            <form method="POST" action="/inquiry" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Name</label>
                        <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="name" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Email</label>
                        <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="email" type="email" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Subject</label>
                    <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="subject" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Message</label>
                    <textarea class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="message" rows="5" required></textarea>
                </div>
                <input type="hidden" name="division" value="General">
                <button type="submit" class="w-full bg-vibrant-amber text-charcoal-text px-8 py-4 rounded-xl font-headline-sm text-headline-sm hover:shadow-lg transition-all">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
