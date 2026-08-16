<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - SIM Kurikulum SMK PGRI Blora</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64x64.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">

    <!-- Type: Fraunces (buku induk / ijazah serif) + Plus Jakarta Sans (isian formulir) + IBM Plex Mono (label & nomor) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,450;9..144,560;9..144,650&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root{
            --ink:#252A20;
            --paper:#F7F1DE;
            --paper-line:#E7DCB8;
            --green-950:#122417;
            --green-900:#183823;
            --green-800:#204E31;
            --green-700:#2B6640;
            --brass:#C6A052;
            --brass-light:#E8D8A2;
            --stamp:#9C4033;
        }
        .auth-page{
            font-family:'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            color:var(--ink);
            background-color:var(--green-900);
            background-image:
                repeating-linear-gradient(135deg, rgba(255,255,255,0.025) 0 1px, transparent 1px 9px),
                radial-gradient(ellipse 90% 60% at 50% -10%, rgba(198,160,82,0.16), transparent 60%),
                linear-gradient(160deg, var(--green-950) 0%, var(--green-900) 45%, var(--green-800) 100%);
            min-height:100vh;
        }
        .auth-font-display{ font-family:'Fraunces', ui-serif, Georgia, serif; }
        .auth-font-mono{ font-family:'IBM Plex Mono', ui-monospace, monospace; }

        /* Batik-inspired corner motif — very quiet, just a texture cue, not a costume */
        .auth-motif{
            position:absolute; inset:0; pointer-events:none; overflow:hidden;
        }
        .auth-motif svg{ position:absolute; opacity:0.07; }

        /* Eyebrow ribbon */
        .auth-eyebrow{
            letter-spacing:0.22em;
        }

        /* Program-studi tags styled like sewn fabric labels (nod to jurusan Tata Busana) */
        .auth-tag{
            position:relative;
            font-family:'IBM Plex Mono', ui-monospace, monospace;
            font-size:0.65rem;
            letter-spacing:0.08em;
            color:var(--green-950);
            background:var(--brass-light);
            padding:0.3rem 0.65rem 0.3rem 0.9rem;
            clip-path:polygon(10px 0, 100% 0, 100% 100%, 10px 100%, 0 50%);
        }

        /* Perforated seam between header and the paper card — like a tear-off document stub */
        .auth-perforation{
            position:relative;
            height:18px;
            z-index:2;
            background-image: radial-gradient(circle at 10px 9px, transparent 7.5px, var(--paper) 8px);
            background-repeat:repeat-x;
            background-size:20px 18px;
            background-position:center;
        }

        /* Paper card: faint ruled lines, like notebook / rapor paper */
        .auth-card{
            position:relative;
            background-color:var(--paper);
            background-image:repeating-linear-gradient(var(--paper) 0 34px, var(--paper-line) 34px 35px);
            background-position:0 6px;
            box-shadow:0 30px 60px -20px rgba(10,20,10,0.55), 0 2px 0 rgba(0,0,0,0.04);
        }

        /* Official round stamp — the signature element */
        .auth-stamp{
            filter:drop-shadow(0 3px 6px rgba(0,0,0,0.35));
            transform:rotate(-9deg);
            transition:transform 0.5s cubic-bezier(.2,.8,.2,1);
        }
        .auth-stamp-wrap:hover .auth-stamp{ transform:rotate(-4deg) scale(1.03); }

        @media (prefers-reduced-motion: reduce){
            .auth-stamp{ transition:none; }
        }

        /* Underline-style form fields — filling a paper form, not a SaaS input box */
        .auth-field input[type="text"],
        .auth-field input[type="password"]{
            background:transparent;
            border:none;
            border-bottom:1.5px solid var(--paper-line);
            border-radius:0;
            padding:0.55rem 0.1rem;
            font-size:0.975rem;
            color:var(--ink);
            transition:border-color .2s ease, box-shadow .2s ease;
        }
        .auth-field input[type="text"]:focus,
        .auth-field input[type="password"]:focus{
            outline:none;
            box-shadow:none;
            border-bottom-color:var(--stamp);
        }
        .auth-field input::placeholder{ color:#A69E82; }
        .auth-field-label{
            font-family:'IBM Plex Mono', ui-monospace, monospace;
            font-size:0.68rem;
            letter-spacing:0.1em;
            color:#6B6A4E;
        }

        .auth-btn{
            background:linear-gradient(180deg, var(--green-700), var(--green-800));
            box-shadow:0 10px 24px -8px rgba(20,50,30,0.55), inset 0 1px 0 rgba(255,255,255,0.08);
        }
        .auth-btn:hover{ background:linear-gradient(180deg, #2f7247, var(--green-700)); }
        .auth-btn:focus-visible{ outline:2px solid var(--brass); outline-offset:2px; }
    </style>
</head>
<body class="auth-page flex items-center justify-center p-4 relative">

    <div class="auth-motif" aria-hidden="true">
        <svg width="420" height="420" style="top:-120px; left:-140px;" viewBox="0 0 100 100">
            <g fill="none" stroke="var(--brass-light)" stroke-width="0.5">
                <circle cx="50" cy="50" r="14"/><circle cx="50" cy="50" r="24"/><circle cx="50" cy="50" r="34"/>
                <path d="M50 16 L50 84 M16 50 L84 50 M27 27 L73 73 M73 27 L27 73"/>
            </g>
        </svg>
        <svg width="380" height="380" style="bottom:-130px; right:-130px;" viewBox="0 0 100 100">
            <g fill="none" stroke="var(--brass-light)" stroke-width="0.5">
                <circle cx="50" cy="50" r="14"/><circle cx="50" cy="50" r="24"/><circle cx="50" cy="50" r="34"/>
                <path d="M50 16 L50 84 M16 50 L84 50 M27 27 L73 73 M73 27 L27 73"/>
            </g>
        </svg>
    </div>

    <div class="w-full max-w-md relative z-10">

        <!-- Header: wordmark + official stamp -->
        <div class="text-center mb-2 px-2">
            <p class="auth-eyebrow auth-font-mono text-[10px] text-[color:var(--brass-light)] uppercase mb-3">
                Sistem Informasi &middot; Manajemen Kurikulum
            </p>

            <div class="auth-stamp-wrap inline-block mb-4">
                <svg class="auth-stamp" width="92" height="92" viewBox="0 0 100 100">
                    <defs>
                        <path id="stampCirclePath" d="M50,10 a40,40 0 1,1 -0.1,0" fill="none"/>
                    </defs>
                    <circle cx="50" cy="50" r="46" fill="none" stroke="var(--brass)" stroke-width="1.4"/>
                    <circle cx="50" cy="50" r="40" fill="none" stroke="var(--brass)" stroke-width="1" stroke-dasharray="1.6 2.4"/>
                    <text font-family="IBM Plex Mono, monospace" font-size="6.3" letter-spacing="1.5" fill="var(--brass)">
                        <textPath href="#stampCirclePath" startOffset="2%">
                            SMK PGRI BLORA &bull; SIM KURIKULUM &bull;
                        </textPath>
                    </text>
                    <g transform="translate(50,52)" stroke="var(--brass)" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M-13,-6 L0,-1 L13,-6 L13,7 L0,12 L-13,7 Z"/>
                        <path d="M0,-1 L0,12"/>
                        <path d="M-13,-6 L0,-11 L13,-6"/>
                    </g>
                </svg>
            </div>

            <h1 class="auth-font-display text-3xl text-[color:var(--paper)] mb-1" style="font-weight:560;">
                SIM Kurikulum
            </h1>
            <p class="text-sm" style="color:var(--brass-light);">SMK PGRI Blora</p>

            <div class="flex items-center justify-center gap-1.5 mt-4">
                <span class="auth-tag">MPLB</span>
                <span class="auth-tag">AKL</span>
                <span class="auth-tag">BUSANA</span>
            </div>
        </div>

        <div class="auth-perforation" aria-hidden="true"></div>

        <!-- Main Content -->
        <div class="auth-card rounded-b-lg px-7 pt-7 pb-8">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="text-center mt-7 text-xs" style="color:var(--brass-light);">
            <p>&copy; {{ date('Y') }} SMK PGRI Blora &middot; Sistem Informasi Manajemen Kurikulum</p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
