<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class FormPolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            [
                'key' => 'policy.land_purchase',
                'label' => 'Land Purchase Policy',
                'order' => 10,
                'value' => <<<'MD'
**Policies and Fees Agreement**

By submitting this form, I acknowledge that I have read and understood the policies and fees associated with my dealings with the company. This agreement remains in effect until further notice.

1. The company charges a commission of **10% on freehold properties** and **5% on leasehold lands** upon completion of the sale.
2. **Consultation Fee:** the consultation fee is non-refundable. An additional site visit fee will apply for each plot of land viewed after the initial consultation.
3. **Plot Viewings:** after paying the consultation fee, the client will be shown three plots within the requested budget range. Subsequent site visits will incur a fee between **D400 and D700**, depending on distance.
4. **Property Documentation:** the percentage of the commission fee will be deducted from the sales amount.
MD,
            ],
            [
                'key' => 'policy.land_sale',
                'label' => 'Land Sale / Listing Policy',
                'order' => 20,
                'value' => <<<'MD'
**Policies and Fees Agreement**

1. **Consultation Fee:** the consultation fee for listing is **D1,500** (one thousand, five hundred dalasi), which is non-refundable.
2. **Property Viewings:** after paying the consultation fee, the company will arrange a site visit to evaluate the property.
3. **Commission Charges for Property Sale:**
   - **Leasehold Properties:** 5% commission on the sale price
   - **Freehold Properties:** 10% commission on the sale price
MD,
            ],
            [
                'key' => 'policy.rental_consultation',
                'label' => 'Rental Consultation Policy',
                'order' => 30,
                'value' => <<<'MD'
**Policies and Fees Agreement**

1. **Consultation Fee:** D700, non-refundable. An additional site visit fee of **D500** will apply for each property viewed after the initial consultation.
2. **Property Viewings:** after paying the consultation fee, the client will be shown three properties within budget. Any subsequent site visits will incur a fee between **D400 and D700** depending on distance.
3. **Agent Fee:** non-refundable unless the company is at fault. The company will withhold any payable agent fees until the transaction is completed.
4. **Property Documentation:** the agent fee must be paid with the first rent payment. Documentation will not be processed and the property will not be accessible until the agent fee is paid.
5. **Tenancy Agreement:** keys will only be released after the tenancy agreement is signed.
6. **Payment and Key Release:** all payments must be completed (or an instalment agreement reached with the landlord/landlady) before keys are handed over.
7. **Furnished Property Withdrawal:** if the client withdraws after booking a furnished property, a penalty of **50% of the booking amount** applies.
8. **Furnished Property Booking Fee:** the booking fee for furnished properties is **50% of the rent price**.
9. **Security Deposit:** for short-term furnished properties, **D5,000**.
10. **Deposit Withdrawal Penalty:** if the client withdraws within seven days of making a deposit, **5% of the deposit will be withheld**. The percentage withheld increases the longer the property is held.
11. **Fee Disclosure:** all applicable fees are clearly communicated during consultation, and the client accepts the terms in full.
MD,
            ],
            [
                'key' => 'policy.rental_weekly_agent_fees',
                'label' => 'Rental — Weekly Agent Fees',
                'order' => 31,
                'value' => <<<'MD'
**Furnished property weekly agent fees**

| Stay length | Agent fee |
|---|---|
| 3 days – 1 week | D2,000 |
| 8 days – 2 weeks | D2,800 |
| 16 days – 3 weeks | D3,500 |
| 22 days – 4 weeks | D5,000 |
| 6 – 8 weeks | D6,500 |
| 10 – 14 weeks | D7,500 |
| 15 – 20 weeks | D8,500 |
| 21 – 25 weeks | D10,000 |
MD,
            ],
            [
                'key' => 'policy.rental_yearly_agent_fees',
                'label' => 'Rental — Yearly Agent Fees',
                'order' => 32,
                'value' => <<<'MD'
**Yearly agent fee bands**

| Yearly rent | Agent fee |
|---|---|
| D30,000 – D95,000 | D5,000 |
| D100,000 – D150,000 | D8,000 |
| D160,000 – D195,000 | D10,000 |
| D200,000 – D295,000 | D15,000 |
| D300,000 – D375,000 | D20,000 |
| D380,000 – D450,000 | D25,000 |
| D460,000 – D525,000 | D30,000 |
| D530,000 – D630,000 | D35,000 |
| D635,000 – D750,000 | D45,000 |
| D760,000 – D1,500,000 | D50,000 |
MD,
            ],
            [
                'key' => 'policy.pet_application',
                'label' => 'Pet Application Agreement',
                'order' => 50,
                'value' => <<<'MD'
**Pet Owner Agreement**

By submitting this application, I acknowledge and agree to the following:

1. **Pet Deposit / Fees:** I understand a pet deposit (per the lease agreement) is required, in addition to any applicable pet fees. This deposit is refundable subject to damages caused by the pet.
2. **Pet Behavior:** I agree to ensure my pet does not cause disturbance or damage to the property — including excessive barking, scratching, or other disruptive behavior.
3. **Insurance / Legal Requirements:** I will maintain renter's insurance covering damage or injury caused by my pet, and comply with all local laws and regulations regarding pets.
4. **Health and Safety:** I will provide up-to-date vaccination records for my pet and ensure it is in good health. I will keep the property owner informed of any required medical treatment.
5. **Liability:** I am responsible for all costs associated with damages caused by my pet(s) and agree to repair or compensate for any damages — to the property, furnishings, or common areas.
6. **Pet Removal:** If the pet becomes disruptive, aggressive, or causes damage, the landlord reserves the right to require the pet be removed from the property.
7. **Lease Terms:** This pet application is subject to approval and may become part of the lease agreement.
MD,
            ],
            [
                'key' => 'policy.purchase_build',
                'label' => 'Built Property Purchase Policy',
                'order' => 15,
                'value' => <<<'MD'
**Policies and Fees Agreement**

By submitting this form, I authorize SA Property and M&T Global Construction Group to review the details provided and contact me for further discussions regarding the purchase of a build property. I understand that this form is a request for services and does not constitute a binding agreement.

1. **Consultation Fee:** the consultation fee is **D500 (five hundred dalasi)**, non-refundable.
2. **Property Viewings:** after paying the consultation fee, the company will arrange for a site visit to view the properties within my budget.
3. **Commission Charges for Property Sale:**
   - **Leasehold Properties:** 5% commission on the sale price
   - **Freehold Properties:** 10% commission on the sale price
MD,
            ],
            [
                'key' => 'policy.built_property_listing',
                'label' => 'Built Property Listing Policy',
                'order' => 40,
                'value' => <<<'MD'
**Policies and Fees Agreement**

1. **Consultation Fee:** the consultation fee for listing is **D1,500** (one thousand, five hundred dalasi), non-refundable.
2. **Property Viewings:** after paying the consultation fee, the company will arrange a site visit to evaluate the property.
3. **Commission Charges:**
   - **Leasehold Properties:** 5% commission on the sale price
   - **Freehold Properties:** 10% commission on the sale price
MD,
            ],
        ];

        foreach ($policies as $policy) {
            Setting::updateOrCreate(
                ['key' => $policy['key']],
                [
                    'group' => 'policies',
                    'key' => $policy['key'],
                    'value' => $policy['value'],
                    'type' => 'textarea',
                    'label' => $policy['label'],
                    'description' => 'Markdown content shown on the public form. Edit to update without a redeploy.',
                    'order' => $policy['order'],
                ]
            );
        }
    }
}
