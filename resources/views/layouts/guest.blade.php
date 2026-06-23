<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }

            /* Animated gradient background */
            .auth-bg {
                background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #1a1a2e);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }

            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Floating orbs */
            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.4;
                animation: float 8s ease-in-out infinite;
            }

            .orb-1 {
                width: 400px; height: 400px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                top: -10%; right: -5%;
                animation-delay: 0s;
                animation-duration: 8s;
            }

            .orb-2 {
                width: 350px; height: 350px;
                background: linear-gradient(135deg, #f093fb, #f5576c);
                bottom: -10%; left: -5%;
                animation-delay: 2s;
                animation-duration: 10s;
            }

            .orb-3 {
                width: 300px; height: 300px;
                background: linear-gradient(135deg, #4facfe, #00f2fe);
                top: 50%; left: 50%;
                transform: translate(-50%, -50%);
                animation-delay: 4s;
                animation-duration: 12s;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) scale(1); }
                33% { transform: translateY(-30px) scale(1.05); }
                66% { transform: translateY(20px) scale(0.95); }
            }

            /* Grid pattern overlay */
            .grid-pattern {
                background-image:
                    linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
                background-size: 60px 60px;
            }

            /* Auth card */
            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                border: 1px solid rgba(255, 255, 255, 0.4);
                box-shadow:
                    0 0 0 1px rgba(255, 255, 255, 0.1),
                    0 20px 50px -12px rgba(0, 0, 0, 0.35),
                    0 0 80px rgba(99, 102, 241, 0.08);
                animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
            }

            @keyframes cardEntrance {
                from {
                    opacity: 0;
                    transform: translateY(30px) scale(0.96);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            /* Logo entrance */
            .logo-entrance {
                animation: logoSlideDown 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
            }

            @keyframes logoSlideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px) scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            /* Logo glow ring */
            .logo-ring {
                background: linear-gradient(135deg, #667eea, #764ba2);
                padding: 3px;
                border-radius: 9999px;
                box-shadow:
                    0 0 20px rgba(102, 126, 234, 0.4),
                    0 0 40px rgba(102, 126, 234, 0.1);
            }

            .logo-ring-inner {
                background: white;
                border-radius: 9999px;
                padding: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Particles */
            .particle {
                position: absolute;
                width: 3px;
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                animation: particleFloat linear infinite;
            }

            @keyframes particleFloat {
                from { transform: translateY(100vh) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                to { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
            }
        </style>
    </head>
    <body class="auth-bg min-h-screen relative overflow-x-hidden antialiased">
        <!-- Grid overlay -->
        <div class="fixed inset-0 grid-pattern pointer-events-none"></div>

        <!-- Floating orbs -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>

        <!-- Particles -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            @for ($i = 0; $i < 15; $i++)
                <div class="particle" style="
                    left: {{ rand(0, 100) }}%;
                    animation-duration: {{ rand(15, 30) }}s;
                    animation-delay: {{ rand(0, 15) }}s;
                    width: {{ rand(2, 4) }}px;
                    height: {{ rand(2, 4) }}px;
                "></div>
            @endfor
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 sm:px-0 relative z-10">
            <!-- Logo Section -->
            <div class="logo-entrance">
                <a href="/" class="flex flex-col items-center gap-3 group transition-all duration-300">
                    <div class="logo-ring transition-all duration-300 group-hover:shadow-[0_0_30px_rgba(102,126,234,0.6)]">
                        <div class="logo-ring-inner">
                            <img src="{{ asset('images/trpl.png') }}" alt="Logo TRPL" class="w-12 h-12 object-contain">
                        </div>
                    </div>
                    <div class="text-center mt-1">
                        <h1 class="text-2xl font-extrabold text-white tracking-[0.25em] drop-shadow-lg">INVENTARIS</h1>
                        <p class="text-xs text-indigo-300/70 tracking-[0.2em] mt-1 font-medium">SISTEM INFORMASI</p>
                    </div>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md mt-6 sm:mt-8 px-7 py-8 sm:px-9 sm:py-10 auth-card rounded-[1.5rem]">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="mt-8 mb-6 text-xs text-indigo-300/40 text-center tracking-wider font-medium logo-entrance" style="animation-delay: 0.4s;">
                &copy; {{ date('Y') }} Sistem Informasi Inventaris
            </p>
        </div>
    </body>
</html>
