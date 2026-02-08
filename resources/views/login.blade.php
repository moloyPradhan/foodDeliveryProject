<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @include('layouts.headerLink')
</head>

<body
    class="font-sans bg-gradient-to-br from-blue-50 via-indigo-50 to-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-80 max-w-full">
        <h2 class="text-2xl font-bold text-gray-700 mb-5 text-center">
            Login
        </h2>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm text-gray-600 mb-1">
                Email
            </label>
            <input type="email" id="email" name="email" placeholder="Enter Email" autocomplete="off" required
                class="w-full px-3 py-3 border border-gray-300 rounded-md focus:border-gray-500 focus:outline-none" />
        </div>

        <!-- Password -->
        <div class="relative mb-3">
            <label for="password" class="block text-sm text-gray-600 mb-1">
                Password
            </label>

            <input type="password" id="password" name="password" placeholder="Enter Password" autocomplete="off"
                required
                class="w-full px-3 py-3 pr-10 border border-gray-300 rounded-md focus:border-gray-500 focus:outline-none" />

            <!-- Toggle -->
            <button type="button" id="togglePwd" class="absolute right-3 top-[38px] text-gray-400 hover:text-blue-600">
                <!-- Eye -->
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                             c4.478 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.064 7-9.542 7
                             -4.477 0-8.268-2.943-9.542-7z" />
                </svg>

                <!-- Eye Off -->
                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
                             c-4.478 0-8.268-2.943-9.543-7
                             a9.956 9.956 0 012.873-4.568M6.223 6.223
                             A9.953 9.953 0 0112 5
                             c4.478 0 8.268 2.943 9.543 7
                             a9.97 9.97 0 01-4.043 5.181M15 12a3 3 0 00-3-3
                             m0 0a3 3 0 013 3m-3-3L3 21" />
                </svg>
            </button>
        </div>

        <!-- Links -->
        <div class="flex justify-between text-sm mb-5">
            <a href="{{ route('homePage') }}" class="text-blue-600 hover:underline">
                Forgot password?
            </a>

        </div>

        <!-- Login Button -->
        <button type="button" id="btnLogin"
            class="w-full py-3 bg-gray-800 hover:bg-gray-900 transition text-white font-semibold rounded-md shadow-sm">
            Login
        </button>

        <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">
                Don’t have an account?
            </span>
            <a href="{{ route('registerPage') }}" class="ml-1 text-sm text-blue-600 font-medium hover:underline">
                Create one
            </a>
        </div>
    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");
        const loginBtn = document.getElementById("btnLogin");

        const togglePwd = document.getElementById("togglePwd");
        const eyeOpen = document.getElementById("eyeOpen");
        const eyeClosed = document.getElementById("eyeClosed");

        togglePwd.addEventListener("click", () => {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeOpen.classList.toggle("hidden", isPassword);
            eyeClosed.classList.toggle("hidden", !isPassword);
        });

        loginBtn.addEventListener("click", login);

        async function login() {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!email) {
                showToast('warning', 'Enter email!');
                emailInput.focus();
                return;
            }

            if (!password) {
                showToast('warning', 'Enter password!');
                passwordInput.focus();
                return;
            }

            try {
                loginBtn.textContent = "Loading...";
                loginBtn.disabled = true;

                const res = await httpRequest('/api/auth/login', {
                    method: "POST",
                    body: {
                        email,
                        password
                    }
                });

                showToast('success', res.message);

                setTimeout(() => {
                    const params = new URLSearchParams(window.location.search);
                    const source = params.get('source');

                    if (source === 'cart') {
                        location.href = "{{ url('/cart') }}";
                    } else {
                        location.href = "{{ route('homePage') }}";
                    }
                }, 350);

            } catch (err) {
                console.error(err);
            } finally {
                loginBtn.textContent = "Login";
                loginBtn.disabled = false;
            }
        }
    </script>

</body>

</html>
