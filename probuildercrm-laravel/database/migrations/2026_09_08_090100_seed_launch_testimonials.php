<?php

use App\Models\Testimonial;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $testimonials = [
            [
                'quote' => "I used to spend a whole evening every week matching payments to customers in Excel. Now every receipt and reminder is automatic - I check the dashboard for two minutes and I know exactly where every project stands.",
                'author_role' => 'Real Estate Builder',
                'author_city' => 'Indore',
                'sort_order' => 1,
            ],
            [
                'quote' => "Loan disbursements from the bank used to be the most confusing part of our books. Pro Builder CRM keeps it all tied to the right unit and customer, so our accountant isn't chasing us for statements anymore.",
                'author_role' => 'Construction Contractor',
                'author_city' => 'Raipur',
                'sort_order' => 2,
            ],
            [
                'quote' => "My brokers can see their own commission and nothing else. My supervisor logs payments from site visits on his phone. Everyone has exactly the access they need, nothing more.",
                'author_role' => 'Property Developer',
                'author_city' => 'Bhopal',
                'sort_order' => 3,
            ],
        ];

        // Only seed if the table is empty — an admin's own edits (or
        // deletions) after launch should never be overwritten by this
        // migration re-running on a later deploy... except migrations
        // never re-run once applied anyway; this guard just protects a
        // fresh install where the table already has rows for some reason.
        if (Testimonial::count() === 0) {
            foreach ($testimonials as $t) {
                Testimonial::create($t);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
