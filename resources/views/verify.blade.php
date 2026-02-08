<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    @include('layouts.headerLink')
</head>

<body
    class="font-sans bg-gradient-to-br from-blue-50 via-indigo-50 to-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-80 max-w-full">
        <h2 class="text-2xl font-bold text-gray-700 mb-2 text-center">
            Verify Code
        </h2>

        <p class="text-sm text-gray-500 mb-5 text-center">
            Enter the 6-digit code
        </p>

        <!-- OTP Inputs -->
        <div class="flex justify-between mb-5">
            <input class="otp-input" maxlength="1" />
            <input class="otp-input" maxlength="1" />
            <input class="otp-input" maxlength="1" />
            <input class="otp-input" maxlength="1" />
            <input class="otp-input" maxlength="1" />
            <input class="otp-input" maxlength="1" />
        </div>

        <!-- Verify Button -->
        <button id="btnVerify"
            class="w-full py-3 bg-gray-800 hover:bg-gray-900 transition text-white font-semibold rounded-md shadow-sm">
            Verify
        </button>
    </div>

    <style>
        .otp-input {
            width: 42px;
            height: 48px;
            text-align: center;
            font-size: 1.25rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
        }

        .otp-input:focus {
            border-color: #939396;
        }
    </style>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        const otpInputs = document.querySelectorAll('.otp-input');
        const btnVerify = document.getElementById('btnVerify');

        const email = "{{ $email }}"; // or pass via blade

        // Auto focus next input
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        btnVerify.addEventListener('click', verifyCode);

        async function verifyCode() {
            const code = Array.from(otpInputs)
                .map(input => input.value)
                .join('');

            if (code.length !== 6) {
                showToast('warning', 'Enter 6 digit code');
                return;
            }

            try {
                btnVerify.textContent = 'Verifying...';
                btnVerify.disabled = true;

                await httpRequest('/api/auth/verify', {
                    method: 'POST',
                    body: {
                        email,
                        code
                    }
                });

                showToast('success', 'Account verified successfully! ');

                setTimeout(() => {
                    location.href = "{{ route('loginPage') }}";
                }, 200);

            } catch (err) {
                showToast('error', 'Invalid or expired code');
                console.error(err);
            } finally {
                btnVerify.textContent = 'Verify';
                btnVerify.disabled = false;
            }
        }
    </script>
</body>

</html>
