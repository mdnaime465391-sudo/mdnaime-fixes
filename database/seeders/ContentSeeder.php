<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seeds default content (settings, about page, services, testimonials)
 * only when the relevant table is empty. Never deletes, truncates or
 * overwrites existing data, and never touches inquiries.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Setting::query()->count() === 0) {
            Setting::create([
                'site_title' => 'Md Naime',
                'tagline' => 'Premium med spa websites & the Laravel systems behind them.',
                'availability_label' => 'Open for Q2 2026 projects',
                'location' => 'Dhaka, Bangladesh',
                'seo_title' => 'Md Naime — UI/UX Designer & Web Developer, Dhaka',
                'seo_description' => 'Med spa sites that book. Systems that run the clinic. Figma → frontend → Laravel, delivered by one person.',
                'email' => 'mdnaime4657@gmail.com',
                'phone' => '+880 1739 387922',
                'social_links' => [
                    ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/'],
                    ['label' => 'Dribbble', 'url' => 'https://dribbble.com/'],
                    ['label' => 'GitHub', 'url' => 'https://github.com/'],
                ],
                'call_booking_url' => null,
            ]);
            $this->command?->info('Default settings created.');
        } else {
            $this->command?->line('Settings already exist — skipped.');
        }

        if (AboutPage::query()->count() === 0) {
            AboutPage::create([
                'eyebrow' => 'ABOUT',
                'headline' => 'I design the interface and build the system behind it.',
                'intro' => 'One person, the full pipeline: Figma UI/UX, frontend and the Laravel application behind it.',
                'story_heading' => 'A designer who ships, a developer who cares about the room.',
                'story' => '<p>I am Md Naime, a UI/UX designer and web developer in Dhaka. I work with med spas, clinics and service businesses that need a site to feel as considered as the experience they sell — and a system behind it that removes the admin work.</p><p>Most studios hand the Figma file to someone else. Here the same person designs the interface and writes the Laravel behind it, which is why the details survive the handoff.</p>',
                'principles' => [
                    ['title' => 'Clarity before decoration', 'body' => 'Every screen has a job. If a detail does not help a patient book or a team member work faster, it does not ship.'],
                    ['title' => 'One pipeline, one owner', 'body' => 'Design and build stay in the same hands, so nothing is lost between Figma, frontend and Laravel.'],
                    ['title' => 'Built to be handed over', 'body' => 'Clean structure, a real admin panel and a short walkthrough. Your team can run it without me.'],
                ],
                'toolkit' => config('portfolio.about_defaults.toolkit'),
                'availability_label' => 'Available for new projects',
                'show_story' => true,
                'show_principles' => true,
                'show_toolkit' => true,
                'show_testimonials' => true,
                'show_cta' => true,
                'seo_title' => 'About Md Naime — UI/UX Designer & Laravel Developer',
                'seo_description' => 'Md Naime designs premium clinic websites and builds the Laravel systems behind them — Figma, frontend and backend, end to end.',
            ]);
            $this->command?->info('Default about page created.');
        } else {
            $this->command?->line('About page already exists — skipped.');
        }

        if (Service::query()->count() === 0) {
            Service::insert([
                [
                    'title' => 'Med Spa Website Design',
                    'slug' => 'med-spa-website-design',
                    'short_description' => 'Premium clinic websites that build patient trust and turn visits into consultation bookings.',
                    'long_description' => '<p>A med spa site has one job: make the right patient feel confident enough to book. I design around trust — clear treatment presentation, calm premium visuals, and a booking path with zero friction — then build it on WordPress/Elementor so your team can edit everything.</p>',
                    'icon' => 'spa',
                    'features' => json_encode([
                        'Trust-first design that reflects the in-room experience',
                        'Treatment pages written and structured for clarity',
                        'Booking flow optimised for consultation requests',
                        'WordPress/Elementor build your team can maintain',
                    ]),
                    'sort_order' => 0,
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Booking Systems & Custom Web Apps',
                    'slug' => 'booking-systems-web-apps',
                    'short_description' => 'Laravel web apps that simplify operations: scheduling, client portals, admin dashboards.',
                    'long_description' => '<p>When spreadsheets and off-the-shelf tools stop scaling, I design and build the system your business actually needs: online booking, appointment scheduling, client portals, staff and service management, and admin dashboards — shipped as a custom Laravel application.</p>',
                    'icon' => 'layout',
                    'features' => json_encode([
                        'Online booking & appointment scheduling',
                        'Client portals & staff dashboards',
                        'Service/staff management & reporting',
                        'Custom business apps on Laravel + MySQL',
                    ]),
                    'sort_order' => 1,
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            $this->command?->info('Default services created.');
        } else {
            $this->command?->line('Services already exist — skipped.');
        }

        if (Testimonial::query()->count() === 0) {
            Testimonial::insert([
                [
                    'name' => 'Dr. Ayesha Rahman', 'role' => 'Founder', 'company' => 'Lumière Aesthetics',
                    'quote' => 'Naime understood our clinic better than agencies twice the price. The new site finally feels like the experience we give in the treatment room — and consults come in through it every week.',
                    'sort_order' => 0, 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
                ],
                [
                    'name' => 'Marcus Feld', 'role' => 'Operations Director', 'company' => 'Serenity Wellness Group',
                    'quote' => 'He replaced three disconnected tools with one booking system our front desk actually enjoys using. Setup was calm, the handover was clean, and nothing broke.',
                    'sort_order' => 1, 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
                ],
                [
                    'name' => 'Priya Nair', 'role' => 'Marketing Lead', 'company' => 'Radiance Dermatology',
                    'quote' => 'Rare to find one person who can design the brand experience and then write the Laravel behind it. We briefed once and got the whole pipeline back.',
                    'sort_order' => 2, 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
                ],
                [
                    'name' => 'Tomas Lindqvist', 'role' => 'Co-founder', 'company' => 'BookFlow',
                    'quote' => 'Our client portal went from a rough Figma file to a working product in weeks. The interface is calm, the edge cases are handled, and our users stopped emailing support.',
                    'sort_order' => 3, 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
                ],
            ]);
            $this->command?->info('Default testimonials created.');
        } else {
            $this->command?->line('Testimonials already exist — skipped.');
        }
    }
}
