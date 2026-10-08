@extends('layouts.marketing')

@section('title', 'Terms of Service')

@section('content')

<section class="section">
    <div class="container" style="max-width: 760px;">
        <h1 style="font-size: 2rem;">Terms of Service</h1>
        <p style="color: var(--color-ink-soft);">Last updated: {{ now()->format('F j, Y') }}</p>

        <div style="display: flex; flex-direction: column; gap: var(--space-md); color: var(--color-ink-soft); line-height: 1.7;">
            <p>These Terms of Service ("Terms") are a legal agreement between you (and, if you're signing up on behalf of a business, that business) and {{ config('site.legal_name') }} ("we", "us", "our"), governing your access to and use of {{ config('site.name') }} (the "Service"). By creating an account, starting a trial, or otherwise using the Service, you accept these Terms in full. If you do not agree, do not use the Service.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">1. Who can use the Service</h2>
            <p>You must be at least 18 years old and able to form a legally binding contract to use the Service. If you're signing up on behalf of a business, you confirm you're authorized to accept these Terms on that business's behalf, and "you" below refers to that business.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">2. Your account</h2>
            <p>You're responsible for the accuracy of the information you provide, for keeping your login credentials confidential, and for everything that happens under your account, including actions taken by team members you add. Notify us immediately at {{ config('site.email') }} if you suspect unauthorized access.</p>
            <p>A signup is verified by email confirmation; we may grant immediate trial access on that basis without independently verifying your identity or business details beforehand. We reserve the right to suspend or terminate any account found to be fraudulent, impersonating another person or business, or in breach of these Terms, at any time and without prior notice.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">3. Acceptable use</h2>
            <p>You agree not to: use the Service for any unlawful purpose; upload data you don't have the right to store or process; attempt to disrupt, reverse-engineer, or gain unauthorized access to the Service or other customers' data; use the Service to send spam or unsolicited communications; or resell or sublicense the Service without our written permission. We may suspend or terminate access for any violation of this section.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">4. Trials, subscriptions and payments</h2>
            <p>New accounts may start with a free trial period. At the end of the trial, continued access requires an active, paid subscription on one of our published plans. Subscription fees are billed in advance for the plan and billing period you choose, through the payment method(s) we make available (including, where applicable, Google Play Billing for the Android app).</p>
            <p>We may change our prices or plans at any time; changes apply from your next billing cycle and we'll make reasonable efforts to notify you in advance.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">5. Refund Policy</h2>
            <p>You may apply for a refund within 3 days of your purchase/plan start. Requests made after this 3-day window will not be accepted, and the payment will not be refunded. Where a refund request is approved, it can take up to 7 days for the refunded amount to reach you, depending on your bank or payment provider.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">6. Your data</h2>
            <p>You own the business data you enter into {{ config('site.name') }}. We do not sell it or share it with third parties, except where needed to operate the Service itself (e.g. our hosting or payment providers) or where required by law.</p>
            <p>You are solely responsible for the accuracy, legality, and appropriateness of the data you enter — including customer, payment, and booking records — and for having any consents or rights needed to store and process that data. {{ config('site.name') }} provides tools, including an in-app backup/export feature, to make managing your data easier, but keeping your own copies of critical business data is your responsibility, not ours. We recommend downloading and safely storing a backup regularly, and always before a major change.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">7. Service availability</h2>
            <p>We work to keep the Service available and reliable, but we don't guarantee uninterrupted or error-free operation. The Service may be unavailable from time to time for maintenance, updates, or reasons outside our control (including internet, hosting, or third-party service outages). We're not liable for any loss arising from such downtime.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">8. Disclaimer of warranties</h2>
            <p>The Service is provided "as is" and "as available", without warranties of any kind, express or implied, including (to the extent permitted by law) any warranty of merchantability, fitness for a particular purpose, or non-infringement. We do not warrant that the Service will meet your specific requirements or that any errors will be corrected.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">9. Limitation of liability</h2>
            <p>To the maximum extent permitted by law, {{ config('site.legal_name') }} and its officers, employees, and partners will not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits, revenue, data, or business opportunity, arising from or related to your use of (or inability to use) the Service — even if advised of the possibility of such damages.</p>
            <p>Where liability cannot be excluded under applicable law, our total aggregate liability to you for any claim arising out of or relating to the Service is limited to the amount you paid us for the Service in the 3 months immediately before the event giving rise to the claim.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">10. Indemnification</h2>
            <p>You agree to indemnify and hold harmless {{ config('site.legal_name') }} and its officers and employees from any claim, demand, loss, or damage, including reasonable legal fees, arising out of: your use of the Service; the data you enter or store; your violation of these Terms; or your violation of any right of a third party (including your own customers).</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">11. Termination</h2>
            <p>You may stop using the Service and request account closure at any time by contacting {{ config('site.email') }}. We may suspend or terminate your access, with or without notice, if you violate these Terms, fail to pay applicable fees, or if we reasonably believe your account poses a security or legal risk to the Service or other customers. Sections of these Terms that by their nature should survive termination (including data responsibility, limitation of liability, indemnification, and governing law) will survive.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">12. Changes to these Terms</h2>
            <p>We may update these Terms from time to time. We'll update the "Last updated" date above when we do; continued use of the Service after a change means you accept the updated Terms. If a change is material, we'll make reasonable efforts to notify you (e.g. by email).</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">13. Governing law</h2>
            <p>These Terms are governed by the laws of India, without regard to conflict-of-law principles. Subject to applicable law, any dispute arising out of or relating to these Terms or the Service will be subject to the exclusive jurisdiction of the courts located in India.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">14. Contact</h2>
            <p>Questions about these terms? Email us at <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>.</p>
        </div>
    </div>
</section>

@endsection
