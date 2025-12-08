<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => [
                    'hero_title' => 'Find Your Perfect Property',
                    'hero_subtitle' => 'Discover thousands of properties for sale and rent. Your dream home is just a click away.',
                    'stats_clients' => '500+',
                    'why_choose_title' => 'Why Choose SÀ Property?',
                    'why_choose_subtitle' => 'We make finding your perfect property simple, transparent, and stress-free.',
                    'feature_1_title' => 'Trusted & Verified',
                    'feature_1_description' => 'All our properties and agents are thoroughly verified to ensure you get the best experience.',
                    'feature_2_title' => 'Best Prices',
                    'feature_2_description' => 'We offer competitive pricing and help you find properties that fit your budget.',
                    'feature_3_title' => '24/7 Support',
                    'feature_3_description' => 'Our dedicated team is always ready to assist you with any questions or concerns.',
                    'cta_title' => 'Ready to Find Your Dream Home?',
                    'cta_subtitle' => 'Whether you\'re buying, selling, or renting, we\'re here to help you every step of the way.',
                ],
                'meta_title' => 'SÀ Property - Find Your Dream Home',
                'meta_description' => 'Find your dream property with SÀ Property. Browse homes for sale and rent, connect with trusted agents, and make your real estate journey seamless.',
                'is_published' => true,
            ],
            [
                'title' => 'Properties',
                'slug' => 'properties',
                'content' => [
                    'page_title' => 'Properties',
                    'page_subtitle' => 'Find your perfect property from our extensive listings',
                ],
                'meta_title' => 'Browse Properties - SÀ Property',
                'meta_description' => 'Browse our extensive collection of properties for sale and rent. Find houses, apartments, condos, and more.',
                'is_published' => true,
            ],
            [
                'title' => 'Agents',
                'slug' => 'agents',
                'content' => [
                    'page_title' => 'Our Agents',
                    'page_subtitle' => 'Meet our professional and dedicated real estate agents',
                    'cta_title' => 'Become an Agent',
                    'cta_description' => 'Are you a licensed real estate professional? Join our team and connect with clients looking for their dream properties.',
                ],
                'meta_title' => 'Our Agents - SÀ Property',
                'meta_description' => 'Meet our professional real estate agents. Our experienced team is ready to help you find your perfect property.',
                'is_published' => true,
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'content' => [
                    'page_title' => 'Contact Us',
                    'page_subtitle' => 'We\'d love to hear from you. Get in touch with our team.',
                    'form_title' => 'Send Us a Message',
                    'form_subtitle' => 'Fill out the form below and we\'ll get back to you as soon as possible.',
                    'office_address' => '123 Property Street, Real Estate City, RC 12345',
                    'email_1' => 'info@saproperty.com',
                    'email_2' => 'support@saproperty.com',
                    'phone_1' => '+1 (555) 123-4567',
                    'phone_2' => '+1 (555) 987-6543',
                    'hours_weekday' => 'Mon - Fri: 9:00 AM - 6:00 PM',
                    'hours_saturday' => 'Sat: 10:00 AM - 4:00 PM',
                    'hours_sunday' => 'Sun: Closed',
                ],
                'meta_title' => 'Contact Us - SÀ Property',
                'meta_description' => 'Get in touch with SÀ Property. We\'re here to help with all your real estate needs.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}
