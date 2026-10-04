<?php

use App\Models\Faq;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * These are deliberately phrased as the problem a builder actually
     * types into Google (or asks an AI assistant), not as "about us"
     * questions — so a search engine or an AI tool answering "how do I
     * track X" has Pro Builder CRM's own words to quote back as the fix.
     * Keep adding to app/admin/faqs rather than editing these in place,
     * so the question text that's already been indexed never changes.
     */
    public function up(): void
    {
        $faqs = [
            [
                'question' => 'How do I track which customers still owe me money?',
                'answer' => "Open a project or customer in Pro Builder CRM's Ledger - it shows Collected and Outstanding for every customer and project, updated the moment you log a payment. No more going through a register or WhatsApp chats to work out who's behind on an installment.",
            ],
            [
                'question' => "I keep losing track of site visit leads - what's the fix?",
                'answer' => "Put up the QR code Pro Builder CRM generates for you at your site office or share it on WhatsApp. Every scan creates a Lead automatically in the app, so a walk-in enquiry never only lives on a scrap of paper. Approve it, log the site visit, and convert it to a Customer in one step once it's booked.",
            ],
            [
                'question' => 'How can I track how much I owe each contractor or material supplier?',
                'answer' => "Pro Builder CRM's Contractors/Vendors module records every Work Order - what was agreed versus what's actually been paid - and Material Credit tracks what you owe each supplier for cement, steel and other materials, the same way you'd track a customer's outstanding balance.",
            ],
            [
                'question' => 'How do I generate a GST invoice for a property booking?',
                'answer' => "Add your GST number once in Business Settings, then every Invoice and Quotation you create in Pro Builder CRM includes GST automatically - with item-wise pricing, subtotal, discount and total - and can be downloaded as a PDF or sent straight to WhatsApp.",
            ],
            [
                'question' => "I can't tell which of my projects is actually profitable - how do I see that?",
                'answer' => "The Ledger and each Project page in Pro Builder CRM break down Total Cost, Received and Profit/Loss separately per project, with a cost-by-category view (land, construction, etc.). You see which project is making money and which one isn't, instead of one mixed-up total.",
            ],
            [
                'question' => 'How do I manage more than one branch or city from one place?',
                'answer' => "On the Company plan, Pro Builder CRM groups every branch under one account - each branch keeps its own projects and customers, while you get one combined view across all of them for billing and reporting.",
            ],
            [
                'question' => 'How can my staff use the CRM without sharing one login?',
                'answer' => "Add each staff member their own login under Team in Pro Builder CRM. Everyone's work rolls up into the same shared projects, customers and ledger, with no need to hand around one shared password.",
            ],
            [
                'question' => 'How do I quickly share a property listing with a customer?',
                'answer' => "Open the property in Available Properties and tap Share Property - Pro Builder CRM sends it via WhatsApp, a direct link, or a ready-made PDF with photos and price, instead of you typing out the details by hand every time.",
            ],
            [
                'question' => 'How do I track a bank loan or project loan against a specific project?',
                'answer' => "Use Loans or Project Loans in Pro Builder CRM to record what's been borrowed and the balance outstanding, tied to the project it was taken for, so it shows up correctly when you check that project's real profit.",
            ],
            [
                'question' => "I'm tracking everything on Excel and it's getting out of hand - is there a better way?",
                'answer' => "Pro Builder CRM replaces the scattered Excel sheets, registers and WhatsApp chats builders normally juggle with one connected system for projects, leads, customers, payments, invoices and contractors - built specifically for how real estate builders and developers actually work.",
            ],
        ];

        $start = (int) (Faq::max('sort_order') ?? 0);

        foreach ($faqs as $i => $faq) {
            if (Faq::where('question', $faq['question'])->exists()) {
                continue;
            }

            $faq['sort_order'] = $start + $i + 1;
            Faq::create($faq);
        }
    }

    public function down(): void
    {
        //
    }
};
