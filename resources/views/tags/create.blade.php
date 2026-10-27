@extends('layout')

@section('title', 'Create Tag')

@section('content')

    <div class="max-w-2xl mx-auto px-6 py-8">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                Create Tag
            </h1>

            <p class="text-gray-500 mt-1">
                Add a new tag
            </p>
        </div>


        <form
            action="{{ route('tags.store') }}"
            method="POST"
            class="bg-white p-6 rounded-xl shadow-sm"
        >

            @csrf

            <div class="mb-6">

                <label
                    for="title"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Tag Name
                </label>

                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none"
                    placeholder="Enter tag name"
                >

                @error('title')
                <p class="text-red-500 text-sm mt-2">
                    {{ $message }}
                </p>
                @enderror

            </div>


            <div class="flex items-center gap-3">

                <a
                    href="{{ route('tags.index') }}"
                    class="btn-cancel"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn-create"
                >
                    Create Tag
                </button>

            </div>

        </form>

    </div>

@endsection
