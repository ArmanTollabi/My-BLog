<!--header-->
@include('layouts.header')

<!-- Sidebar -->
@auth
    @can('view_cat',\App\Models\Category::class)
    @include('layouts.sidebar')
    @endcan
@endauth


<!-- Main -->
<main class="flex-1">
    <div class="max-w-4xl mx-auto w-full px-4 sm:px-6">
        @if (session('success'))
            <x-alert type="success" class="mt-6">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
        @endif

        @if (session('warning'))
            <x-alert type="warning" class="mt-6">{{ session('warning') }}</x-alert>
        @endif

        @if (session('status'))
            <x-alert type="info" class="mt-6">{{ session('status') }}</x-alert>
        @endif
    </div>

    @yield('content')

</main>


<!-- Footer -->
<x-confirm-modal />
@include('layouts.footer')
