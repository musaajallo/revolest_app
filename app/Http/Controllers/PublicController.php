<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Inquiry;
use App\Models\Listing;
use App\Models\Page;
use App\Models\Property;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    private const PUBLIC_LISTING_STATUSES = ['for_rent', 'for_sale'];

    /**
     * Display the homepage
     */
    public function home()
    {
        $page = Page::findBySlug('home');

        $featuredProperties = Property::where('status', 'active')
            ->where('is_featured', true)
            ->where(function ($query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->with(['listings' => function ($query) {
                $query->whereIn('status', self::PUBLIC_LISTING_STATUSES)->latest();
            }])
            ->latest()
            ->take(10)
            ->get();

        $totalProperties = Property::count();
        $totalAgents = Agent::count();
        $totalListings = Listing::whereIn('status', self::PUBLIC_LISTING_STATUSES)->count();

        return view('public.home', compact(
            'page',
            'featuredProperties',
            'totalProperties',
            'totalAgents',
            'totalListings'
        ));
    }

    /**
     * Display all properties with search/filter
     */
    public function properties(Request $request)
    {
        $page = Page::findBySlug('properties');

        $query = Property::where(function ($propertyQuery) {
                $propertyQuery->where('status', 'active')
                    ->where(function ($availabilityQuery) {
                        $availabilityQuery->whereNull('available_from')
                            ->orWhere('available_from', '<=', now());
                    });
            })
            ->with(['listings' => function ($listingQuery) {
                $listingQuery->whereIn('status', self::PUBLIC_LISTING_STATUSES)->latest();
            }]);

        // Search by title or address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where(function ($priceQuery) use ($request) {
                $priceQuery
                    ->where(function ($saleQuery) use ($request) {
                        $saleQuery->where('purpose', 'sale')
                            ->where(function ($fieldQuery) use ($request) {
                                $fieldQuery->where('sale_price', '>=', $request->min_price)
                                    ->orWhere('price', '>=', $request->min_price);
                            });
                    })
                    ->orWhere(function ($rentQuery) use ($request) {
                        $rentQuery->where('purpose', 'rent')
                            ->where(function ($fieldQuery) use ($request) {
                                $fieldQuery->where('rental_price', '>=', $request->min_price)
                                    ->orWhere('price', '>=', $request->min_price);
                            });
                    })
                    ->orWhere(function ($mixedQuery) use ($request) {
                        $mixedQuery->where('purpose', 'mixed')
                            ->whereHas('listings', function ($listingQuery) use ($request) {
                                $listingQuery
                                    ->whereIn('status', self::PUBLIC_LISTING_STATUSES)
                                    ->where('price', '>=', $request->min_price);
                            });
                    });
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($priceQuery) use ($request) {
                $priceQuery
                    ->where(function ($saleQuery) use ($request) {
                        $saleQuery->where('purpose', 'sale')
                            ->where(function ($fieldQuery) use ($request) {
                                $fieldQuery->where('sale_price', '<=', $request->max_price)
                                    ->orWhere('price', '<=', $request->max_price);
                            });
                    })
                    ->orWhere(function ($rentQuery) use ($request) {
                        $rentQuery->where('purpose', 'rent')
                            ->where(function ($fieldQuery) use ($request) {
                                $fieldQuery->where('rental_price', '<=', $request->max_price)
                                    ->orWhere('price', '<=', $request->max_price);
                            });
                    })
                    ->orWhere(function ($mixedQuery) use ($request) {
                        $mixedQuery->where('purpose', 'mixed')
                            ->whereHas('listings', function ($listingQuery) use ($request) {
                                $listingQuery
                                    ->whereIn('status', self::PUBLIC_LISTING_STATUSES)
                                    ->where('price', '<=', $request->max_price);
                            });
                    });
            });
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where(function ($bedroomQuery) use ($request) {
                $bedroomQuery
                    ->where('bedrooms', '>=', $request->bedrooms)
                    ->orWhereHas('listings', function ($listingQuery) use ($request) {
                        $listingQuery
                            ->whereIn('status', self::PUBLIC_LISTING_STATUSES)
                            ->where('bedrooms', '>=', $request->bedrooms);
                    });
            });
        }

        $properties = $query->latest()->paginate(12);

        return view('public.properties.index', compact('properties', 'page'));
    }

    /**
     * Display a single property
     */
    public function propertyShow(Property $property)
    {
        $property->load([
            'owner',
            'listings' => function ($query) {
                $query->with('agent')->latest();
            },
        ]);

        $relatedProperties = Property::where(function ($propertyQuery) {
                $propertyQuery->where('status', 'active')
                    ->where(function ($availabilityQuery) {
                        $availabilityQuery->whereNull('available_from')
                            ->orWhere('available_from', '<=', now());
                    });
            })
            ->with(['listings' => function ($query) {
                $query->whereIn('status', self::PUBLIC_LISTING_STATUSES)->latest();
            }])
            ->where('id', '!=', $property->id)
            ->where('type', $property->type)
            ->take(3)
            ->get();

        return view('public.properties.show', compact('property', 'relatedProperties'));
    }

    /**
     * Display all agents
     */
    public function agents()
    {
        $page = Page::findBySlug('agents');
        $agents = Agent::withCount('listings')->paginate(12);

        return view('public.agents.index', compact('page', 'agents'));
    }

    /**
     * Display a single agent profile
     */
    public function agentShow(Agent $agent)
    {
        $agent->load(['listings.property']);

        $activeListings = $agent->listings()
            ->whereIn('status', self::PUBLIC_LISTING_STATUSES)
            ->with('property')
            ->get();

        return view('public.agents.show', compact('agent', 'activeListings'));
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        $page = Page::findBySlug('contact');

        return view('public.contact', compact('page'));
    }

    /**
     * Store a property inquiry
     */
    public function storeInquiry(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        $validated['status'] = 'new';

        Inquiry::create($validated);

        return back()->with('success', 'Your inquiry has been submitted successfully. We will get back to you soon!');
    }

    /**
     * Store a general contact message
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // For now, we'll create an inquiry without a listing_id
        // In the future, you might want a separate ContactMessage model
        Inquiry::create([
            'listing_id' => null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => "[{$validated['subject']}] " . $validated['message'],
            'status' => 'new',
        ]);

        return back()->with('success', 'Thank you for contacting us! We will respond shortly.');
    }
}
