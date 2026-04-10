<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => 'Revolest Property',
                'type' => 'text',
                'label' => 'Site Name',
                'description' => 'The name of the website.',
                'order' => 1,
            ],
            [
                'group' => 'general',
                'key' => 'site_description',
                'value' => 'Find your dream property with Revolest. Browse homes for sale and rent, connect with trusted agents, and make your real estate journey seamless.',
                'type' => 'textarea',
                'label' => 'Site Description',
                'description' => 'A brief description of the site for SEO.',
                'order' => 2,
            ],
            [
                'group' => 'general',
                'key' => 'contact_email',
                'value' => 'info@revolest.com',
                'type' => 'text',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address.',
                'order' => 3,
            ],
            [
                'group' => 'general',
                'key' => 'contact_phone',
                'value' => '+220 123 4567',
                'type' => 'text',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number.',
                'order' => 4,
            ],
            [
                'group' => 'general',
                'key' => 'contact_address',
                'value' => "Kairaba Avenue\nSerrekunda, The Gambia",
                'type' => 'textarea',
                'label' => 'Contact Address',
                'description' => 'Physical office address.',
                'order' => 5,
            ],

            // Social Media
            [
                'group' => 'social',
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/revolest',
                'type' => 'text',
                'label' => 'Facebook URL',
                'description' => 'Link to the Facebook page.',
                'order' => 1,
            ],
            [
                'group' => 'social',
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/revolest',
                'type' => 'text',
                'label' => 'Twitter URL',
                'description' => 'Link to the Twitter/X profile.',
                'order' => 2,
            ],
            [
                'group' => 'social',
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/revolest',
                'type' => 'text',
                'label' => 'Instagram URL',
                'description' => 'Link to the Instagram profile.',
                'order' => 3,
            ],
            [
                'group' => 'social',
                'key' => 'linkedin_url',
                'value' => 'https://linkedin.com/company/revolest',
                'type' => 'text',
                'label' => 'LinkedIn URL',
                'description' => 'Link to the LinkedIn profile.',
                'order' => 4,
            ],
        ];

        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }
    }
}
