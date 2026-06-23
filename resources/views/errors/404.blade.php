<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #1a1a2e);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            color: #fff;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Grid pattern */
        .grid-pattern {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 1;
        }

        /* Floating orbs */
        .orbs { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: orbFloat 8s ease-in-out infinite;
        }

        .orb-1 {
            width: 400px; height: 400px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -10%; right: -5%;
        }

        .orb-2 {
            width: 350px; height: 350px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            bottom: -10%; left: -5%;
            animation-delay: 2s;
            animation-duration: 10s;
        }

        .orb-3 {
            width: 250px; height: 250px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            top: 40%; left: 60%;
            animation-delay: 4s;
            animation-duration: 12s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            33% { transform: translateY(-30px) scale(1.05); }
            66% { transform: translateY(20px) scale(0.95); }
        }

        /* Main container */
        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
            max-width: 560px;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* 404 number */
        .error-code {
            font-size: clamp(120px, 20vw, 200px);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #667eea 0%, #a78bfa 30%, #f093fb 60%, #667eea 100%);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 4s ease infinite;
            position: relative;
            letter-spacing: -4px;
            margin-bottom: 0.5rem;
        }

        .error-code::after {
            content: '404';
            position: absolute;
            inset: 0;
            font-size: inherit;
            font-weight: inherit;
            letter-spacing: inherit;
            background: linear-gradient(135deg, #667eea, #a78bfa, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: blur(40px);
            opacity: 0.4;
            z-index: -1;
        }

        @keyframes shimmer {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Ghost icon */
        .ghost {
            display: inline-block;
            margin-bottom: 1.5rem;
            animation: ghostBounce 3s ease-in-out infinite;
        }

        @keyframes ghostBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .ghost svg {
            width: 72px;
            height: 72px;
            filter: drop-shadow(0 0 20px rgba(102, 126, 234, 0.3));
        }

        /* Texts */
        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }

        .description {
            font-size: 0.95rem;
            color: rgba(165, 180, 210, 0.7);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            border-radius: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px -5px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px -5px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .btn-primary:hover::before {
            transform: translateX(100%);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.06);
            color: rgba(199, 210, 235, 0.8);
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-icon {
            width: 18px;
            height: 18px;
        }

        /* Floating particles */
        .particles { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 2; }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            animation: particleRise linear infinite;
        }

        @keyframes particleRise {
            from { transform: translateY(100vh); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            to { transform: translateY(-100vh); opacity: 0; }
        }

        /* Decorative ring */
        .deco-ring {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
            animation: ringPulse 6s ease-in-out infinite;
        }

        .deco-ring-1 {
            width: 500px; height: 500px;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        .deco-ring-2 {
            width: 700px; height: 700px;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 1s;
        }

        .deco-ring-3 {
            width: 900px; height: 900px;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 2s;
        }

        @keyframes ringPulse {
            0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.08; transform: translate(-50%, -50%) scale(1.05); }
        }

        /* Logo ring */
        .logo-ring {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 3px;
            border-radius: 9999px;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.4), 0 0 40px rgba(102, 126, 234, 0.1);
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .logo-ring-inner {
            background: white;
            border-radius: 9999px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-ring img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .brand-text {
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.25em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            margin-bottom: 1.5rem;
        }

        .brand-sub {
            font-size: 0.65rem;
            color: rgba(165, 180, 210, 0.5);
            letter-spacing: 0.2em;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.7rem;
            color: rgba(165, 180, 210, 0.3);
            letter-spacing: 0.1em;
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="grid-pattern"></div>

    <div class="orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Decorative rings -->
    <div class="deco-ring deco-ring-1"></div>
    <div class="deco-ring deco-ring-2"></div>
    <div class="deco-ring deco-ring-3"></div>

    <!-- Particles -->
    <div class="particles">
        <div class="particle" style="left:5%;  animation-duration:18s; animation-delay:0s;"></div>
        <div class="particle" style="left:12%; animation-duration:22s; animation-delay:3s;"></div>
        <div class="particle" style="left:25%; animation-duration:16s; animation-delay:1s;"></div>
        <div class="particle" style="left:35%; animation-duration:25s; animation-delay:5s;"></div>
        <div class="particle" style="left:48%; animation-duration:19s; animation-delay:2s;"></div>
        <div class="particle" style="left:58%; animation-duration:23s; animation-delay:7s;"></div>
        <div class="particle" style="left:70%; animation-duration:17s; animation-delay:4s;"></div>
        <div class="particle" style="left:82%; animation-duration:21s; animation-delay:6s;"></div>
        <div class="particle" style="left:93%; animation-duration:20s; animation-delay:1s;"></div>
        <div class="particle" style="left:40%; animation-duration:26s; animation-delay:8s;"></div>
    </div>

    <div class="container">
        <!-- Logo -->
        <div class="logo-ring">
            <div class="logo-ring-inner">
                <img src="{{ asset('images/trpl.png') }}" alt="Logo TRPL">
            </div>
        </div>
        <div class="brand-text">INVENTARIS<div class="brand-sub">SISTEM INFORMASI</div></div>

        <!-- 404 -->
        <div class="error-code">404</div>

        <!-- Text -->
        <h1 class="title">Halaman Tidak Ditemukan</h1>
        <p class="description">
            Oops! Halaman yang kamu cari sepertinya sudah dipindahkan, dihapus, atau mungkin tidak pernah ada.
        </p>

        <!-- Actions -->
        <div class="actions">
            <a href="{{ url('/') }}" class="btn-primary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                Kembali ke Beranda
            </a>
            <a href="javascript:history.back()" class="btn-secondary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                </svg>
                Halaman Sebelumnya
            </a>
        </div>
    </div>

    <div class="footer">&copy; {{ date('Y') }} Sistem Informasi Inventaris</div>
</body>
</html>
