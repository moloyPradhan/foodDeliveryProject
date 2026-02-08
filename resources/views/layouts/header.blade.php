<header class="sticky top-0 z-50 bg-white shadow px-4 py-3">
    <div class="max-w-7xl mx-auto flex items-center gap-4">

        <!-- Logo -->
        <div class="text-xl font-semibold shrink-0">
            <a href="{{ url('/') }}">FulBite</a>
        </div>

        <!-- Search -->
        <div class="flex-1 max-w-xl mx-auto hidden sm:block">
            <input type="text" id="search" placeholder="Search..."
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-gray-500" />
        </div>

        <!-- Auth -->
        <div class="relative shrink-0" x-data="{ open: false }">
            @if ($isLoggedIn)
                <button @click="open = !open"
                    class="flex items-center gap-2 text-gray-700 hover:text-blue-600 focus:outline-none">
                    <span>{{ $authUser['name'] ?? 'User' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-cloak x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg">
                    <a href="{{ route('profilePage') }}" class="block px-4 py-2 hover:bg-gray-100">
                        Profile
                    </a>

                    <button id="btnLogout" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                        Logout
                    </button>
                </div>
            @else
                <a href="{{ route('loginPage') }}" class="text-gray-700 hover:text-blue-600" title="Login">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
                    </svg>
                </a>
            @endif
        </div>
    </div>
</header>

<!-- Alpine -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script type="module">
    import {
        httpRequest,
        showConfirmAlert
    } from '/js/httpClient.js';

    // Preserve search query
    const params = new URLSearchParams(window.location.search);
    const searchValue = params.get("search");
    const searchBar = document.getElementById('search');

    if (searchBar && searchValue) {
        searchBar.value = searchValue;
    }

    // Logout (safe)
    const logoutBtn = document.getElementById("btnLogout");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", async () => {
            const result = await showConfirmAlert("question", "Sure want to logout?");
            if (!result.isConfirmed) return;

            await httpRequest('/api/auth/logout', {
                method: 'POST'
            });
            window.location.href = @json(route('homePage'));
        });
    }
</script>
