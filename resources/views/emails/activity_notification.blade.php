<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title ?? 'Notifikasi Aktiviti e-JPVNK' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
            color: #1e293b;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
            color: #ffffff;
            padding: 28px 24px;
            text-align: center;
        }
        .header-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #a7f3d0;
            margin-bottom: 4px;
        }
        .header-sub {
            font-size: 18px;
            font-weight: 900;
            color: #ffffff;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .body-content {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
        .badge-kursus { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-klinik { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-eptr { background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-pawah { background-color: #f5f3ff; color: #5b21b6; border: 1px solid #ddd6fe; }
        .badge-naimbif { background-color: #fdf2f8; color: #9d174d; border: 1px solid #fbcfe8; }
        .badge-epu { background-color: #fff7ed; color: #9a3412; border: 1px solid #ffedd5; }
        .badge-inventori { background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-kenderaan { background-color: #f0fdfa; color: #115e59; border: 1px solid #99f6e4; }
        .badge-media { background-color: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .badge-sistem { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

        .card-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .card-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .card-message {
            font-size: 14px;
            color: #334155;
            margin: 0;
            line-height: 1.7;
        }
        .timestamp {
            font-size: 12px;
            color: #64748b;
            margin-top: 12px;
            display: block;
        }

        /* Certificate Special Box */
        .cert-box {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px dashed #d97706;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
        }
        .cert-icon {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .cert-heading {
            font-size: 18px;
            font-weight: 900;
            color: #78350f;
            margin: 0 0 6px 0;
        }
        .cert-sub {
            font-size: 13px;
            color: #92400e;
            margin: 0 0 18px 0;
        }
        .btn-cert {
            display: inline-block;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff !important;
            font-weight: 800;
            font-size: 14px;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.4);
            letter-spacing: 0.3px;
        }
        .btn-cert:hover {
            background: #92400e;
        }

        /* Standard CTA Button */
        .btn-action {
            display: inline-block;
            background: #059669;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            margin-top: 12px;
        }
        .btn-action:hover {
            background: #047857;
        }

        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        .footer-org {
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">Kerajaan Negeri Kelantan</div>
            <div class="header-sub">Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
        </div>

        <!-- Body Content -->
        <div class="body-content">
            <div class="greeting">
                Assalamualaikum & Salam Sejahtera, {{ $user->name ?? 'Pengguna' }},
            </div>

            <p style="margin-top: 0; color: #475569; font-size: 14px;">
                Terdapat aktiviti atau kemaskini terkini dalam akaun sistem <strong>e-JPVNK</strong> anda:
            </p>

            <!-- Card Box -->
            <div class="card-box">
                @php
                    $typeClass = match ($notification->type ?? 'sistem') {
                        'kursus' => 'badge-kursus',
                        'klinik' => 'badge-klinik',
                        'eptr' => 'badge-eptr',
                        'pawah' => 'badge-pawah',
                        'naimbif' => 'badge-naimbif',
                        'epu' => 'badge-epu',
                        'inventori' => 'badge-inventori',
                        'kenderaan' => 'badge-kenderaan',
                        'media' => 'badge-media',
                        default => 'badge-sistem',
                    };
                    $typeLabel = match ($notification->type ?? 'sistem') {
                        'kursus' => 'Modul Kursus & Latihan',
                        'klinik' => 'Modul Klinik Haiwan',
                        'eptr' => 'Modul Ternakan (e-PTR)',
                        'pawah' => 'Modul Program Pawah',
                        'naimbif' => 'Modul Program NAIMbif',
                        'epu' => 'Modul Unggas (e-PU)',
                        'inventori' => 'Modul Inventori & Stor',
                        'kenderaan' => 'Modul Tempahan Kenderaan',
                        'media' => 'Modul Tempahan Media',
                        default => 'Notifikasi Sistem',
                    };
                @endphp

                <span class="badge {{ $typeClass }}">{{ $typeLabel }}</span>
                <h3 class="card-title">{{ $notification->title }}</h3>
                <p class="card-message">{{ $notification->message }}</p>
                <span class="timestamp">
                    🕒 {{ $notification->created_at ? $notification->created_at->translatedFormat('d F Y, h:i A') : now()->translatedFormat('d F Y, h:i A') }}
                </span>
            </div>

            <!-- If Course Certificate Notification -->
            @if($isCertificate && $certificateUrl)
                <div class="cert-box">
                    <div class="cert-icon">📜</div>
                    <h4 class="cert-heading">Sijil Penyertaan Digital Rasmi</h4>
                    <p class="cert-sub">
                        Tahniah atas kehadiran anda! Sijil digital rasmi telah disahkan dan sedia untuk dimuat turun atau dicetak secara terus.
                    </p>
                    <a href="{{ $certificateUrl }}" target="_blank" class="btn-cert">
                        📥 Muat Turun / Cetak Sijil Digital (PDF)
                    </a>
                </div>
            @elseif(!empty($notification->action_url))
                <div style="text-align: center; margin-top: 24px; margin-bottom: 12px;">
                    <a href="{{ $notification->action_url }}" target="_blank" class="btn-action">
                        👉 Papar Butiran & Tindakan Seterusnya
                    </a>
                </div>
            @endif

            <p style="font-size: 13px; color: #64748b; margin-top: 24px; line-height: 1.5;">
                Sekiranya anda mempunyai sebarang pertanyaan atau memerlukan bantuan lanjut, sila hubungi pentadbir modul berkaitan di Pejabat Perkhidmatan Veterinar Negeri Kelantan.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-org">Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
            <div>Jalan Kubang Kerian, 16150 Kota Bharu, Kelantan Darul Naim</div>
            <div style="margin-top: 8px; font-size: 11px; color: #94a3b8;">
                Emel ini dijana secara automatik oleh Sistem e-JPVNK. Sila jangan balas terus ke alamat emel ini.
            </div>
        </div>
    </div>
</body>
</html>
