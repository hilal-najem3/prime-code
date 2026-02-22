<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laravel Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts & Icons -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">
    @yield('styles')
</head>

<body class="h-screen flex overflow-hidden bg-gray-50">

    @php
    $languages = $languages ?? App\Models\Language::where('is_active', 1)->get();
    @endphp

    <!-- Sidebar -->
    <aside id="sidebar"
        class="w-64 bg-white border-r flex flex-col h-screen overflow-y-auto transition-transform duration-300 transform lg:translate-x-0 -translate-x-full fixed lg:static z-50">
        <div class="flex justify-between items-center px-4 py-4 border-b">
            <span class="text-xl font-semibold">Admin</span>
            <button id="sidebarCloseBtn" class="lg:hidden text-gray-500 hover:text-red-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-2 text-gray-700 text-sm">
            <x-admin.nav-item icon="home" label="Dashboard" route="admin.dashboard" />

            <x-admin.nav-group icon="folder" label="Management">
                <x-admin.sub-item route="admin.users.index" label="Users" />
                <x-admin.sub-item route="admin.languages.index" label="Languages" />
            </x-admin.nav-group>

            <x-admin.nav-group icon="file-text" label="Posts">
                <x-admin.sub-item route="admin.post-categories.index" label="Categories" />
                <x-admin.sub-item route="admin.posts.index" label="Posts" />
                <x-admin.sub-item route="admin.post-tags.index" label="Tags" />
            </x-admin.nav-group>

            <x-admin.nav-item icon="user" label="Profile" route="profile.edit" />

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2 rounded-md hover:bg-gray-100 text-left">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span class="ml-2">Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="flex items-center justify-between px-4 py-4 bg-white border-b shadow-sm">
            <button id="sidebarOpenBtn" class="lg:hidden text-gray-700 text-2xl">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-xl font-bold">Welcome Admin!</h1>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        const languages = @json($languages->map(fn($lang) => ['code' => $lang->code, 'name' => $lang->name]) ?? []);
    </script>

    <script>
        lucide.createIcons();

        $('#sidebarOpenBtn').on('click', () => $('#sidebar').removeClass('-translate-x-full'));
        $('#sidebarCloseBtn').on('click', () => $('#sidebar').addClass('-translate-x-full'));

        $('[data-toggle]').on('click', function () {
            const submenu = $(this).next('[data-submenu]');
            submenu.toggleClass('hidden');
            $(this).find('[data-arrow]').toggleClass('rotate-90');
        });
    </script>

    @stack('scripts')
</body>

</html>