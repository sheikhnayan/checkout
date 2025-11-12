<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Addon;
use App\Models\Event;
use App\Models\PromoCode;

class FrontendController extends Controller
{
    public function index($slug, Request $request)
    {
        // Get website by slug instead of domain
        $data = Website::where('slug', $slug)->first();
        
        // Return 404 if website not found
        if (!$data) {
            abort(404, 'Website not found');
        }

        if (isset($request->event_name)) {
            # code...
            $event = Event::where('name', $request->event_name)->first();
            return view('index', compact('data', 'event')); // Assuming 'index' is the view you want to return
        }

        return view('index_two', compact('data')); // Assuming 'index' is the view you want to return

        // dd($event);

    }

    public function addons($slug, $id)
    {
        $data = Addon::where('package_id', $id)->get();

        return response()->json($data);
    }

    public function checkCode($slug, $code)
    {
        $check = PromoCode::where('promo_code', $code)->first();

        if ($check) {
            return response()->json(['valid' => true, 'discount' => $check->percentage, 'type' => $check->type, 'id' => $check->id]);
        }

        return response()->json(['valid' => false]);
    }
}
