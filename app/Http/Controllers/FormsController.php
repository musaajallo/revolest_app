<?php

namespace App\Http\Controllers;

use App\Models\BuiltPropertyListingLead;
use App\Models\CustomerFeedback;
use App\Models\LandPurchaseLead;
use App\Models\LandSaleLead;
use App\Models\PetApplication;
use App\Models\RentalConsultation;
use App\Models\RepairRequest;
use App\Support\HardensPublicForms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormsController extends Controller
{
    use HardensPublicForms;

    public function index()
    {
        return view('public.forms.index');
    }

    public function thankYou(string $type)
    {
        $copy = match ($type) {
            'land-purchase' => 'Your land purchase consultation request has been received. An agent will contact you shortly.',
            'land-sale' => 'Your land listing request has been received. An agent will reach out to schedule a site visit.',
            'rental-consultation' => 'Your rental consultation has been received. We will be in touch with matching properties.',
            'property-listing' => 'Your property listing request has been received. An agent will reach out to schedule a site visit.',
            'customer-feedback' => 'Thank you for your feedback. Your responses help us improve our service.',
            'maintenance-request' => 'Your maintenance request has been received. We will follow up to schedule the repair.',
            'pet-application' => 'Your pet application has been received. We will review it and respond shortly.',
            default => 'Thank you. Your submission has been received.',
        };

        return view('public.forms.thank-you', ['copy' => $copy]);
    }

    public function landPurchase()
    {
        return view('public.forms.land-purchase');
    }

    public function storeLandPurchase(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['full_name', 'phone', 'email']);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
            'identification_type' => 'nullable|string|max:100',
            'identification_number' => 'nullable|string|max:100',
            'id_attached' => 'nullable|boolean',
            'preferred_locations' => 'nullable|string|max:500',
            'plot_size' => 'nullable|string|max:100',
            'with_buildings' => 'nullable|string|max:255',
            'future_development' => 'nullable|boolean',
            'land_type' => 'nullable|string|max:50',
            'budget' => 'nullable|string|max:100',
            'payment_plan' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'timeframe' => 'nullable|string|max:100',
            'completion_target' => 'nullable|string|max:100',
            'special_requirements' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
            'details' => 'nullable|array',
            'signed_name' => 'required|string|max:255',
            'agree_terms' => 'accepted',
        ]);

        LandPurchaseLead::create($this->stamp($request, $data));

        return redirect()->route('forms.thank-you', ['type' => 'land-purchase']);
    }

    public function landSale()
    {
        return view('public.forms.land-sale');
    }

    public function storeLandSale(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['full_name', 'phone_primary', 'email']);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'phone_tertiary' => 'nullable|string|max:50',
            'current_address' => 'nullable|string|max:255',
            'land_location' => 'nullable|string|max:1000',
            'land_size' => 'nullable|string|max:100',
            'current_use' => 'nullable|string|max:50',
            'current_use_other' => 'nullable|string|max:255',
            'jointly_owned' => 'nullable|boolean',
            'ownership_disputes' => 'nullable|boolean',
            'zoning' => 'nullable|string|max:50',
            'asking_price' => 'nullable|numeric|min:0',
            'consultation_purpose' => 'nullable|array',
            'consultation_purpose.*' => 'string|max:50',
            'consultation_purpose_other' => 'nullable|string|max:255',
            'plans_for_land' => 'nullable|string|max:5000',
            'current_issues' => 'nullable|string|max:5000',
            'has_liens' => 'nullable|boolean',
            'taxes_up_to_date' => 'nullable|boolean',
            'has_legal_documents' => 'nullable|boolean',
            'documents_provided' => 'nullable|array',
            'documents_provided.*' => 'string|max:50',
            'free_from_disputes' => 'nullable|boolean',
            'utilities' => 'nullable|array',
            'utilities.*' => 'string|max:50',
            'road_accessible' => 'nullable|boolean',
            'existing_structures' => 'nullable|string|max:5000',
            'environmental_concerns' => 'nullable|string|max:5000',
            'has_recent_survey' => 'nullable|boolean',
            'land_history' => 'nullable|string|max:5000',
            'referral_source' => 'nullable|string|max:255',
            'referral_notes' => 'nullable|string|max:255',
            'previous_company_contact' => 'nullable|boolean',
            'previous_company_experience' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
            'signed_name' => 'required|string|max:255',
            'agree_terms' => 'accepted',
        ]);

        LandSaleLead::create($this->stamp($request, $data));

        return redirect()->route('forms.thank-you', ['type' => 'land-sale']);
    }

    public function rentalConsultation()
    {
        return view('public.forms.rental-consultation');
    }

    public function storeRentalConsultation(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['full_name', 'phone', 'email']);

        $data = $request->validate([
            'consultation_date' => 'nullable|date',
            'full_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'institution' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'number_of_kids' => 'nullable|integer|min:0|max:50',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'preferred_areas' => 'nullable|string|max:255',
            'property_kind' => 'nullable|in:full_compound,apartment',
            'bedrooms' => 'nullable|integer|min:0|max:20',
            'furnished' => 'nullable|boolean',
            'preferred_structure' => 'nullable|string|max:255',
            'desired_facilities' => 'nullable|string|max:5000',
            'property_suggestions' => 'nullable|string|max:5000',
            'reason_for_moving' => 'nullable|string|max:5000',
            'occupants_count' => 'nullable|integer|min:1|max:50',
            'move_in_window' => 'nullable|string|max:100',
            'rental_duration' => 'nullable|string|max:100',
            'payment_plan' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer,cheque',
            'payer' => 'nullable|in:me,other',
            'payer_name' => 'nullable|string|max:255',
            'payer_occupation' => 'nullable|string|max:255',
            'payer_address' => 'nullable|string|max:255',
            'payer_phone' => 'nullable|string|max:50',
            'payer_relationship' => 'nullable|string|max:100',
            'previous_company_contact' => 'nullable|boolean',
            'previous_company_experience' => 'nullable|string|max:5000',
            'referral_source' => 'nullable|string|max:255',
            'referral_name' => 'nullable|string|max:255',
            'signed_name' => 'required|string|max:255',
            'agree_terms' => 'accepted',
        ]);

        RentalConsultation::create($this->stamp($request, $data));

        return redirect()->route('forms.thank-you', ['type' => 'rental-consultation']);
    }

    public function builtPropertyListing()
    {
        return view('public.forms.property-listing');
    }

    public function storeBuiltPropertyListing(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['first_name', 'last_name', 'phone', 'property_address']);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'legal_description' => 'nullable|string|max:5000',
            'property_address' => 'required|string|max:255',
            'land_dimension' => 'nullable|string|max:100',
            'approximate_sqft' => 'nullable|string|max:50',
            'property_status' => 'nullable|in:freehold,leasehold',
            'property_type' => 'nullable|string|max:50',
            'buildings_on_property' => 'nullable|array',
            'buildings_on_property.*' => 'string|max:50',
            'asking_price' => 'nullable|numeric|min:0',
            'possession' => 'nullable|string|max:50',
            'showing_instructions' => 'nullable|string|max:50',
            'number_of_rooms' => 'nullable|integer|min:0|max:200',
            'bedrooms_detail' => 'nullable|string|max:255',
            'bathrooms_detail' => 'nullable|string|max:255',
            'age_of_house' => 'nullable|string|max:100',
            'square_footage' => 'nullable|string|max:100',
            'roof_type' => 'nullable|string|max:100',
            'furnace' => 'nullable|string|max:100',
            'amenities' => 'nullable|string|max:5000',
            'natural_features' => 'nullable|array',
            'natural_features.*' => 'string|max:50',
            'site_documents' => 'nullable|array',
            'site_documents.*' => 'string|max:50',
            'disclosures' => 'nullable|array',
            'disclosures.*' => 'string|max:50',
            'disclosures_other' => 'nullable|string|max:5000',
            'documents_attached' => 'nullable|array',
            'documents_attached.*' => 'string|max:50',
            'referral_source' => 'nullable|string|max:255',
            'referral_name' => 'nullable|string|max:255',
            'previous_company_contact' => 'nullable|boolean',
            'previous_company_experience' => 'nullable|string|max:5000',
            'signed_name' => 'required|string|max:255',
            'agree_terms' => 'accepted',
        ]);

        BuiltPropertyListingLead::create($this->stamp($request, $data));

        return redirect()->route('forms.thank-you', ['type' => 'property-listing']);
    }

    public function customerFeedback()
    {
        return view('public.forms.customer-feedback');
    }

    public function storeCustomerFeedback(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['full_name', 'email', 'overall_satisfaction', 'additional_comments']);

        $satisfaction = ['very_satisfied', 'satisfied', 'neutral', 'dissatisfied', 'very_dissatisfied'];
        $quality = ['excellent', 'good', 'average', 'poor', 'very_poor'];
        $yesNoSomewhat = ['yes', 'no', 'somewhat'];
        $easeOfFinding = ['very_easy', 'easy', 'neutral', 'difficult', 'very_difficult'];
        $recommend = ['definitely', 'maybe', 'not_likely'];
        $score = ['1_3', '4_6', '7_10'];
        $expectations = ['yes', 'no', 'exceed'];
        $heard = ['word_of_mouth', 'social_media', 'online_ad', 'friend_family', 'other'];

        $data = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'overall_satisfaction' => ['nullable', 'in:'.implode(',', $satisfaction)],
            'service_quality' => ['nullable', 'in:'.implode(',', $quality)],
            'customer_service_experience' => ['nullable', 'in:'.implode(',', $quality)],
            'staff_helpful' => ['nullable', 'in:'.implode(',', $yesNoSomewhat)],
            'delivery_on_time' => ['nullable', 'in:'.implode(',', $yesNoSomewhat)],
            'ease_of_finding' => ['nullable', 'in:'.implode(',', $easeOfFinding)],
            'would_recommend' => ['nullable', 'in:'.implode(',', $recommend)],
            'accessibility_score' => ['nullable', 'in:'.implode(',', $score)],
            'expectations_met' => ['nullable', 'in:'.implode(',', $expectations)],
            'brand_score' => ['nullable', 'in:'.implode(',', $score)],
            'heard_about_us' => ['nullable', 'in:'.implode(',', $heard)],
            'heard_about_us_other' => 'nullable|string|max:255',
            'improvement_suggestions' => 'nullable|string|max:5000',
            'additional_comments' => 'nullable|string|max:5000',
            'why_chose_us' => 'nullable|string|max:5000',
            'missing_features' => 'nullable|string|max:5000',
            'signed_name' => 'nullable|string|max:255',
        ]);

        $data['status'] = 'new';
        $data['signed_at'] = filled($data['signed_name'] ?? null) ? now() : null;
        $data['submitted_at'] = now();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        CustomerFeedback::create($data);

        return redirect()->route('forms.thank-you', ['type' => 'customer-feedback']);
    }

    public function maintenanceRequest()
    {
        return view('public.forms.maintenance-request');
    }

    public function storeMaintenanceRequest(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['first_name', 'last_name', 'phone', 'property_address', 'description']);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'property_address' => 'required|string|max:255',
            'apartment_number' => 'nullable|string|max:100',
            'description' => 'required|string|max:5000',
            'priority' => ['nullable', 'in:'.implode(',', RepairRequest::PRIORITIES)],
            'category' => 'nullable|string|max:100',
            'preferred_visit' => ['nullable', 'in:home,anytime,call_to_confirm,fix_appointment'],
            'has_pets' => 'nullable|boolean',
            'pet_notes' => 'nullable|string|max:1000',
            'permission_to_enter' => 'accepted',
            'tenant_signature_name' => 'required|string|max:255',
        ]);

        $data['status'] = 'new';
        $data['signed_at'] = now();
        $data['submitted_at'] = now();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        $user = Auth::user();
        if ($user && $user->tenant) {
            $data['tenant_id'] = $user->tenant->id;
        }

        RepairRequest::create($data);

        return redirect()->route('forms.thank-you', ['type' => 'maintenance-request']);
    }

    public function petApplication()
    {
        return view('public.forms.pet-application');
    }

    public function storePetApplication(Request $request)
    {
        $this->applyPublicFormHardening($request, signature: ['tenant_name', 'phone', 'property_address']);

        $data = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'property_address' => 'required|string|max:255',
            'lease_start_date' => 'nullable|date',
            'pets' => 'required|array|min:1|max:5',
            'pets.*.name' => 'nullable|string|max:100',
            'pets.*.type' => 'required|string|max:50',
            'pets.*.breed' => 'nullable|string|max:100',
            'pets.*.age' => 'nullable|string|max:50',
            'pets.*.weight' => 'nullable|string|max:50',
            'pets.*.spayed_neutered' => 'nullable|boolean',
            'pets.*.house_trained' => 'nullable|boolean',
            'pets.*.vaccinations_up_to_date' => 'nullable|boolean',
            'pets.*.aggression_history' => 'nullable|boolean',
            'pets.*.aggression_notes' => 'nullable|string|max:1000',
            'pets.*.special_medical_needs' => 'nullable|boolean',
            'pets.*.medical_notes' => 'nullable|string|max:1000',
            'keep_location' => ['nullable', 'in:'.implode(',', PetApplication::KEEP_LOCATIONS)],
            'supervised_outdoors' => 'nullable|boolean',
            'past_complaints' => 'nullable|boolean',
            'past_complaints_notes' => 'nullable|string|max:2000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'signed_name' => 'required|string|max:255',
            'agree_terms' => 'accepted',
        ]);

        unset($data['agree_terms']);

        // Normalize pets — fill missing booleans as false rather than null
        $data['pets'] = array_map(function (array $pet): array {
            foreach (['spayed_neutered', 'house_trained', 'vaccinations_up_to_date', 'aggression_history', 'special_medical_needs'] as $flag) {
                $pet[$flag] = (bool) ($pet[$flag] ?? false);
            }

            return $pet;
        }, $data['pets']);

        $data['status'] = 'new';
        $data['signed_at'] = now();
        $data['submitted_at'] = now();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        $user = Auth::user();
        if ($user && $user->tenant) {
            $data['tenant_id'] = $user->tenant->id;
        }

        PetApplication::create($data);

        return redirect()->route('forms.thank-you', ['type' => 'pet-application']);
    }

    private function stamp(Request $request, array $data): array
    {
        unset($data['agree_terms']);
        $data['status'] = 'new';
        $data['signed_at'] = now();
        $data['submitted_at'] = now();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        return $data;
    }
}
