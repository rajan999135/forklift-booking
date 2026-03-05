{{-- resources/views/admin/forklifts/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6" x-data="imageUploader()">
    <h1 class="text-2xl font-semibold mb-6">Add Forklift</h1>

    {{-- Success message --}}
    @if (session('ok'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('ok') }}
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.forklifts.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="mt-1 w-full border rounded-md p-2 text-sm"
                    required
                >
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hourly Rate --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Hourly Rate (CAD)</label>
                <input
                    type="number"
                    step="0.01"
                    name="hourly_rate"
                    value="{{ old('hourly_rate') }}"
                    class="mt-1 w-full border rounded-md p-2 text-sm"
                    required
                >
                @error('hourly_rate')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Capacity --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Capacity (kg)</label>
                <input
                    type="number"
                    name="capacity_kg"
                    value="{{ old('capacity_kg') }}"
                    class="mt-1 w-full border rounded-md p-2 text-sm"
                    required
                >
                @error('capacity_kg')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Location (free text) --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Location</label>
                <input
                    type="text"
                    name="location_name"
                    class="mt-1 w-full border rounded-md p-2 text-sm"
                    placeholder="e.g. Regina Yard, Saskatoon Depot"
                    value="{{ old('location_name') }}"
                >
                @error('location_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Main image --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Main Image</label>
            <div
                class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors"
                :class="{ 'border-green-400 bg-green-50': mainPreview }"
                @dragover.prevent
                @drop.prevent="dropMain($event)"
                @click="$refs.main.click()"
            >
                <input
                    type="file"
                    accept="image/*"
                    class="hidden"
                    name="image"
                    x-ref="main"
                    @change="previewMain"
                >

                <template x-if="!mainPreview">
                    <p class="text-slate-500">
                        Drag your file here or click.
                        <span class="text-pink-600 underline">Select a file</span>
                    </p>
                </template>

                <template x-if="mainPreview">
                    <img
                        :src="mainPreview"
                        alt="Preview"
                        class="mx-auto max-h-56 rounded-md shadow mt-2"
                    >
                </template>
            </div>
            @error('image')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Gallery images --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Gallery Images</label>
            <div
                class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors"
                :class="{ 'border-green-400 bg-green-50': galleryPreviews.length }"
                @dragover.prevent
                @drop.prevent="dropGallery($event)"
                @click="$refs.gallery.click()"
            >
                <input
                    type="file"
                    accept="image/*"
                    class="hidden"
                    name="images[]"
                    multiple
                    x-ref="gallery"
                    @change="previewGallery"
                >

                <p class="text-slate-500">
                    Drag one more picture here.
                </p>

                <div
                    class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"
                    x-show="galleryPreviews.length"
                >
                    <template x-for="(src, i) in galleryPreviews" :key="i">
                        <img :src="src" class="h-28 w-full object-cover rounded-md shadow">
                    </template>
                </div>
            </div>
            @error('images.*')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button
                type="submit"
                class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700"
            >
                Save
            </button>

            <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-md border">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- Alpine inline --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploader', () => ({
            mainPreview: null,
            galleryPreviews: [],

            previewMain(e) {
                const file = e.target.files?.[0];
                if (file) {
                    this.mainPreview = URL.createObjectURL(file);
                }
            },

            dropMain(e) {
                const files = e.dataTransfer.files;
                if (!files?.length) return;
                this.$refs.main.files = files;
                this.previewMain({ target: this.$refs.main });
            },

            previewGallery(e) {
                const files = Array.from(e.target.files || []);
                this.galleryPreviews = files.map(f => URL.createObjectURL(f));
            },

            dropGallery(e) {
                const files = e.dataTransfer.files;
                if (!files?.length) return;
                this.$refs.gallery.files = files;
                this.previewGallery({ target: this.$refs.gallery });
            },
        }));
    });
</script>
@endsection
