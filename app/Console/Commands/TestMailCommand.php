<?php

namespace App\Console\Commands;

use App\Mail\ActivityNotificationMail;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email=hanif@dvs.gov.my}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji penghantaran emel notifikasi aktiviti ke alamat emel yang ditetapkan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetEmail = $this->argument('email');
        $this->info("Memulakan ujian penghantaran emel ke: {$targetEmail}");
        
        $mailer = config('mail.default', 'log');
        $fromAddress = config('mail.from.address', 'notifikasi@veterinar.kelantan.gov.my');
        $fromName = config('mail.from.name', 'Jabatan Perkhidmatan Veterinar Kelantan');

        $this->table(['Konfigurasi', 'Nilai'], [
            ['Default Mailer', $mailer],
            ['From Address', $fromAddress],
            ['From Name', $fromName],
            ['SMTP Host', config('mail.mailers.smtp.host', '-')],
            ['SMTP Port', config('mail.mailers.smtp.port', '-')],
            ['SMTP Username', config('mail.mailers.smtp.username', '-')],
            ['SMTP Encryption', config('mail.mailers.smtp.scheme', config('mail.mailers.smtp.encryption', '-'))],
        ]);

        if ($mailer === 'log') {
            $this->warn("PERHATIAN: 'MAIL_MAILER=log'. Emel hanya akan ditulis ke dalam fail 'storage/logs/laravel.log' dan tidak dihantar melalui internet.");
            $this->warn("Untuk menghantar emel sebenar ke peti masuk, sila tetapkan MAIL_MAILER=smtp dan tetapan SMTP dalam .env atau Render Environment Variables.");
        }

        try {
            $user = User::where('email', $targetEmail)->first() ?: new User([
                'name' => 'Mohd Hanif bin Ismail (Super Admin)',
                'email' => $targetEmail,
                'role' => 'super_admin',
            ]);

            $notification = new UserNotification([
                'user_id' => $user->id ?? 1,
                'type' => 'sistem',
                'title' => 'Ujian Penghantaran Emel Sistem e-JPVNK',
                'message' => 'Ini adalah emel ujian rasmi daripada Sistem Pengurusan Veterinar Negeri Kelantan (e-JPVNK) untuk mengesahkan konfigurasi penghantaran notifikasi aktiviti berfungsi dengan lancar.',
                'action_url' => url('/dashboard'),
            ]);
            $notification->id = 9999;
            $notification->created_at = now();

            Mail::to($targetEmail)->send(new ActivityNotificationMail($notification, $user));

            $this->info("Emel berjaya dihantar ke: {$targetEmail}");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("RALAT: Gagal menghantar emel: " . $e->getMessage());
            $this->line($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
