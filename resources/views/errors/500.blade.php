<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(-45deg, #0c1a0f, #0f2918, #1a2e0c, #0c1a1a);
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

        .grid-pattern {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none; z-index: 1;
        }

        .orbs { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.3; animation: orbFloat 8s ease-in-out infinite; }
        .orb-1 { width: 400px; height: 400px; background: linear-gradient(135deg, #f59e0b, #d97706); top: -10%; right: -5%; }
        .orb-2 { width: 350px; height: 350px; background: linear-gradient(135deg, #ef4444, #f59e0b); bottom: -10%; left: -5%; animation-delay: 2s; animation-duration: 10s; }

        @keyframes orbFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            33% { transform: translateY(-30px) scale(1.05); }
            66% { transform: translateY(20px) scale(0.95); }
        }

        .container {
            position: relative; z-index: 10; text-align: center; padding: 2rem; max-width: 560px;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .icon-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 80px; height: 80px; border-radius: 1.25rem;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(239, 68, 68, 0.1));
            border: 1px solid rgba(245, 158, 11, 0.2);
            margin-bottom: 1.5rem;
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.15); }
            50% { box-shadow: 0 0 0 15px rgba(245, 158, 11, 0); }
        }

        .icon-wrap svg { width: 40px; height: 40px; color: #f59e0b; }

        .error-code {
            font-size: clamp(100px, 18vw, 180px); font-weight: 900; line-height: 1;
            background: linear-gradient(135deg, #f59e0b, #fbbf24, #ef4444, #f59e0b);
            background-size: 300% 300%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; animation: shimmer 4s ease infinite;
            letter-spacing: -4px; margin-bottom: 0.5rem;
        }

        @keyframes shimmer { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

        .title { font-size: 1.5rem; font-weight: 700; color: #e2e8f0; margin-bottom: 0.75rem; }
        .description { font-size: 0.95rem; color: rgba(165, 180, 210, 0.7); line-height: 1.7; margin-bottom: 2.5rem; max-width: 400px; margin-left: auto; margin-right: auto; }

        .actions { display: flex; flex-direction: column; align-items: center; gap: 1rem; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.875rem 2rem; background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff; font-size: 0.875rem; font-weight: 600; border: none;
            border-radius: 0.875rem; cursor: pointer; text-decoration: none;
            transition: all 0.3s ease; box-shadow: 0 8px 25px -5px rgba(245, 158, 11, 0.4);
            position: relative; overflow: hidden;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 35px -5px rgba(245, 158, 11, 0.5); }
        .btn-primary::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent); transform: translateX(-100%); transition: transform 0.6s ease; }
        .btn-primary:hover::before { transform: translateX(100%); }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem; background: rgba(255,255,255,0.06);
            color: rgba(199,210,235,0.8); font-size: 0.8rem; font-weight: 500;
            border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem;
            cursor: pointer; text-decoration: none; transition: all 0.3s ease;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); color: #fff; }

        .btn-icon { width: 18px; height: 18px; }

        .logo-ring { background: linear-gradient(135deg, #f59e0b, #d97706); padding: 3px; border-radius: 9999px; box-shadow: 0 0 20px rgba(245, 158, 11, 0.4), 0 0 40px rgba(245, 158, 11, 0.1); display: inline-block; margin-bottom: 0.5rem; }
        .logo-ring-inner { background: white; border-radius: 9999px; padding: 10px; display: flex; align-items: center; justify-content: center; }
        .logo-ring img { width: 48px; height: 48px; object-fit: contain; }
        .brand-text { font-size: 1.25rem; font-weight: 800; color: #fff; letter-spacing: 0.25em; text-shadow: 0 2px 10px rgba(0,0,0,0.3); margin-bottom: 1.5rem; }
        .brand-sub { font-size: 0.65rem; color: rgba(165, 180, 210, 0.5); letter-spacing: 0.2em; margin-top: 0.25rem; font-weight: 500; }

        .footer { position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%); font-size: 0.7rem; color: rgba(165,180,210,0.3); letter-spacing: 0.1em; z-index: 10; }

        /* Glitch effect for 500 */
        .error-code { position: relative; }
        .error-code::before, .error-code::after {
            content: '500';
            position: absolute; top: 0; left: 0; right: 0;
            background: inherit; -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-code::before { animation: glitch1 3s infinite; clip-path: inset(20% 0 60% 0); }
        .error-code::after { animation: glitch2 3s infinite; clip-path: inset(60% 0 10% 0); }

        @keyframes glitch1 {
            0%, 90%, 100% { transform: translateX(0); }
            92% { transform: translateX(5px); }
            94% { transform: translateX(-5px); }
            96% { transform: translateX(3px); }
        }
        @keyframes glitch2 {
            0%, 90%, 100% { transform: translateX(0); }
            91% { transform: translateX(-5px); }
            93% { transform: translateX(5px); }
            95% { transform: translateX(-3px); }
        }
    </style>
</head>
<body>
    <div class="grid-pattern"></div>
    <div class="orbs"><div class="orb orb-1"></div><div class="orb orb-2"></div></div>

    <div class="container">
        <div class="logo-ring">
            <div class="logo-ring-inner">
                <img src="{{ asset('images/trpl.png') }}" alt="Logo TRPL">
            </div>
        </div>
        <div class="brand-text">INVENTARIS<div class="brand-sub">SISTEM INFORMASI</div></div>

        <div class="error-code">500</div>
        <h1 class="title">Terjadi Kesalahan Server</h1>
        <p class="description">Sepertinya ada yang tidak beres di sisi server kami. Tim teknis sudah diberitahu. Silakan coba lagi dalam beberapa saat.</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn-primary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Kembali ke Beranda
            </a>
            <a href="javascript:location.reload()" class="btn-secondary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                Muat Ulang
            </a>
        </div>
    </div>

    <div class="footer">&copy; {{ date('Y') }} Sistem Informasi Inventaris</div>
</body>
</html>
