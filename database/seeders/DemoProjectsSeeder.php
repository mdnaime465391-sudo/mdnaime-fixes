<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Seeds the 9 sample projects that mirror the frontend mock data
 * (assets/js/data.js). Contains placeholder .example live-preview URLs
 * and fake demo credentials — swap them in the project editor.
 *
 * Guard: skipped in production unless SEED_DEMO=true.
 * Idempotent: uses firstOrCreate on slug, so user edits are preserved.
 */
class DemoProjectsSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && ! config('portfolio.seed.demo')) {
            $this->command?->line('Demo projects skipped (production). Set SEED_DEMO=true in .env to seed the sample projects.');

            return;
        }

        $p = fn (array $attrs) => array_merge([
            'categories' => [],
            'features' => [],
            'tech_stack' => [],
            'is_published' => true,
        ], $attrs);

        $rows = [
            $p([
                'title' => 'Lumière Aesthetics', 'slug' => 'lumiere-aesthetics', 'sort_order' => 0,
                'categories' => ['Med Spa Websites', 'Elementor/WordPress'],
                'summary' => 'A premium clinic site that turned browses into booked consultations.',
                'background' => '<p>Lumière Aesthetics is a boutique med spa whose old site undersold the treatment-room experience. Visitors browsed, then phoned — or left.</p>',
                'challenge' => '<p>Present eight treatment lines without overwhelming visitors, establish medical-grade trust on first scroll, and make the booking path obvious on every screen.</p>',
                'design_approach' => '<p>Editorial, calm layout with generous whitespace; treatments presented as clear scannable cards; a persistent "Book consultation" path; testimonials and credentials placed at decision points.</p>',
                'features' => ['Treatment catalogue with detail pages', 'Persistent booking CTA', 'Before/after gallery', 'Team credentials section'],
                'tech_stack' => ['Figma', 'WordPress', 'Elementor'],
                'solution' => '<p>A full Figma redesign, then a clean Elementor build the clinic team edits themselves.</p>',
                'result' => '<p>Consultation requests now arrive through the site every week; the clinic stopped paying for a third-party booking plugin.</p>',
            ]),
            $p([
                'title' => 'Radiance Dermatology', 'slug' => 'radiance-dermatology', 'sort_order' => 1,
                'categories' => ['Med Spa Websites'],
                'summary' => 'A dermatology clinic site built to convert research-heavy patients.',
                'background' => '<p>Radiance treats high-consideration procedures — patients research for weeks before booking. The old site had no structure for that journey.</p>',
                'challenge' => '<p>Answer clinical questions without jargon, differentiate 20+ services, and route visitors to the right treatment fast.</p>',
                'design_approach' => '<p>Condition-first navigation, comparison-friendly service pages, strong authority signals (doctor bios, certifications) above the fold.</p>',
                'features' => ['Condition-based navigation', 'Service comparison blocks', 'Doctor bios & credentials', 'SEO-ready service pages'],
                'tech_stack' => ['Figma', 'WordPress', 'Elementor'],
                'solution' => '<p>A restructured information architecture with a conversion-focused page template per treatment.</p>',
                'result' => '<p>Longer sessions, deeper pages per visit, and a steady rise in form-submitted consultation requests.</p>',
            ]),
            $p([
                'title' => 'Serenity Wellness Group', 'slug' => 'serenity-wellness-group', 'sort_order' => 2,
                'categories' => ['Elementor/WordPress'],
                'summary' => 'A multi-location wellness brand site with one calm, unified identity.',
                'background' => '<p>Serenity runs several wellness locations, each with a different look — the brand felt fragmented online.</p>',
                'challenge' => '<p>Unify locations under one design language while keeping per-location services and schedules clear.</p>',
                'design_approach' => '<p>One design system, location-aware components, and a shared booking entry point per service.</p>',
                'features' => ['Location switcher', 'Unified design system', 'Per-location schedules'],
                'tech_stack' => ['Figma', 'WordPress', 'Elementor'],
                'solution' => '<p>A componentised Elementor build with reusable patterns for every location page.</p>',
                'result' => '<p>One brand online; front-desk staff report fewer "which location is this?" calls.</p>',
            ]),
            $p([
                'title' => 'BookFlow', 'slug' => 'bookflow', 'sort_order' => 3,
                'categories' => ['Laravel Web Apps', 'Booking Systems'],
                'summary' => 'A custom Laravel booking app that replaced three disconnected tools.',
                'background' => '<p>BookFlow’s client ran scheduling, intake and payments across three tools that never agreed with each other.</p>',
                'challenge' => '<p>One system for staff and clients: real-time availability, conflict-free booking, and an admin view the front desk trusts.</p>',
                'design_approach' => '<p>Designed the full product in Figma first — booking wizard, staff calendar, admin dashboard — then built the Laravel backend and a Tailwind frontend.</p>',
                'features' => ['Real-time availability engine', 'Booking wizard with confirmations', 'Staff calendar & service management', 'Admin dashboard with reporting'],
                'tech_stack' => ['Figma', 'Laravel', 'MySQL', 'Tailwind CSS'],
                'solution' => '<p>A single Laravel application handling scheduling, clients and payments, with role-based portals for staff and clients.</p>',
                'result' => '<p>Three tools retired; double-bookings eliminated; the front desk runs the day from one screen.</p>',
                'live_preview_url' => 'https://bookflow.example.com',
                'demo_credentials' => 'admin@bookflow.example.com / password',
                'demo_notes' => 'Demo data resets nightly. Use the staff account to walk the booking flow end to end.',
            ]),
            $p([
                'title' => 'ClientVault Portal', 'slug' => 'clientvault-portal', 'sort_order' => 4,
                'categories' => ['Laravel Web Apps', 'Admin Dashboards'],
                'summary' => 'A secure client portal for documents, requests and project status.',
                'background' => '<p>An agency was emailing sensitive files and losing track of client requests across inboxes.</p>',
                'challenge' => '<p>A portal clients actually log into: secure document exchange, a clear request thread, and zero training required.</p>',
                'design_approach' => '<p>Calm, uncluttered interface; documents and requests front and centre; admin-side dashboards for the team.</p>',
                'features' => ['Secure document vault', 'Request threads with status', 'Client & admin roles', 'Activity log'],
                'tech_stack' => ['Figma', 'Laravel', 'MySQL'],
                'solution' => '<p>A Laravel portal with granular permissions, file versioning and an audit-friendly activity trail.</p>',
                'result' => '<p>Support email volume dropped sharply; every client request now lives in one traceable place.</p>',
                'live_preview_url' => 'https://clientvault.example.com',
                'demo_credentials' => 'demo@clientvault.example.com / password',
                'demo_notes' => 'Sandbox environment — uploads are cleared every 24 hours.',
            ]),
            $p([
                'title' => 'Glow Studio Design System', 'slug' => 'glow-studio-design-system', 'sort_order' => 5,
                'categories' => ['Figma UI/UX'],
                'summary' => 'A Figma design system that keeps a growing studio brand consistent.',
                'background' => '<p>Glow Studio’s marketing pages were drifting — every campaign reinvented buttons, type and spacing.</p>',
                'challenge' => '<p>Create a system non-designers can apply without breaking the brand.</p>',
                'design_approach' => '<p>Token-based Figma library: type scale, colour, spacing and component variants with clear usage rules.</p>',
                'features' => ['Token-based colour & type', 'Component variants', 'Usage documentation'],
                'tech_stack' => ['Figma'],
                'solution' => '<p>A documented library adopted across every new page since.</p>',
                'result' => '<p>New campaign pages ship in days, not weeks, and look like one brand.</p>',
            ]),
            $p([
                'title' => 'Aurelia Aesthetics', 'slug' => 'aurelia-aesthetics', 'sort_order' => 6,
                'categories' => ['Med Spa Websites', 'Elementor/WordPress'],
                'summary' => 'A quiet-luxury med spa site with a frictionless consultation path.',
                'background' => '<p>Aurelia positions on quiet luxury — but its website read generic.</p>',
                'challenge' => '<p>Express restraint and luxury in pixels, then convert that mood into booked consultations.</p>',
                'design_approach' => '<p>Muted palette, editorial serif type, slow reveals; consultation CTA placed at natural pause points.</p>',
                'features' => ['Editorial art direction', 'Treatment storytelling pages', 'Consultation request flow'],
                'tech_stack' => ['Figma', 'WordPress', 'Elementor'],
                'solution' => '<p>A design-led WordPress build that feels like the clinic’s interiors.</p>',
                'result' => '<p>Consultation requests through the site doubled within the first quarter.</p>',
            ]),
            $p([
                'title' => 'Pulse Scheduling', 'slug' => 'pulse-scheduling', 'sort_order' => 7,
                'categories' => ['Booking Systems', 'Laravel Web Apps'],
                'summary' => 'A staff-side scheduling dashboard for a busy multi-service clinic.',
                'background' => '<p>Pulse’s client managed 6 practitioners across 3 rooms on a whiteboard and a shared calendar.</p>',
                'challenge' => '<p>Room + practitioner scheduling without clashes, visible to staff and clients in real time.</p>',
                'design_approach' => '<p>Dashboard-first design: day/week views, drag-friendly slots, colour-coded rooms; a light client booking layer on top.</p>',
                'features' => ['Room & practitioner scheduling', 'Drag-friendly day/week views', 'Client self-booking', 'Reminder notifications'],
                'tech_stack' => ['Figma', 'Laravel', 'MySQL', 'Tailwind CSS'],
                'solution' => '<p>A Laravel scheduling core with a dashboard the whole team runs the clinic from.</p>',
                'result' => '<p>Clashes gone; whiteboard retired; no-shows down thanks to automated reminders.</p>',
                'live_preview_url' => 'https://pulse.example.com',
                'demo_credentials' => 'staff@pulse.example.com / password',
                'demo_notes' => 'Sandbox with fictional clinics and bookings.',
            ]),
            $p([
                'title' => 'Marble & Moss', 'slug' => 'marble-and-moss', 'sort_order' => 8,
                'categories' => ['Elementor/WordPress', 'Med Spa Websites'],
                'summary' => 'A salon and spa site that feels like the space it sells.',
                'background' => '<p>Marble & Moss invests in its interiors; its website flattened all of that into a template.</p>',
                'challenge' => '<p>Carry the tactile, botanical atmosphere online and make booking a service feel effortless.</p>',
                'design_approach' => '<p>Full-bleed imagery, generous type, a short menu-driven service list, booking links at every scroll depth.</p>',
                'features' => ['Atmosphere-led art direction', 'Menu-style service list', 'Embedded booking links'],
                'tech_stack' => ['Figma', 'WordPress', 'Elementor'],
                'solution' => '<p>A WordPress build that treats the site like a walkthrough of the space.</p>',
                'result' => '<p>Online bookings now account for the majority of new appointments.</p>',
            ]),
        ];

        foreach ($rows as $row) {
            // firstOrCreate goes through Eloquent casts — pass the arrays raw.
            Project::firstOrCreate(
                ['slug' => $row['slug']],
                $row
            );
        }

        $this->command?->info('Demo projects seeded (firstOrCreate — existing rows untouched).');
    }
}
