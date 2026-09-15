<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjanjian Lembu Pawah - {{ $perjanjian->no_perjanjian }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400&display=swap');

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, 'Roboto', sans-serif;
            color: #000000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        .page-sheet {
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            max-height: 297mm;
            padding: 18mm 20mm 16mm 20mm;
            margin: 0 auto 25px auto;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            font-size: 10.2pt;
            line-height: 1.48;
        }

        .clause-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 11px;
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.46;
        }

        .clause-num {
            width: 28px;
            flex-shrink: 0;
            font-weight: normal;
        }

        .clause-text {
            flex: 1;
        }

        .dotted-fill {
            border-bottom: 1px dotted #000000;
            font-weight: bold;
            padding: 0 4px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .page-sheet {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 18mm 20mm 16mm 20mm !important;
                box-shadow: none !important;
                border: none !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .page-sheet:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body class="py-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-[210mm] mx-auto mb-6 px-4 py-3.5 flex flex-wrap items-center justify-between gap-3 bg-white rounded-2xl shadow-md border border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                <i class="fa-solid fa-file-contract text-lg"></i>
            </div>
            <div>
                <h1 class="font-bold text-slate-800 text-sm sm:text-base">Surat Perjanjian Lembu Pawah (Rasmi)</h1>
                <p class="text-xs text-slate-500 font-mono">No. Perjanjian: {{ $perjanjian->no_perjanjian }} | 4 Halaman Lengkap (Muat Sempurna A4)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Simpan PDF (4 Halaman A4)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    @php
        $firstTernakan = $perjanjian->ternakanList->first();
        $namaPeserta = $perjanjian->peserta->name ?? null;
        $noKpPeserta = $perjanjian->peserta->ic_number ?? null;
        $alamatPeserta = $perjanjian->peserta->address ?? null;
        if ($alamatPeserta && $perjanjian->jajahan && !str_contains(strtolower($alamatPeserta), strtolower($perjanjian->jajahan))) {
            $alamatPeserta .= ', ' . $perjanjian->jajahan;
        }

        $jajahanText = $perjanjian->jajahan ? ('Jajahan ' . $perjanjian->jajahan) : null;
        $tarikhMulaText = $perjanjian->tarikh_mula ? $perjanjian->tarikh_mula->translatedFormat('d F Y') : null;

        $umurLembu = $firstTernakan ? ($firstTernakan->umur ?? '2') : ($perjanjian->tempoh_tahun ? (string)$perjanjian->tempoh_tahun : null);
        $hargaSemasa = '3,500.00';
        $jenisBaka = $firstTernakan ? ($firstTernakan->baka ?? $perjanjian->jenis_pawah) : ($perjanjian->jenis_pawah ?? 'Lembu Hibrid');
        $warnaLembu = $firstTernakan ? ($firstTernakan->warna ?? 'Perang / Hitam') : 'Perang / Hitam';
        $noTagTelinga = $firstTernakan ? ($firstTernakan->no_tag ?? $perjanjian->no_perjanjian) : $perjanjian->no_perjanjian;
    @endphp

    <!-- ========================================================= -->
    <!-- HALAMAN 1 -->
    <!-- ========================================================= -->
    <div class="page-sheet">
        <!-- Header dengan Dua Jata Kerajaan -->
        <div class="flex items-center justify-between mb-3.5">
            <div class="w-24 flex justify-start">
                <img src="{{ asset('images/jata-negara.svg') }}" alt="Jata Negara" class="h-16 w-auto object-contain" onerror="this.style.display='none'">
            </div>
            <div class="text-center flex-1 px-2">
                <h2 class="text-[12pt] font-bold uppercase tracking-wide leading-tight text-black">JABATAN PERKHIDMATAN VETERINAR</h2>
                <h2 class="text-[12pt] font-bold uppercase tracking-wide leading-tight text-black">NEGERI KELANTAN</h2>
                <h1 class="text-[11.5pt] font-bold uppercase tracking-wider text-black mt-2">SURAT PERJANJIAN LEMBU PAWAH</h1>
            </div>
            <div class="w-24 flex justify-end">
                <img src="{{ asset('images/jata-kelantan.png') }}" alt="Jata Negeri Kelantan" class="h-16 w-auto object-contain" onerror="this.src='{{ asset('images/logo.png') }}'">
            </div>
        </div>

        <!-- Perenggan Permulaan (Preamble 1) -->
        <div class="text-justify leading-relaxed mb-3.5">
            Surat perjanjian ini adalah dibuat di Pejabat Perkhidmatan Veterinar 
            <span class="dotted-fill">{{ $jajahanText ?? '.....................................................' }}</span>
            Pada <span class="dotted-fill">{{ $tarikhMulaText ?? '....................................................' }}</span>
            di antara Kerajaan Malaysia/Negeri Kelantan (selepas di panggil Kerajaan) bagi satu pihak dan 
            <span class="dotted-fill">{{ $namaPeserta ?? '........................................................................................................................................' }}</span>
            Kad Pengenalan Nombor <span class="dotted-fill">{{ $noKpPeserta ?? '.........................................................' }}</span>
            Alamat <span class="dotted-fill">{{ $alamatPeserta ?? '................................................................................................................................................................' }}</span>
            (selepas ini dipanggil Pemawah) di pihak yang lain.
        </div>

        <!-- Perenggan Butiran Lembu (Preamble 2) -->
        <div class="text-justify leading-relaxed mb-3.5">
            BAHAWA pihak Kerajaan ada mempunyai seekor lembu betina berumur lebih kurang 
            <span class="dotted-fill">{{ $umurLembu ?? '.....................................................' }}</span> 
            tahun dengan nilai Harga Semasa RM <span class="dotted-fill">{{ $hargaSemasa }}</span>, jenis 
            <span class="dotted-fill">{{ $jenisBaka ?? '..................................' }}</span>, warnanya 
            <span class="dotted-fill">{{ $warnaLembu ?? '......................................' }}</span> bertanda cacah bilangan 
            <span class="dotted-fill">{{ $noTagTelinga ?? '..............................................' }}</span> di sebelah telinga kanan 
            (menandakan yang lembu itu adalah hak, milik dan harta Kerajaan) dan Kerajaan bersetuju memawahkan lembu itu kepada pemawah mengikut PERJANJIAN DAN SYARAT-SYARAT yang tersiar di bawah ini.
        </div>

        <!-- Nota Wakil Pengarah -->
        <div class="text-justify leading-relaxed mb-3.5">
            Untuk maksud Perjanjian ini, ada pun 'Wakil Pengarah Perkhidmatan Veterinar Negeri ialah terdiri dari Pegawai Veterinar, Timbalan Pegawai Veterinar, Penolong Pegawai Veterinar dan Pembantu Veterinar yang menjaga daerah-daerah yang tertentu di bawah pentadbiran Pengarah Perkhidmatan Veterinar Negeri tersebut.
        </div>

        <!-- Deklarasi Persetujuan -->
        <div class="font-normal mb-2.5">
            MAKA adalah dipersetujui seperti tersebut :
        </div>

        <!-- Fasal 1 hingga 5 -->
        <div class="space-y-2">
            <div class="clause-item">
                <div class="clause-num">(1)</div>
                <div class="clause-text">
                    Bahawa Pemawah selepas menandatangani Surat Perjanjian ini adalah mengakui bahawa beliau adalah ingin dan telah pun menerima lembu tersebut di atas. Pemawah adalah bersetuju mematuhi semua syarat-syarat atau perjanjian-perjanjian yang terkandung di dalamnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(2)</div>
                <div class="clause-text">
                    Bahawa Pemawah akan memelihara lembu serta anak-anak yang dilahirkan semasa Perjanjian ini dikuatkuasakan dengan perbelanjaannya sendiri dengan keadaan yang baik dan memuaskan, mengikut nasihat Pengarah Perkhidmatan Veterinar Negeri atau wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(3)</div>
                <div class="clause-text">
                    Lembu yang tersebut di atas serta semua anak-anaknya yang dilahirkan semasa Perjanjian ini berkuatkuasa adalah hak, milik dan harta Kerajaan dan pemawah tidaklah boleh menjual atau memawahkannya kepada orang lain atau pun membuat apa-apa ikatan dengannya/mereka melainkan terlebih dahulu mendapat kebenaran bertulis daripada Pengarah Perkhidmatan Veterinar Negeri atau wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(4)</div>
                <div class="clause-text">
                    Perjanjian ini akan berkuatkuasa sehingga satu masa nanti apabila Pemawah dapat menyerahkan kepada Kerajaan seekor anak daripada lembu itu, sama ada jantan atau betina, berumur kira-kira dua tahun dan ke atas, yang berkeadaan sihat, sempurna dan memuaskan mengikut pendapat Pengarah Perkhidmatan Veterinar Negeri atau wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(5)</div>
                <div class="clause-text">
                    Setelah pemawah menyerahkan anak lembu itu tadi, seperti bab (4) di atas, selepas mana perjanjian ini pun dibatalkan, maka pemawah adalah berhak memiliki lembu yang dipawahkan itu, juga kesemua anak-anaknya yang tinggal, jika ada, dan Kerajaan tidaklah berhak menuntut apa-apa daripada penternak.
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- HALAMAN 2 -->
    <!-- ========================================================= -->
    <div class="page-sheet">
        <!-- Fasal 6 hingga 13 -->
        <div class="space-y-2.5 pt-1">
            <div class="clause-item">
                <div class="clause-num">(6)</div>
                <div class="clause-text">
                    Kesemua anak-anak yang dilahirkan oleh lembu yang dipawahkan semasa Perjanjian ini berkuatkuasa hendaklah dipelihara oleh Pemawah dengan keadaan yang baik, sempurna dan memuaskan. Semua anak-anak lembu itu akan ditanda cacah di telinga kanannya menandakan yang anak-anak lembu itu adalah hak, milik dan harta Kerajaan semasa Perjanjian ini berkuatkuasa dan semua kelahiran anak-anak lembu ini akan dimasukkan ke dalam Buku Pendaftaran yang disimpan oleh Pengarah Perkhidmatan Veterinar Negeri atau wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(7)</div>
                <div class="clause-text">
                    Jika ada kelahiran anak oleh lembu pawah itu ianya mesti dilaporkan kepada Pengarah Perkhidmatan Veterinar Negeri atau wakilnya yang berdekatan dalam dalam masa satu bulan selepas kelahiran itu supaya pendaftaran dapat dibuat.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(8)</div>
                <div class="clause-text">
                    Jika lembu Pawah atau anak-anaknya sakit atau menunjukkan tanda-tanda tidak sihat. Pemawah mestilah memberitahu dengan serta merta kepada Pegawai dari Jabatan Veterinar yang berdekatan untuk mendapatkan rawatan yang percuma.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(9)</div>
                <div class="clause-text">
                    Jika lembu yang dipawah atau anak-anaknya ditimpa kemalangan dan mati walaupun sesudah mendapat rawatan dari Kerajaan maka pihak Kerajaan tidaklah bertanggungjawab keatasnya dan tidak akan membayar wang gantirugi atau saguhati kepada Pemawah.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(10)</div>
                <div class="clause-text">
                    Pemawah mestilah memberi peluang kepada pegawai-pegawai dari Jabatan Veterinar untuk melawat dan memeriksa lembu, atau anak-anak itu dari satu masa dan memasuki kawasan rumah Pemawah pada siang hari dengan tidak terlebih dahulu meminta atau mendapat kebenaran daripada Pemawah.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(11)</div>
                <div class="clause-text">
                    Jika lembu, yang dipawahkan itu mati disebabkan oleh kelalaian atau kecuaian Pemawah, beliau adalah bertanggungjawab atas kematian itu dan dikehendaki membayar balik kepadanya dan harga yang ditetapkan ialah RM <span class="dotted-fill">{{ $hargaSemasa }}</span> iaitu harga pembelian lembu oleh Kerajaan. (Harga nilaian yang ditetapkan tidaklah boleh dinafikan oleh Pemawah). Begitu juga dengan anak-anak lembu pawah yang mati disebabkan kelalaian dan kecuaian pemawah yang mana pemawah harus terpaksa membayar balik wang kerugian kematian tersebut kepada kerajaan mengikut harga yang ditetapkan oleh Pengarah Perkhidmatan Veterinar Negeri atau Wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(12)</div>
                <div class="clause-text">
                    Jika lembu pawah itu mati disebabkan oleh kelalaian atau kecuaian Pemawah dan jika lembu, itu mempunyai seekor anak atau lebih, jantan atau betina, maka anak atau anak-anaknya itu akan terus menjadi hak, milik dan harta Kerajaan dan untuk Perjanjian ini anak atau anak-anak lembu itu akan menggantikan lembu pawah yang mati itu, Anak atau anak-anak lembu itu akan dipelihara oleh Pemawah dengan perbelanjaannya sendiri dalam keadaan baik dan memuaskan, dan apabila seekor daripadanya meningkat dua tahun dan keatas maka ianya akan dijual dan wangnya dibahagi dua antara Pemawah dengan Kerajaan mengikut lama masanya Pemawah memelihara ternakan tersebut. pemawah berhak menuntut bahagian yang penuh iaitu separuh harga jualan itu jika anak lembu itu genap berumur dua tahun dan keatas tetapi jika sebelum umur itu maka bahagian dan hak Pemawah adalah mengikut masa pemeliharaan itu sahaja dan bayarannya ditetapkan oleh Pengarah Perkhidmatan Veterinar Negeri Kelantan atau wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(13)</div>
                <div class="clause-text">
                    Jika ada sesuatu sebab yang tidak dapat dielakkan mengikut pendapat Pengarah Perkhidmatan Veterinar Negeri atau wakilnya dan seekor daripada anak-anaknya terpaksa dijual sebelum anak yang tua meningkat umur dua tahun dan keatas, Wang jualan itu akan dibahagi dua sama banyaknya antara Kerajaan dan Pemawah. Jika ada anak-anaknya yang masih tinggal selepas jualan itu, Kerajaan cuma berhak menuntut separuh bahagian sahaja lagi iaitu ½ daripada harga anak yang tua itu jika dinilai atau dijual, ditolak dengan bayaran yang telah dibuat terdahulu daripada ini.
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- HALAMAN 3 -->
    <!-- ========================================================= -->
    <div class="page-sheet">
        <!-- Fasal 14 hingga 21 -->
        <div class="space-y-2.5 pt-1">
            <div class="clause-item">
                <div class="clause-num">(14)</div>
                <div class="clause-text">
                    Jika lembu mati disebabkan oleh penyakit, kemalangan atau sebab-sebab yang lain, tidak kerana kelalaian atau kecuaian Pemawah samada lembu itu mempunyai anak atau tidak. Kerajaan tidak membayar apa-apa ganti-rugi atau saguhati kepada Pemawah atas kematian itu dan dengan itu Perjanjian antara Pemawah Kerajaan ini adalah tamat dan dimansuhkan (batal) sama sekali.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(15)</div>
                <div class="clause-text">
                    Jika lembu yang dipawahkan itu terpaksa dijual dengan sebab-sebab tertentu, mengikut pendapat Pengarah Perkhidmatan Veterinar Negeri atau wakilnya, umpamanya lembu itu mandul dan jika Pemawah telah memeliharanya genap tiga tahun maka Pemawah berhak menuntut separuh harga jualan itu. Jika masa pemeliharaan itu kurang daripada tiga tahun maka Pemawah hanya berhak menuntut sebahagian sahaja daripada jualan itu mengikut tempoh meliharaan.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(16)</div>
                <div class="clause-text">
                    Jika anak atau anak-anak lembu itu pula terpaksa dijual dengan sebab-sebab tertentu, mengikut pendapat Pengarah Perkhidmatan Veterinar Negeri atau wakilnya, wang daripada jualan itu akan dibahagikan sama banyak antara Kerajaan dan Pemawah. Walau bagaimanapun lembu serta anak-anaknya yang tinggal tetap hak, milik dan harta Kerajaan dan untuk perjanjian ini semua syarat-syarat terkandung di dalam Surat ini harus dipatuhi oleh Pemawah.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(17)</div>
                <div class="clause-text">
                    Jika Pemawah berhajat membeli lembu dan anak-anaknya untuk kegunaan sendiri selepas anak yang pertamanya meningkat umur dua tahun dan keatas beliau bolehlah membayar kepada Kerajaan cuma wang bahagian yang hak dituntut oleh kerajaan sahaja.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(18)</div>
                <div class="clause-text">
                    Pengarah Perkhidmatan Veterinar Negeri atau wakilnya adalah diberi kuasa oleh Kerajaan untuk menjual ternak-ternak, induk atau/dan anak-anaknya, jika beliau berpendapat ternak-ternak itu tidak dapat disimpan atau dipelihara oleh Pemawah dengan keadaan yang baik, sihat dan sempurna. Semua wang-wang daripada jualan itu akan dimasukkan ke dalam Hasil Pendapatan Kerajaan dan Pemawah tiada berhak menuntut apa-apa saguhati atau bayaran daripada Kerajaan.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(19)</div>
                <div class="clause-text">
                    Pemawah tidaklah dibenarkan memindahkan lembu dan/atau anak-anaknya itu ke tempat lain daripada kampung yang tertentu mengikut Surat Perjanjian ini dengan tiada terlebih dahulu mendapat kebenaran bertulis daripada Pengarah Perkhidmatan Veterinar Negeri atau wakilnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(20)</div>
                <div class="clause-text">
                    Jika Pemawah meninggal dunia dan jika waris yang ingin memelihara lembu serta anak-anaknya itu maka Surat Perjanjian ini hendaklah ditukarkan dengan nama waris itu dengan persetujuan Pengarah Perkhidmatan Veterinar Negeri atau wakilnya dan dengan ini waris yang dinamakan itu akan bertanggungjawab atas semua perkara yang terkandung di dalamnya mulai daripada tarikh Surat Perjanjian ini ditandatangani oleh Pemawah asalnya.
                </div>
            </div>

            <div class="clause-item">
                <div class="clause-num">(21)</div>
                <div class="clause-text">
                    Jika lembu pawah itu anak-anaknya mati disebabkan oleh racun Sodium Arsenita, mengikut pendapat Jabatan Kimia Kerajaan dan jika pada masa itu pihak berkenaan masih melayani pembayaran wang gantirugi kerana seperti itu, Kerajaan akan membuat tuntutan untuk mendapatkan balik wang gantirugi daripada pihak yang berkenaan dan akan mengambil tindakan untuk menggantikan balik lembu dan anak-anaknya yang mati itu kepada Pemawah. Walaubagaimanapun Kerajaan tidak akan membuat gantian secara pembayaran wang dan pemawah berhak menuntutnya sungguhpun wang itu berbangkit daripada pemeliharaan ternakan yang mati itu. Wang itu akan dimasukkan ke dalam Hasil Pendapatan Kerajaan. Jika oleh beberapa sebab Kerajaan tidak dapat menggantikan ternakan yang mati itu, Kerajaan akan membayar wang gantirugi kepada Pemawah. Bahagian yang akan dibayar adalah mengikut tempoh masa pemeliharaan Pemawah. Bayaran kepada Pemawah itu akan ditetapkan oleh Pengarah Perkhidmatan Veterinar Negeri atau wakilnya dan pihak Pemawah tidaklah boleh menafikan ketetapan itu.
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- HALAMAN 4 -->
    <!-- ========================================================= -->
    <div class="page-sheet flex flex-col justify-between">
        <div>
            <!-- Fasal 22 hingga 26 -->
            <div class="space-y-2 pt-1">
                <div class="clause-item">
                    <div class="clause-num">(22)</div>
                    <div class="clause-text">
                        Jika lembu atau anak-anaknya dijual oleh Pemawah dengan tidak terlebih dahulu mendapat kebenaran bertulis daripada Pengarah Perkhidmatan Veterinar Negeri atau wakilnya, Pemawah akan didenda bagi menggantikan kehilangan harta Kerajaan dan dendaan itu akan ditetapkan oleh Pengarah Perkhidmatan Veterinar Negeri atau wakilnya mengikut nilaian harga pasaran.
                    </div>
                </div>

                <div class="clause-item">
                    <div class="clause-num">(23)</div>
                    <div class="clause-text">
                        Pemawah boleh memansuhkan Perjanjian ini dengan menyerahkan balik lembu serta semua anak-anaknya bulat-bulat kepada Kerajaan pada bila-bila masa jua dengan memberitahu sekurang-kurangnya dua bulan terlebih dahulu kepada Kerajaan sebelum penyerahan tersebut dan Kerajaan tidaklah berhak membuat apa-apa bayaran gantirugi atau saguhati kepada Pemawah dan Pemawah tidak berhak membuat apa-apa tuntutan berkaitan dengannya. Hanya Pengarah Perkhidmatan Veterinar Negeri atau wakilnya sahaja yang berkuasa menetukan bayaran saguhati kepada pemawah jika beliau berpendapat bahawa Pemawah telah memelihara lembu pawah itu serta anak-anaknya dengan keadaan memuaskan. Pembayaran saguhati itu adalah mengikut taksiran dan pendapat Pengarah Perkhidmatan Veterinar Negeri atau wakilnya.
                    </div>
                </div>

                <div class="clause-item">
                    <div class="clause-num">(24)</div>
                    <div class="clause-text">
                        Penyerahan seekor lembu itu atau anak-anaknya jika dibuat oleh Pemawah kepada Kerajaan, dari satu masa ke satu masa, akan diakui dengan surat yang tersebut.
                    </div>
                </div>

                <div class="clause-item">
                    <div class="clause-num">(25)</div>
                    <div class="clause-text">
                        Jika ada sesuatu perkara yang berbangkit yang tidak dapat dielakkan oleh sesuatu pihak, Kerajaan atau Pemawah seperti malapetaka dan sebagainya, yang menyebabkan kematian lembu dan anak-anaknya yang mendatangkan kerugian kepada Pemawah dan Pemawah tidak berhak membuat apa-apa tuntutan daripada Kerajaan.
                    </div>
                </div>

                <div class="clause-item">
                    <div class="clause-num">(26)</div>
                    <div class="clause-text">
                        Jika ada sesuatu perkara yang berbangkit mengenai pemeliharaan lembu ini di mana perkara itu tidak dapat diterangkan dengan jelasnya di dalam Surat Perjanjian ini dan jika perselisihan berbangkit yang tidak dapat diselesaikan atau persetujuan dicapai di antara Kedua-dua Pihak, maka perkara ini hendaklah dikemukakan kepada Pengarah Perkhidmatan Veterinar Negeri atau Ketua Jajahan yang tertentu di mana Pemawah itu tinggal dan keputusan itu adalah muktamad dan harus dipatuhi oleh kedua-dua belah pihak.
                    </div>
                </div>
            </div>

            <!-- Perenggan Pengakuan Tandatangan -->
            <div class="text-justify font-bold uppercase mt-4 mb-5 text-[9.2pt] leading-relaxed">
                Semua SYARAT-SYARAT di dalam RANG PERJANJIAN di atas adalah dipersetujui oleh kedua-dua pihak, KERAJAAN DAN PEMAWAH DAN PADA MENYAKSIKANNYA PIHAK YANG TERSEBUT DI SINI ADALAH MENURUNKAN TANDATANGAN PADA TARIKH, HARI DAN WAKTU YANG TERSEBUT INI.
            </div>

            <!-- Ruangan Tandatangan & Saksi (Format Templat Rasmi) -->
            <div class="space-y-4 text-[9.5pt]">
                
                <!-- 1. Tandatangan Pihak Kerajaan -->
                <div class="space-y-0.5">
                    <div class="flex items-end justify-between">
                        <div class="flex-1">
                            DITANDATANGANI OLEH : <span class="dotted-fill">{{ $perjanjian->pegawai_penyelia ?? '....................................................................' }}</span>
                        </div>
                        <div class="w-64 text-right">
                            ....................................................................
                        </div>
                    </div>
                    <div class="flex items-start justify-between text-[8pt] text-slate-600">
                        <div class="pl-44">Nama</div>
                        <div class="pr-8">Tandatangan/Cap jari</div>
                    </div>
                    <div class="pt-0.5">
                        <div>JAWATAN <span class="dotted-fill">PENGARAH / PEGAWAI VETERINAR JAJAHAN</span></div>
                        <div class="pl-24 text-[8pt] font-bold text-slate-800">(BAGI PIHAK KERAJAAN MALAYSIA/NEGERI KELANTAN)</div>
                    </div>
                </div>

                <!-- 2. Saksi Pihak Kerajaan -->
                <div class="space-y-0.5 pt-1">
                    <div class="flex items-end justify-between">
                        <div class="flex-1">
                            DI HADAPAN : <span class="dotted-fill">.....................................................................................</span>
                        </div>
                        <div class="w-64 text-right">
                            ....................................................................
                        </div>
                    </div>
                    <div class="flex items-start justify-between text-[8pt] text-slate-600">
                        <div>(SEBAGAI SAKSI) <span class="pl-16">Nama</span></div>
                        <div class="pr-8">Tandatangan/Cap jari</div>
                    </div>
                    <div class="pt-0.5">
                        <div>JAWATAN ....................................................................................................................................</div>
                    </div>
                </div>

                <!-- 3. Tandatangan Pemawah -->
                <div class="space-y-0.5 pt-1">
                    <div class="flex items-end justify-between">
                        <div class="flex-1">
                            DITANDATANGANI OLEH : <span class="dotted-fill">{{ $namaPeserta ?? '....................................................................' }}</span>
                        </div>
                        <div class="w-64 text-right">
                            ....................................................................
                        </div>
                    </div>
                    <div class="flex items-start justify-between text-[8pt] text-slate-600">
                        <div>(SEBAGAI PEMAWAH) <span class="pl-8">Nama</span></div>
                        <div class="pr-8">Tandatangan/Cap jari</div>
                    </div>
                </div>

                <!-- 4. Saksi Pemawah -->
                <div class="space-y-0.5 pt-1">
                    <div class="flex items-end justify-between">
                        <div class="flex-1">
                            DI HADAPAN : ..............................................................................................................................
                        </div>
                        <div class="w-64 text-right">
                            ....................................................................
                        </div>
                    </div>
                    <div class="flex items-start justify-between text-[8pt] text-slate-600">
                        <div>(SEBAGAI SAKSI)</div>
                        <div class="pr-8">Tandatangan/Cap jari</div>
                    </div>
                    <div class="pt-0.5">
                        <div>JAWATAN/PEKERJAAN ..............................................................................................................</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>


