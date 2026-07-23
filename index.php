<?php

$users = [
    ['nama' => 'ADE RUA NUR LEMONIAR, S.Pd',       'user' => 'Aderuanurlemoniar95@gmail.com',    'password' => 'haeehcsn'],
    ['nama' => 'ADELA WULAN K., S.PD.',             'user' => 'kurniawanalfarizki84@gmail.com',   'password' => 'swhnngbu'],
    ['nama' => 'ARI YUNITASARI, S.Pd',              'user' => 'ariyunitasari@yahoo.co.id',        'password' => 'emstzu0v'],
    ['nama' => 'BUDI SISWANTO, S.Pd.I',             'user' => 'budisiswanto120@gmail.com',        'password' => 'mnluatkl'],
    ['nama' => 'DEBBY FURY WIJAYANTI, S.PD',        'user' => 'debbyfury@gmail.com',              'password' => 'ugfsqqob'],
    ['nama' => 'DEWI WARTINI, S.Pd',                'user' => 'wartinidewi10@yahoo.com',          'password' => 'jgqi1sl2'],
    ['nama' => 'DHANI KISWORO JATI, S.Pd',          'user' => 'dhanikiswono24@gmail.com',         'password' => 'kdbsxmr9'],
    ['nama' => 'Drs. SUSENO',                       'user' => 'gelandangsukma@gmail.com',         'password' => 'drbltakw'],
    ['nama' => 'ERVINDA SEKAR ASMARA, S.PD.',       'user' => 'ervindasekar07@gmail.com',         'password' => 'kvx769m3'],
    ['nama' => 'ILHAM HARDIYAN PRABOWO, S.Pd',      'user' => 'ilhamm979@gmail.com',              'password' => 'mh8ecugs'],
    ['nama' => 'LILIYANA AYU WIDYANINGRUM, S.Pd',   'user' => 'Liliyanaayuw@gmail.com',           'password' => 'mqpbi0ws'],
    ['nama' => 'MUHAMMAD HUDA MUTTAQIN, S.Pd.I',    'user' => 'muochgack2@gmail.com',             'password' => 'custom'],
    ['nama' => 'MUNISAH, S.Pd',                     'user' => 'munisah234@gmail.com',             'password' => 'muttkm3t'],
    ['nama' => 'PANCAWATI PUJI LESTARI, A.Md',      'user' => 'pancawatipujlestari@yahoo.com',    'password' => 'ivw01cgh'],
    ['nama' => 'NIA DANI RAHAYU, S.Pd',             'user' => 'niadanirahayu@gmail.com',          'password' => 'eimn5uyp'],
    ['nama' => 'TRI MULYANININGSIH, S.E',            'user' => 'inay_tri96@yahoo.com',             'password' => 'gnniiyo1'],
    ['nama' => 'WIWIT MERGI WIJAYANTI, A.MD.',      'user' => 'mergiwijayanti@gmail.com',         'password' => '771p102z'],
    ['nama' => 'YULLY SETYO ANGRENGGANI, S.Pd',     'user' => 'yullysang973@gmail.com',           'password' => 'ngtm1hfo'],
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User & Password Erapor 8 - SMK PGRI BLORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background: #eef2f7;
            color: #2d3748;
            padding: 40px 20px;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
        }

        /* ── HEADER ── */
        .header {
            background: linear-gradient(135deg, #1a3a6c 0%, #2563a8 60%, #1e88e5 100%);
            border-radius: 16px;
            padding: 44px 40px 36px;
            margin-bottom: 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(26,58,108,0.30);
        }

        .header::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -40px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .header-label {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            color: #bfdbfe;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            padding: 5px 16px;
            border-radius: 20px;
            margin-bottom: 18px;
            border: 1px solid rgba(255,255,255,0.20);
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            text-shadow: 0 2px 12px rgba(0,0,0,0.18);
        }

        .header h1 span {
            color: #93c5fd;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #93c5fd, #fff);
            border-radius: 2px;
            margin: 14px auto;
        }

        .header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #dbeafe;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .table-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #2b6cb0;
            color: #fff;
        }

        thead th {
            padding: 14px 18px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #ebf4ff;
        }

        tbody tr:nth-child(even) {
            background: #f7fafc;
        }

        tbody tr:nth-child(even):hover {
            background: #ebf4ff;
        }

        td {
            padding: 12px 18px;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        td:first-child {
            font-weight: 600;
            color: #1a365d;
            width: 35px;
            text-align: center;
        }

        .password-cell {
            font-family: 'Courier New', monospace;
            background: #edf2f7;
            border-radius: 4px;
            padding: 4px 8px;
            display: inline-block;
            font-size: 0.85rem;
            color: #2d3748;
        }

        .email {
            color: #2b6cb0;
        }

        .total {
            text-align: right;
            margin-top: 14px;
            font-size: 0.85rem;
            color: #718096;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            body {
                padding: 16px 12px;
            }

            .header {
                padding: 30px 20px 24px;
                border-radius: 12px;
            }

            .header h1 {
                font-size: 1.45rem;
            }

            .header h2 {
                font-size: 1rem;
                letter-spacing: 2px;
            }

            .header-label {
                font-size: 0.68rem;
                letter-spacing: 1.8px;
            }

            /* Sembunyikan tabel biasa, tampilkan sebagai card */
            .table-wrapper {
                background: transparent;
                box-shadow: none;
                overflow: visible;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tbody tr {
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.07);
                margin-bottom: 12px;
                border-bottom: none;
                padding: 14px 16px;
                position: relative;
            }

            tbody tr:hover {
                background: #fff;
            }

            tbody tr:nth-child(even) {
                background: #fff;
            }

            /* Nomor urut pojok kanan atas */
            td:first-child {
                position: absolute;
                top: 12px;
                right: 14px;
                width: auto;
                text-align: right;
                font-size: 0.75rem;
                color: #a0aec0;
                font-weight: 600;
            }

            td {
                padding: 3px 0;
                font-size: 0.88rem;
                border: none;
            }

            /* Label pseudo */
            td:nth-child(2)::before { content: '👤 '; font-style: normal; }
            td:nth-child(3)::before { content: '✉️ '; font-style: normal; }
            td:nth-child(4)::before { content: '🔑 '; font-style: normal; }

            td:nth-child(2) {
                font-weight: 700;
                font-size: 0.95rem;
                color: #1a365d;
                margin-bottom: 4px;
                padding-right: 40px;
            }

            td:nth-child(3) {
                word-break: break-all;
            }

            .password-cell {
                font-size: 0.82rem;
            }

            .total {
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-label">Data Akun Sistem</div>
        <h1>Daftar User &amp; Password <span>Erapor 8</span></h1>
        <div class="divider"></div>
        <h2>SMK PGRI Blora</h2>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Username / Email</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $u): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($u['nama']) ?></td>
                    <td class="email"><?= htmlspecialchars($u['user']) ?></td>
                    <td><span class="password-cell"><?= htmlspecialchars($u['password']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="total">Total: <?= count($users) ?> pengguna</p>
</div>
</body>
</html>
