<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Entertainer;
use App\Models\SharedCart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Generate a short shareable URL for the cart
     */
    public function generateSharedLink(Request $request)
    {
        try {
            $cartData = $request->input('cart');
            $websiteSlug = $request->input('website_slug');
            $affiliateSlug = $request->input('affiliate_slug');
            $clubSlug = $request->input('club_slug');

            if (!$cartData || (!$websiteSlug && !$affiliateSlug)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing cart data or slug'
                ], 400);
            }

            // Decode the cart JSON string to an array for storage
            $cartArray = json_decode($cartData, true);

            if (!is_array($cartArray)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cart data format'
                ], 400);
            }

            // Generate a unique random code (8 characters)
            $code = Str::random(8);

            // Check if code exists, regenerate if it does
            while (SharedCart::where('code', $code)->exists()) {
                $code = Str::random(8);
            }

            // Save the cart to database (as array, will be auto-encoded)
            $sharedCart = SharedCart::create([
                'code'           => $code,
                'cart_data'      => $cartArray,
                'website_slug'   => $websiteSlug ?? '',
                'affiliate_slug' => $affiliateSlug,
                'club_slug'      => $clubSlug,
            ]);

            // Generate the short URL
            $shortUrl = route('shared-cart.view', ['code' => $code]);

            return response()->json([
                'success' => true,
                'short_url' => $shortUrl,
                'code' => $code,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating shared link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the shared cart
     */
    public function viewSharedCart($code)
    {
        $sharedCart = SharedCart::where('code', $code)->firstOrFail();

        // cart_data is already an array due to the cast
        $cartJson = json_encode($sharedCart->cart_data);
        $cartParam = urlencode($cartJson);

        // Redirect to affiliate page if this is an affiliate cart
        if ($sharedCart->affiliate_slug) {
            $affiliateExists = Affiliate::where('slug', $sharedCart->affiliate_slug)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->exists();

            $entertainerExists = Entertainer::where('slug', $sharedCart->affiliate_slug)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->exists();

            $params = ['cart' => $cartParam];
            if ($sharedCart->club_slug) {
                $params['club'] = $sharedCart->club_slug;
            }

            if ($affiliateExists) {
                return redirect()->route('affiliate.public', array_merge(
                    ['slug' => $sharedCart->affiliate_slug],
                    $params
                ));
            }

            if ($entertainerExists) {
                return redirect()->route('entertainer.public', array_merge(
                    ['slug' => $sharedCart->affiliate_slug],
                    $params
                ));
            }
        }

        // Redirect to the regular checkout page with cart as URL parameter
        return redirect()->route('index', [
            'slug' => $sharedCart->website_slug,
            'cart' => $cartParam
        ]);
    }
}
