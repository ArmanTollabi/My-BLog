@extends('layout')

@section('title', 'Create Category')

@section('content')

    <div class="max-w-2xl mx-auto px-6 py-10">

        <div class="mb-8">

            <h1 class="text-3xl font-bold text-gray-900">
                Create Category
            </h1>

            <p class="mt-2 text-gray-500">
                Create a new category for your posts.
            </p>

        </div>


        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

            <form
                action="{{ route('categories.store') }}"
                method="POST"
            >

                @csrf

                {{-- Category Name --}}
                <div>

                    <label
                        for="name"
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Category Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Enter category title"
                        class="w-full px-4 py-3
                               border border-gray-300
                               rounded-lg
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-500
                               focus:border-indigo-500"
                    >

                    @error('title')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-8">

                    <a
                        href="{{ route('categories.index') }}"
                        class="btn-cancel">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn-create"
                    >
                        Create
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
