<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forklift;
use App\Models\Location;

class ForkliftController extends Controller
{
    public function create()
    {
        return view('admin.forklifts.create');
    }

    public function store(Request $request)
    {
        // Validate fields
        $data = $request->validate([
            'name'          => ['required','string','max:120'],
            'hourly_rate'   => ['required','numeric','min:0'],
            'capacity_kg'   => ['required','numeric','min:1'],
            'location_name' => ['required','string','max:120'],
            'main_image'    => ['required','image','max:5120'],
            'gallery.*'     => ['nullable','image','max:5120'],
        ]);

        // Create or reuse location
        $location = Location::firstOrCreate(
            ['name' => trim($data['location_name'])],
            [
                'address'     => null,
                'city'        => null,
                'province'    => null,
                'postal_code' => null,
            ]
        );

        // Upload main image
        $image = $request->file('main_image')->store('forklifts', 'public');

        // Upload gallery
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('forklifts', 'public');
            }
        }

        // Create forklift
        Forklift::create([
            'name'        => $data['name'],
            'hourly_rate' => $data['hourly_rate'],
            'capacity_kg' => $data['capacity_kg'],
            'location_id' => $location->id,
            'image'       => $image,
            'images'      => json_encode($galleryPaths),
        ]);

        return back()->with('status', 'Forklift created successfully!');
    }
}
