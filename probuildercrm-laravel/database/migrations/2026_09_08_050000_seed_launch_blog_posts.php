<?php

use App\Models\BlogPost;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->posts() as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }

    public function down(): void
    {
        BlogPost::whereIn('slug', array_column($this->posts(), 'slug'))->delete();
    }

    private function posts(): array
    {
        return [
            [
                'slug' => 'excel-booking-mistake',
                'title' => 'The booking mix-up that made me stop trusting Excel',
                'excerpt' => "Two sales guys, one Excel sheet, and a flat that got sold twice. Here's what actually happened and what I changed after.",
                'category' => 'Operations',
                'date' => '2026-08-14',
                'read_time' => '4 min read',
                'author' => 'Pro Builder CRM Team',
                'featured_image' => 'screenshots/dashboard.png',
                'featured_image_alt' => 'Pro Builder CRM project dashboard showing live unit booking status',
                'content' => [
                    ['type' => 'paragraph', 'text' => "A couple of years ago, before any of this existed, I ran bookings for a 40-unit project out of one Excel file on Google Drive. Two sales guys, one sheet, colour-coded rows — green for booked, yellow for token received, that sort of thing. It worked fine for the first fifteen units."],
                    ['type' => 'paragraph', 'text' => "Then one Saturday, both of them were showing the same flat to two different families at the same time. Neither knew. One had opened the sheet on his phone that morning before it synced, marked the unit as \"hold,\" and gone to meet his client. The other opened it twenty minutes later on his laptop, saw it as available, took a token of ₹51,000 in cash, and marked it booked. By evening we had two \"booked\" families for A-402, and only one flat."],
                    ['type' => 'heading', 'text' => "It wasn't really an Excel problem"],
                    ['type' => 'paragraph', 'text' => "I spent a while being annoyed at Google Sheets for this, which in hindsight was pointless. The sheet did exactly what a shared file does — it showed whatever was last saved to whoever opened it. The actual problem was that nothing forced the two of them to be looking at the same version of the truth at the same moment. A phone with a weak signal in the site office was enough to break the whole system."],
                    ['type' => 'paragraph', 'text' => "Sorting it out cost us one refund, one very awkward phone call, and about a week of one family not trusting us at all until we personally sat with them and worked out a better unit with some price flexibility. Not the end of the world, but not something I wanted to explain to an investor either."],
                    ['type' => 'heading', 'text' => "What changed"],
                    ['type' => 'paragraph', 'text' => "The fix isn't complicated — a unit's status has to update the moment someone touches it, for everyone, not just for the person holding the phone. That's really the whole idea behind the Projects module in this CRM: the second a token is recorded against a unit, it's gone from \"available\" everywhere, on every device, immediately. No refresh, no \"let me check with the office and call you back.\""],
                    ['type' => 'list', 'items' => [
                        "A unit can only be booked once — the system won't let a second booking through against the same unit",
                        "Every team member sees live status, not whatever was last saved locally",
                        "Token amount, date and who took it are recorded automatically, so there's no reconstructing it from memory later",
                    ]],
                    ['type' => 'paragraph', 'text' => "I'm not going to pretend a spreadsheet can't work for a small project run by one person. It can. But the moment more than one person is touching bookings — which for most builders is by unit five or six — that's the moment a shared file stops being enough."],
                ],
            ],
            [
                'slug' => 'chasing-payments-without-sounding-rude',
                'title' => "Chasing a late payment without sounding like a debt collector",
                'excerpt' => "Most builders either go silent on a late installment or come in too hard and damage the relationship. There's a better middle.",
                'category' => 'Collections',
                'date' => '2026-08-22',
                'read_time' => '5 min read',
                'author' => 'Pro Builder CRM Team',
                'featured_image' => 'screenshots/analytics.png',
                'featured_image_alt' => 'Pro Builder CRM collections and payment reminder analytics',
                'content' => [
                    ['type' => 'paragraph', 'text' => "Ask any builder what the worst part of the job is and a good number will say the same thing: asking your own customer for money. It's a strange discomfort — you've delivered on your side, the payment schedule was agreed and signed, and yet picking up the phone to say \"your installment is 12 days late\" still feels awkward, especially with someone you've had a good relationship with through the whole buying process."],
                    ['type' => 'paragraph', 'text' => "What I've seen builders do wrong falls into two camps. One camp goes quiet — they let it slide a week, then two, telling themselves the customer will remember on their own. They usually don't, or they assume since nobody called, it isn't urgent. The other camp overcorrects — a stern call on day one, sometimes routed through a site manager who doesn't have the full relationship context, and it comes across as aggressive over something that was, half the time, a genuine oversight."],
                    ['type' => 'heading', 'text' => "The gap is usually just visibility"],
                    ['type' => 'paragraph', 'text' => "Most customers aren't avoiding payment. They forgot, or their own money is tied up somewhere and they need a nudge to prioritise it, or — this happens more than builders admit — nobody on the builder's side actually noticed the due date had passed until a customer walked in and asked why nobody had reminded them."],
                    ['type' => 'paragraph', 'text' => "The single biggest improvement isn't a harder tone. It's a reminder that goes out on day one, automatically, before the customer has had a chance to forget and before it's had time to become a two-week-old awkward conversation. A message that lands the morning after the due date, sent from the same WhatsApp number the customer already has saved, reading something ordinary like a receipt update rather than a warning, does most of the work with none of the tension."],
                    ['type' => 'list', 'items' => [
                        "Send the first reminder on or right after the due date, not a week later — the tone can stay completely neutral this early",
                        "Keep the message short and factual: unit, amount due, and a way to pay or reply — no line about consequences",
                        "If it's still unpaid after the second reminder, that's when a personal call makes sense, not before",
                    ]],
                    ['type' => 'paragraph', 'text' => "This is also why we built automatic WhatsApp payment reminders into the CRM rather than leaving it as a manual task on someone's to-do list. It's not about replacing the human conversation — it's about making sure that conversation only has to happen for the small number of customers who genuinely need a call, instead of for everyone, because nobody remembered who was due when."],
                    ['type' => 'paragraph', 'text' => "The other thing that helps more than people expect: sending the receipt the same day a payment is recorded, automatically. Customers who get a prompt, professional receipt tend to pay the next installment faster too — it signals that someone's actually watching the books, not just collecting and hoping."],
                ],
            ],
            [
                'slug' => 'broker-commission-disputes',
                'title' => "Where broker commission disputes actually come from",
                'excerpt' => "It's almost never about the percentage. It's about nobody writing down what was agreed on the day of the deal.",
                'category' => 'Brokers & Commissions',
                'date' => '2026-09-01',
                'read_time' => '4 min read',
                'author' => 'Pro Builder CRM Team',
                'featured_image' => 'screenshots/brokers.png',
                'featured_image_alt' => 'Pro Builder CRM broker commission tracking screen',
                'content' => [
                    ['type' => 'paragraph', 'text' => "A broker brings you a buyer, the deal closes, everyone shakes hands — and three months later there's an argument over whether the commission was 1.5% or 2%, and whether it was on the agreement value or the total sale value including the parking spot that got added later. I've sat through versions of this conversation more times than I can count, on both sides of the table, and it's almost never a bad-faith thing. It's a memory thing."],
                    ['type' => 'heading', 'text' => "The commission is agreed once, verbally, and never again"],
                    ['type' => 'paragraph', 'text' => "Here's the pattern: the terms get discussed on a phone call or in a five-minute conversation at the site office, at the exact moment everyone's focused on closing the deal, not on documenting it. It's genuinely nobody's priority in that moment. Then the deal takes two, three, sometimes six months to actually settle — payments come in over time — and by the time the final commission is due, the person who quoted the percentage may not even be the one processing the payout."],
                    ['type' => 'paragraph', 'text' => "Add a partial payment plan into the mix — say the commission is meant to be released in step with the customer's own installments — and now you've got a running balance that somebody has to track by hand, against a rate that was never written down in the first place. That's the exact setup where a dispute happens, and where it takes real effort to sort out because there's no record either side can point to."],
                    ['type' => 'heading', 'text' => "What actually prevents it"],
                    ['type' => 'list', 'items' => [
                        "The commission rate gets recorded against the deal the day it's agreed, not reconstructed later from memory",
                        "Every payout is logged against that specific deal, so the running balance owed is always visible instead of calculated at the end",
                        "The broker can see their own earned, paid, and pending commission directly, without having to ask",
                    ]],
                    ['type' => 'paragraph', 'text' => "That last point matters more than it sounds. A broker who can check their own statement any time doesn't need to call your office to ask \"where's my commission from the Sharma deal,\" and you don't have to go digging through old WhatsApp chats to answer them. Most of the tension just disappears when both sides are looking at the same number."],
                    ['type' => 'paragraph', 'text' => "None of this needs to be complicated. It just needs to happen at the moment the deal is agreed, not weeks later when someone's trying to remember what was said."],
                ],
            ],
            [
                'slug' => 'common-problems-builders-face',
                'title' => 'The five problems every builder we spoke to had in common',
                'excerpt' => "Before writing a line of code, we sat with a dozen builders and asked what actually goes wrong day to day. The same handful of problems kept coming up.",
                'category' => 'Why We Built This',
                'date' => '2026-09-08',
                'read_time' => '5 min read',
                'author' => 'Pro Builder CRM Team',
                'featured_image' => 'screenshots/mobile-dashboard.png',
                'featured_image_alt' => 'Pro Builder CRM mobile dashboard used to manage a project on the go',
                'content' => [
                    ['type' => 'paragraph', 'text' => "Before any of the CRM existed, we spent about two months just sitting with builders — some running one project, one running four at once, one who'd been in the business for over twenty years and still kept his books in a physical register alongside a laptop he didn't fully trust. We asked all of them the same open question: what actually goes wrong on a normal week? Not what software they wanted, just what breaks."],
                    ['type' => 'paragraph', 'text' => "We expected a dozen different answers. We got the same five problems, over and over, just in a different order depending on how big the project was."],
                    ['type' => 'heading', 'text' => "1. Nobody is sure what's actually available right now"],
                    ['type' => 'paragraph', 'text' => "This came up first almost every time. A unit gets a verbal hold, or a token gets collected at the site office, and the person sitting in the back office doesn't find out until the end of the day — sometimes the next day. In that gap, the same unit gets shown, or worse, sold, to someone else. We wrote about one specific case of this happening on our own project a while back; it turns out it's closer to the rule than the exception once more than one person is touching bookings."],
                    ['type' => 'heading', 'text' => "2. Payments get chased too late, or too awkwardly, or not at all"],
                    ['type' => 'paragraph', 'text' => "Almost every builder we spoke to said the same thing in different words: they hate asking their own customers for money. So installment reminders get delayed out of discomfort, sometimes for weeks, until the gap is big enough that the conversation feels confrontational by the time it happens. A few builders had simply stopped tracking due dates properly and just waited for customers to pay whenever they remembered."],
                    ['type' => 'heading', 'text' => "3. Broker commissions turn into arguments nobody can actually settle"],
                    ['type' => 'paragraph', 'text' => "This one surprised us with how often it came up. A rate gets agreed verbally at the time of the deal, the deal takes months to close with payments coming in stages, and by the time the commission is due, there's no record either side fully agrees on. Two builders told us they'd lost brokers over disputes that were really just about nobody writing anything down."],
                    ['type' => 'heading', 'text' => "4. The real numbers live in someone's head, not on a screen"],
                    ['type' => 'paragraph', 'text' => "Ask a builder how much has actually been collected this month, across all units, and a lot of them need to call their accountant or flip through a register to answer. Not because the information doesn't exist — it's usually written down somewhere — but because it's scattered across a notebook, a WhatsApp chat with the site manager, and three different Excel files that don't talk to each other."],
                    ['type' => 'list', 'items' => [
                        "Unit availability updates the moment a token is recorded, for everyone, not just the person who took it — no more double bookings",
                        "Payment reminders go out automatically and neutrally from day one, so the awkward call only happens for the few customers who genuinely need one",
                        "Commission rates and payouts are logged against the deal itself, so there's a record both sides can check instead of arguing from memory",
                        "Collections, dues and unit status sit on one dashboard, so the answer to \"where do we stand\" doesn't require a phone call",
                    ]],
                    ['type' => 'heading', 'text' => "5. Everything runs fine — as long as the owner is personally watching it"],
                    ['type' => 'paragraph', 'text' => "This was the one that tied all the others together. Every system we saw worked reasonably well when the owner was actively checking on it — calling the site office, reviewing the register, chasing the accountant. The moment the owner was travelling, or busy with a new project, or just took a week off, things started slipping through. That's really the gap we built this CRM to close: not to replace the owner's judgment, but to make sure the basic facts — what's booked, what's due, what's owed to whom — stay correct and visible whether or not anyone's personally chasing them that day."],
                ],
            ],
        ];
    }
};
