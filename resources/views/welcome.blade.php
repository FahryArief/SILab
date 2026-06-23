<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SILab') }} - Sistem Informasi Manajemen Inventaris Laboratorium Komputer</title>
    <meta name="description" content="Sistem Informasi Manajemen Inventaris Laboratorium Komputer - Dokumentasi, Prestasi, dan Fasilitas Lab terbaik.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background: #0a0a1a;
            color: #ffffff;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* ===== NAVIGATION ===== */
        .nav-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-wrapper.scrolled {
            background: rgba(10, 10, 26, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .nav-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }
        .nav-brand-icon svg {
            width: 22px;
            height: 22px;
            color: white;
        }
        .nav-brand-text {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, #ffffff, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }
        .nav-btn {
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .nav-btn-outline {
            color: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.35);
            color: #fff;
        }
        .nav-btn-primary {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            border: none;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35);
        }
        .nav-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.5);
        }

        /* ===== HERO SECTION ===== */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25), transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        .hero-bg::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2), transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 850px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #a5b4fc;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: #34d399;
            border-radius: 50%;
            position: relative;
        }
        .hero-badge-dot::after {
            content: '';
            position: absolute;
            inset: -3px;
            background: #34d399;
            border-radius: 50%;
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
            opacity: 0;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin: 0 0 1.5rem;
            animation: fadeInUp 0.8s ease-out 0.1s forwards;
            opacity: 0;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, #c084fc, #818cf8, #6ee7b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            max-width: 650px;
            margin: 0 auto 2.5rem;
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
            opacity: 0;
        }
        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.3s forwards;
            opacity: 0;
        }
        .btn-hero-primary {
            padding: 1rem 2.5rem;
            font-size: 1.05rem;
            font-weight: 700;
            border-radius: 50px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(99, 102, 241, 0.55);
        }
        .btn-hero-secondary {
            padding: 1rem 2.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.06);
            color: white;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .hero-scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            animation: bounce 2s infinite;
        }
        .hero-scroll-indicator svg {
            width: 28px;
            height: 28px;
            color: rgba(255, 255, 255, 0.3);
        }

        /* ===== SECTION SHARED STYLES ===== */
        .section {
            padding: 6rem 2rem;
            position: relative;
        }
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #a5b4fc;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }
        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin: 0 0 1rem;
            line-height: 1.2;
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.5);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ===== DOKUMENTASI KEGIATAN LAB ===== */
        .dokumentasi-section {
            background: linear-gradient(180deg, #0a0a1a 0%, #0f0f2e 100%);
        }
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }
        .doc-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(40px);
        }
        .doc-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .doc-card:hover {
            transform: translateY(-8px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.15);
        }
        .doc-card-img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .doc-card:hover .doc-card-img {
            transform: scale(1.05);
        }
        .doc-card-img-wrapper {
            overflow: hidden;
            position: relative;
        }
        .doc-card-img-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(to top, rgba(10, 10, 26, 0.8), transparent);
        }
        .doc-card-date {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.35rem 0.85rem;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #c4b5fd;
            border: 1px solid rgba(139, 92, 246, 0.3);
            z-index: 2;
        }
        .doc-card-body {
            padding: 1.5rem;
        }
        .doc-card-tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(99, 102, 241, 0.15);
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #818cf8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }
        .doc-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.75rem;
            line-height: 1.4;
        }
        .doc-card p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            line-height: 1.7;
            margin: 0;
        }

        /* ===== PRESTASI ===== */
        .prestasi-section {
            background: #0f0f2e;
            position: relative;
        }
        .prestasi-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.1), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .prestasi-timeline {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }
        .prestasi-timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, transparent, rgba(99, 102, 241, 0.4), rgba(168, 85, 247, 0.4), transparent);
            transform: translateX(-50%);
        }
        .prestasi-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 3rem;
            position: relative;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .prestasi-item.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .prestasi-item:nth-child(odd) {
            flex-direction: row;
        }
        .prestasi-item:nth-child(even) {
            flex-direction: row-reverse;
        }
        .prestasi-content {
            width: calc(50% - 3rem);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .prestasi-content:hover {
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.1);
            transform: translateY(-4px);
        }
        .prestasi-dot {
            position: absolute;
            left: 50%;
            top: 2rem;
            transform: translateX(-50%);
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border: 3px solid #0f0f2e;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
            z-index: 2;
        }
        .prestasi-year {
            font-size: 0.8rem;
            font-weight: 700;
            color: #a5b4fc;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.5rem;
        }
        .prestasi-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        .prestasi-content h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
        }
        .prestasi-content p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            line-height: 1.7;
            margin: 0;
        }
        .prestasi-medal {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-top: 0.75rem;
        }
        .medal-gold {
            background: rgba(251, 191, 36, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
        .medal-silver {
            background: rgba(156, 163, 175, 0.15);
            color: #9ca3af;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }
        .medal-bronze {
            background: rgba(217, 119, 6, 0.15);
            color: #d97706;
            border: 1px solid rgba(217, 119, 6, 0.3);
        }
        .medal-champion {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        /* ===== FASILITAS LAB GALLERY ===== */
        .fasilitas-section {
            background: linear-gradient(180deg, #0f0f2e 0%, #0a0a1a 100%);
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .gallery-item.visible {
            opacity: 1;
            transform: scale(1);
        }
        .gallery-item:first-child {
            grid-column: span 2;
            grid-row: span 2;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
            min-height: 250px;
        }
        .gallery-item:first-child img {
            min-height: 520px;
        }
        .gallery-item:hover img {
            transform: scale(1.08);
        }
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10, 10, 26, 0.9) 0%, rgba(10, 10, 26, 0.3) 40%, transparent 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1.5rem;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-overlay h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 0.25rem;
        }
        .gallery-overlay p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
        }
        .gallery-overlay-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5);
            width: 50px;
            height: 50px;
            background: rgba(99, 102, 241, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.4s ease;
        }
        .gallery-item:hover .gallery-overlay-icon {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        .gallery-overlay-icon svg {
            width: 22px;
            height: 22px;
            color: white;
        }

        /* ===== LIGHTBOX ===== */
        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(0, 0, 0, 0.92);
            backdrop-filter: blur(20px);
            align-items: center;
            justify-content: center;
            padding: 2rem;
            cursor: pointer;
        }
        .lightbox.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        .lightbox img {
            max-width: 90%;
            max-height: 85vh;
            border-radius: 16px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            cursor: default;
        }
        .lightbox-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
        }
        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .lightbox-caption {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        /* ===== STATS COUNTER ===== */
        .stats-section {
            background: #0a0a1a;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }
        .stat-item {
            text-align: center;
            padding: 2rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        .stat-item.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 500;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: #070714;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 4rem 2rem 0;
        }
        .footer-main {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .footer-brand-desc {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-top: 1rem;
            max-width: 350px;
        }
        .footer-social {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .footer-social a:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.4);
            color: #818cf8;
            transform: translateY(-3px);
        }
        .footer-col-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1.25rem;
        }
        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-col li {
            margin-bottom: 0.75rem;
        }
        .footer-col a {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .footer-col a:hover {
            color: #a5b4fc;
        }
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .footer-bottom p {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.85rem;
            margin: 0;
        }
        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }
        .footer-bottom-links a {
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s ease;
        }
        .footer-bottom-links a:hover {
            color: #a5b4fc;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }
        @keyframes ping {
            75%, 100% { transform: scale(2.5); opacity: 0; }
        }
        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .gallery-item:first-child {
                grid-column: span 2;
                grid-row: span 1;
            }
            .gallery-item:first-child img {
                min-height: 300px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-main {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero h1 { font-size: 2.2rem; }
            .hero-desc { font-size: 1rem; }
            .docs-grid {
                grid-template-columns: 1fr;
            }
            .prestasi-timeline::before { left: 1rem; }
            .prestasi-item, .prestasi-item:nth-child(even) {
                flex-direction: row !important;
                padding-left: 3rem;
            }
            .prestasi-content { width: 100%; }
            .prestasi-dot {
                left: 1rem;
            }
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            .gallery-item:first-child {
                grid-column: span 1;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            .footer-main {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .btn-hero-primary, .btn-hero-secondary {
                padding: 0.85rem 2rem;
                font-size: 0.95rem;
                width: 100%;
                text-align: center;
            }
        }

        /* ===== MOBILE NAV ===== */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
        }
        @media (max-width: 768px) {
            .mobile-menu-btn { display: block; }
            .nav-links { display: none; }
            .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 10, 26, 0.95);
                backdrop-filter: blur(20px);
                padding: 1rem 2rem 2rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="nav-wrapper" id="navbar">
        <div class="nav-inner">
            <a href="/" class="nav-brand">
                <div class="nav-brand-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="nav-brand-text">SILab</span>
            </a>
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation menu">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="nav-links" id="navLinks">
                <a href="#dokumentasi" class="nav-link">Dokumentasi</a>
                <a href="#prestasi" class="nav-link">Prestasi</a>
                <a href="#fasilitas" class="nav-link">Fasilitas</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-btn nav-btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-bg">
            <div class="hero-grid"></div>
        </div>
        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                Sistem Informasi Manajemen Inventaris Laboratorium Komputer
            </div>
            <h1>
                Laboratorium Komputer<br>
                <span class="gradient-text">Modern & Terintegrasi</span>
            </h1>
            <p class="hero-desc">
                Kelola inventaris, dokumentasikan kegiatan, dan pantau fasilitas laboratorium komputer dengan sistem manajemen digital yang cerdas dan efisien.
            </p>
            <div class="hero-actions">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-hero-primary">Buka Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn-hero-primary">Mulai Sekarang</a>
                    <a href="{{ route('login') }}" class="btn-hero-secondary">Masuk ke Akun</a>
                @endauth
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>
    </section>

    <!-- Stats Counter -->
    <section class="section stats-section">
        <div class="section-container">
            <div class="stats-grid">
                <div class="stat-item" data-animate>
                    <div class="stat-number" data-count="12">0</div>
                    <div class="stat-label">Ruang Laboratorium</div>
                </div>
                <div class="stat-item" data-animate>
                    <div class="stat-number" data-count="500">0</div>
                    <div class="stat-label">Perangkat Inventaris</div>
                </div>
                <div class="stat-item" data-animate>
                    <div class="stat-number" data-count="25">0</div>
                    <div class="stat-label">Penghargaan Diraih</div>
                </div>
                <div class="stat-item" data-animate>
                    <div class="stat-number" data-count="1200">0</div>
                    <div class="stat-label">Mahasiswa Terlayani</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dokumentasi Kegiatan Lab -->
    <section class="section dokumentasi-section" id="dokumentasi">
        <div class="section-container">
            <div class="section-header">
                <div class="section-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Dokumentasi
                </div>
                <h2 class="section-title">Kegiatan Laboratorium</h2>
                <p class="section-subtitle">Rangkuman aktivitas, pelatihan, dan kegiatan akademik yang berlangsung di laboratorium komputer kami.</p>
            </div>

            <div class="docs-grid">
                <!-- Card 1 -->
                <div class="doc-card" data-animate>
                    <div class="doc-card-img-wrapper">
                        <span class="doc-card-date">📅 15 Mei 2026</span>
                        <img src="{{ asset('images/landing/lab_activity_1.png') }}" alt="Workshop Pemrograman" class="doc-card-img" loading="lazy">
                    </div>
                    <div class="doc-card-body">
                        <span class="doc-card-tag">Workshop</span>
                        <h3>Workshop Pemrograman Web Modern</h3>
                        <p>Pelatihan intensif pengembangan web menggunakan teknologi terkini seperti Laravel, React, dan microservices. Diikuti oleh 80+ mahasiswa dari berbagai program studi.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="doc-card" data-animate>
                    <div class="doc-card-img-wrapper">
                        <span class="doc-card-date">📅 28 April 2026</span>
                        <img src="{{ asset('images/landing/lab_activity_2.png') }}" alt="Praktikum IoT" class="doc-card-img" loading="lazy">
                    </div>
                    <div class="doc-card-body">
                        <span class="doc-card-tag">Praktikum</span>
                        <h3>Praktikum Internet of Things (IoT)</h3>
                        <p>Sesi praktikum hands-on menggunakan sensor, mikrokontroler, dan platform IoT. Mahasiswa belajar membuat smart device dan sistem monitoring otomatis.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="doc-card" data-animate>
                    <div class="doc-card-img-wrapper">
                        <span class="doc-card-date">📅 10 Maret 2026</span>
                        <img src="{{ asset('images/landing/lab_activity_3.png') }}" alt="Seminar AI" class="doc-card-img" loading="lazy">
                    </div>
                    <div class="doc-card-body">
                        <span class="doc-card-tag">Seminar</span>
                        <h3>Seminar Kecerdasan Buatan & Machine Learning</h3>
                        <p>Seminar bersama pakar industri tentang implementasi AI dalam dunia kerja, dilengkapi demo proyek mahasiswa dan sesi tanya jawab interaktif.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Prestasi -->
    <section class="section prestasi-section" id="prestasi">
        <div class="section-container">
            <div class="section-header">
                <div class="section-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Prestasi
                </div>
                <h2 class="section-title">Prestasi & Pencapaian</h2>
                <p class="section-subtitle">Daftar penghargaan dan pencapaian membanggakan dari mahasiswa dan tim laboratorium kami.</p>
            </div>

            <div class="prestasi-timeline">
                <!-- Item 1 -->
                <div class="prestasi-item" data-animate>
                    <div class="prestasi-content">
                        <div class="prestasi-year">🗓️ 2026</div>
                        <div class="prestasi-icon">🏆</div>
                        <h3>Juara 1 Gemastik XVI</h3>
                        <p>Tim lab berhasil meraih juara pertama dalam Pagelaran Mahasiswa Nasional bidang Teknologi Informasi dan Komunikasi kategori Keamanan Siber.</p>
                        <span class="prestasi-medal medal-gold">🥇 Gold Medal</span>
                    </div>
                    <div class="prestasi-dot"></div>
                </div>

                <!-- Item 2 -->
                <div class="prestasi-item" data-animate>
                    <div class="prestasi-content">
                        <div class="prestasi-year">🗓️ 2025</div>
                        <div class="prestasi-icon">🎖️</div>
                        <h3>Best Paper Award - ICSEC 2025</h3>
                        <p>Paper penelitian tentang optimisasi jaringan kampus menggunakan SDN mendapat penghargaan Best Paper pada konferensi internasional ICSEC.</p>
                        <span class="prestasi-medal medal-champion">🏅 Best Paper</span>
                    </div>
                    <div class="prestasi-dot"></div>
                </div>

                <!-- Item 3 -->
                <div class="prestasi-item" data-animate>
                    <div class="prestasi-content">
                        <div class="prestasi-year">🗓️ 2025</div>
                        <div class="prestasi-icon">🥈</div>
                        <h3>Runner Up Hackathon Nasional</h3>
                        <p>Tim mahasiswa lab komputer berhasil menjadi runner up dalam Hackathon Nasional dengan solusi Smart Campus berbasis IoT dan Machine Learning.</p>
                        <span class="prestasi-medal medal-silver">🥈 Silver Medal</span>
                    </div>
                    <div class="prestasi-dot"></div>
                </div>

                <!-- Item 4 -->
                <div class="prestasi-item" data-animate>
                    <div class="prestasi-content">
                        <div class="prestasi-year">🗓️ 2024</div>
                        <div class="prestasi-icon">🏅</div>
                        <h3>Juara 3 Kompetisi Web Design</h3>
                        <p>Mahasiswa dari Laboratorium Web Development berhasil meraih juara 3 pada ajang kompetisi desain web tingkat nasional dengan UI/UX inovatif.</p>
                        <span class="prestasi-medal medal-bronze">🥉 Bronze Medal</span>
                    </div>
                    <div class="prestasi-dot"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fasilitas Lab Gallery -->
    <section class="section fasilitas-section" id="fasilitas">
        <div class="section-container">
            <div class="section-header">
                <div class="section-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Galeri
                </div>
                <h2 class="section-title">Fasilitas Laboratorium</h2>
                <p class="section-subtitle">Laboratorium kami dilengkapi dengan peralatan modern dan lingkungan belajar yang kondusif untuk mendukung pembelajaran optimal.</p>
            </div>

            <div class="gallery-grid">
                <!-- Large Item -->
                <div class="gallery-item" data-animate data-lightbox data-caption="Lab Komputer Utama - Dilengkapi 40 unit PC spesifikasi tinggi">
                    <img src="{{ asset('images/landing/lab_facility_1.png') }}" alt="Lab Komputer Utama" loading="lazy">
                    <div class="gallery-overlay">
                        <div class="gallery-overlay-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                        <h4>Lab Komputer Utama</h4>
                        <p>40 unit PC spesifikasi tinggi dengan monitor dual</p>
                    </div>
                </div>

                <!-- Small Item 1 -->
                <div class="gallery-item" data-animate data-lightbox data-caption="Lab Elektronika - Peralatan lengkap untuk praktikum hardware">
                    <img src="{{ asset('images/landing/lab_facility_2.png') }}" alt="Lab Elektronika" loading="lazy">
                    <div class="gallery-overlay">
                        <div class="gallery-overlay-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                        <h4>Lab Elektronika</h4>
                        <p>Peralatan praktikum lengkap</p>
                    </div>
                </div>

                <!-- Small Item 2 -->
                <div class="gallery-item" data-animate data-lightbox data-caption="Lab Jaringan - Server rack dan perangkat networking profesional">
                    <img src="{{ asset('images/landing/lab_facility_3.png') }}" alt="Lab Jaringan" loading="lazy">
                    <div class="gallery-overlay">
                        <div class="gallery-overlay-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                        <h4>Lab Jaringan</h4>
                        <p>Server rack & networking profesional</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="footer-main">
            <div class="footer-brand">
                <a href="/" class="nav-brand" style="margin-bottom: 0.5rem;">
                    <div class="nav-brand-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="nav-brand-text">SILab</span>
                </a>
                <p class="footer-brand-desc">
                    Sistem informasi manajemen inventaris laboratorium komputer terpadu. Memudahkan pengelolaan aset, peminjaman peralatan, dan booking ruangan secara digital.
                </p>
                <div class="footer-social">
                    <a href="https://www.instagram.com/trpl.polinela" aria-label="Instagram">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="https://www.youtube.com/@trplpolinela" aria-label="YouTube">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a href="#" aria-label="GitHub">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Navigasi</h4>
                <ul>
                    <li><a href="#hero">Beranda</a></li>
                    <li><a href="#dokumentasi">Dokumentasi</a></li>
                    <li><a href="#prestasi">Prestasi</a></li>
                    <li><a href="#fasilitas">Fasilitas</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Layanan</h4>
                <ul>
                    <li><a href="{{ Route::has('login') ? route('login') : '#' }}">Peminjaman Barang</a></li>
                    <li><a href="{{ Route::has('login') ? route('login') : '#' }}">Booking Ruangan</a></li>
                    <li><a href="{{ Route::has('login') ? route('login') : '#' }}">Katalog Inventaris</a></li>
                    <li><a href="{{ Route::has('login') ? route('login') : '#' }}">Laporan Online</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Kontak</h4>
                <ul>
                    <li><a href="#">📍 Kampus Politeknik Negeri Lampung, Jl. Soekarno-Hatta No.10, Bandar Lampung, Lampung, Indonesia. 35141</a></li>
                    <li><a href="#">📞 (021) 123-4567</a></li>
                    <li><a href="#">✉️ trpl@polinela.ac.id</a></li>
                    <li><a href="#">🕐 Sen-Jum, 08:00-17:00</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} SILab TRPL- Sistem Informasi Manajemen Inventaris Laboratorium Komputer. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" id="lightboxClose" aria-label="Close lightbox">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <img src="" alt="Lightbox image" id="lightboxImg">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    navLinks.classList.remove('active');
                }
            });
        });

        // Scroll-triggered animations with IntersectionObserver
        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Add staggered delay for siblings
                    const siblings = entry.target.parentElement.querySelectorAll('[data-animate]');
                    let delay = 0;
                    siblings.forEach((sib, i) => {
                        if (sib === entry.target) delay = i * 150;
                    });
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, delay);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));

        // Counter animation
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    const counter = entry.target.querySelector('[data-count]');
                    if (counter) {
                        const target = parseInt(counter.dataset.count);
                        const duration = 2000;
                        const start = performance.now();

                        function updateCounter(currentTime) {
                            const elapsed = currentTime - start;
                            const progress = Math.min(elapsed / duration, 1);
                            // Ease out quad
                            const easeOut = 1 - (1 - progress) * (1 - progress);
                            const current = Math.floor(easeOut * target);
                            counter.textContent = current.toLocaleString() + (target >= 100 ? '+' : '');
                            if (progress < 1) requestAnimationFrame(updateCounter);
                        }
                        requestAnimationFrame(updateCounter);
                    }
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-item').forEach(el => counterObserver.observe(el));

        // Lightbox
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightboxImg');
        const lightboxCaption = document.getElementById('lightboxCaption');
        const lightboxClose = document.getElementById('lightboxClose');

        document.querySelectorAll('[data-lightbox]').forEach(item => {
            item.addEventListener('click', () => {
                const img = item.querySelector('img');
                const caption = item.dataset.caption || '';
                lightboxImg.src = img.src;
                lightboxCaption.textContent = caption;
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        lightboxClose.addEventListener('click', (e) => {
            e.stopPropagation();
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        });

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                lightbox.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>
