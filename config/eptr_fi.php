<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jadual Fi Bayaran Enakmen Pendaftaran Ternakan Ruminan (EPTR)
    |--------------------------------------------------------------------------
    |
    | Senarai kadar fi dan caj statutori mengikut peruntukan Enakmen
    | Pendaftaran Ternakan Ruminan Negeri Kelantan bagi Ruminan Besar
    | (Lembu, Kerbau) dan Ruminan Kecil (Kambing, Bebiri).
    |
    */
    'fi' => [
        [
            'peruntukan' => 'Seksyen 5',
            'keterangan' => 'Pendaftaran pertama',
            'kadar_ruminan_besar' => 2.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 2.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'pendaftaran',
            'borang' => 'Borang A',
        ],
        [
            'peruntukan' => 'Seksyen 6',
            'keterangan' => 'Penandaan Ternakan Ruminan (Tagging)',
            'kadar_ruminan_besar' => 8.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 5.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'penandaan',
            'borang' => 'Borang A / Tagging',
        ],
        [
            'peruntukan' => 'Seksyen 7',
            'keterangan' => 'Pendaftaran lewat ternakan ruminan',
            'kadar_ruminan_besar' => 12.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 10.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'pendaftaran_lewat',
            'borang' => 'Borang A',
        ],
        [
            'peruntukan' => 'Seksyen 8',
            'keterangan' => 'Pindah milik ternakan ruminan',
            'kadar_ruminan_besar' => 2.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 2.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'pindah_milik',
            'borang' => 'Borang B',
        ],
        [
            'peruntukan' => 'Seksyen 10',
            'keterangan' => 'Pendaftaran ternakan ruminan yang dipindah masuk (Pemindahan dalam negeri iaitu antara jajahan)',
            'kadar_ruminan_besar' => 2.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 2.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'pindah_masuk_daftar',
            'borang' => 'Permit Pemindahan / Borang A',
        ],
        [
            'peruntukan' => 'Seksyen 10',
            'keterangan' => 'Bayaran ternakan ruminan yang dipindah masuk (Pemindahan dalam negeri iaitu antara jajahan)',
            'kadar_ruminan_besar' => 10.00,
            'unit_ruminan_besar' => '/konsainan',
            'kadar_ruminan_kecil' => 8.00,
            'unit_ruminan_kecil' => '/konsainan',
            'kategori' => 'pindah_masuk_konsainan',
            'borang' => 'Permit Pemindahan',
        ],
        [
            'peruntukan' => 'Perenggan 11(1)(b)',
            'keterangan' => 'Bayaran pembatalan pendaftaran ternakan ruminan akibat sembelihan',
            'kadar_ruminan_besar' => 10.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 5.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'pembatalan_sembelihan',
            'borang' => 'Borang C / Borang D (Permit Sembelihan)',
        ],
        [
            'peruntukan' => 'Perenggan 11(1)(c)',
            'keterangan' => 'Bayaran pembatalan akibat pindah keluar negeri',
            'kadar_ruminan_besar' => 20.00,
            'unit_ruminan_besar' => '/ekor',
            'kadar_ruminan_kecil' => 10.00,
            'unit_ruminan_kecil' => '/ekor',
            'kategori' => 'pembatalan_pindah_keluar',
            'borang' => 'Borang C / Permit Pemindahan Keluar',
        ],
        [
            'peruntukan' => 'Subseksyen 44(3)',
            'keterangan' => 'Bayaran salinan pendua Borang B',
            'kadar_ruminan_besar' => 10.00,
            'unit_ruminan_besar' => '/salinan',
            'kadar_ruminan_kecil' => 10.00,
            'unit_ruminan_kecil' => '/salinan',
            'kategori' => 'salinan_pendua',
            'borang' => 'Borang B (Salinan Pendua)',
        ],
    ],
];
