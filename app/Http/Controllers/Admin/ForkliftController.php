<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForkliftRequest;
use App\Models\Forklift;
use App\Models\Location;

class ForkliftController extends Controller
{
    public function index()
    {
        $forklifts = Forklift::with('location')->latest()->paginate(12);

        return view('admin.forklifts.index', compact('forklifts'));
    }

    public function create()
    {
        // $locations is no longer strictly needed for the text input,
        // but you might still use it elsewhere (e.g. suggestions).
        $locations = Location::orderBy('name')->get();

        return view('admin.forklifts.create', compact('locations'));
    }

    public function store(ForkliftRequest $request)
    {
        $data     = $request->validated();
        $uploaded = false;

        // 1) Create or reuse location from location_name
        //    (location_name is coming from the form input)
        $location = Location::firstOrCreate(
            ['name' => trim($data['location_name'])],
            [
                'address'     => null,
                'city'        => null,
                'province'    => null,
                'postal_code' => null,
            ]
        );

        // Inject the foreign key and remove the temporary field
        $data['location_id'] = $location->id;
        unset($data['location_name']);

        

        // 2) Main image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('forklifts', 'public');
            $uploaded = true;
        }

        // 3) Gallery images (images[])
        if ($request->hasFile('images')) {
            $data['images'] = collect($request->file('images'))
                ->map(fn ($f) => $f->store('forklifts', 'public'))
                ->values()
                ->all();
            $uploaded = true;
        }

        // 4) Create forklift with correct location_id + images
        Forklift::create($data);

        return redirect()
            ->route('admin.forklifts.create')
            ->with('ok', 'Forklift created.')
            ->with('image_uploaded', $uploaded);
    }

    public function edit(Forklift $forklift)
    {
        $locations = Location::orderBy('name')->get();

        return view('admin.forklifts.edit', compact('forklift', 'locations'));
    }

    public function update(ForkliftRequest $request, Forklift $forklift)
    {
        $data     = $request->validated();
        $uploaded = false;

        // 1) Update or reuse location from location_name (if provided)
        if (!empty($data['location_name'])) {
            $location = Location::firstOrCreate(
                ['name' => trim($data['location_name'])],
                [
                    'address'     => null,
                    'city'        => null,
                    'province'    => null,
                    'postal_code' => null,
                ]
            );

            $data['location_id'] = $location->id;
        }

        unset($data['location_name']);

        // 2) Begin from existing gallery
        $gallery = collect($forklift->images ?? []);

        // Optional: clear all gallery
        if ($request->boolean('clear_gallery')) {
            $gallery = collect();
        }

        // Optional: remove selected images
        $toRemove = collect($request->input('remove_images', []));
        if ($toRemove->isNotEmpty()) {
            $gallery = $gallery
                ->reject(fn ($path) => $toRemove->contains($path))
                ->values();
        }

        // 3) Replace main image if a new one was uploaded
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('forklifts', 'public');
            $uploaded = true;
        }

        // 4) Append newly uploaded gallery images
        if ($request->hasFile('images')) {
            $newGalleryUploads = collect($request->file('images'))
                ->map(fn ($f) => $f->store('forklifts', 'public'));
            $gallery = $gallery->merge($newGalleryUploads)->values();
            $uploaded = true;
        }

        // 5) Persist final gallery
        $data['images'] = $gallery->all();

        $forklift->update($data);

        return redirect()
            ->route('admin.forklifts.edit', $forklift)
            ->with('ok', 'Forklift updated.')
            ->with('image_uploaded', $uploaded);
    }

    public function destroy(Forklift $forklift)
    {
        $forklift->delete();

        return back()->with('ok', 'Forklift deleted.');
    }
}
