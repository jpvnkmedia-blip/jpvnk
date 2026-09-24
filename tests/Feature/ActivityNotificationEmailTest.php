<?php

namespace Tests\Feature;

use App\Mail\ActivityNotificationMail;
use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ActivityNotificationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_notification_sends_email_to_user_automatically(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Ahmad Penternak',
            'email' => 'ahmad.penternak@example.com',
            'ic_number' => '880101035555',
            'role' => 'penternak',
        ]);

        $notification = UserNotification::send(
            $user->id,
            'Permohonan Ternakan Diluluskan',
            'Permohonan pendaftaran tag RFID bagi 5 ekor lembu telah berjaya diluluskan.',
            'eptr',
            url('/eptr'),
            'fa-solid fa-cow',
            'emerald'
        );

        $this->assertNotNull($notification);
        $this->assertDatabaseHas('user_notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'title' => 'Permohonan Ternakan Diluluskan',
        ]);

        Mail::assertSent(ActivityNotificationMail::class, function ($mail) use ($user) {
            return $mail->hasTo('ahmad.penternak@example.com')
                && $mail->notification->title === 'Permohonan Ternakan Diluluskan'
                && $mail->notification->type === 'eptr';
        });
    }

    public function test_user_notification_handles_invalid_or_missing_email_gracefully(): void
    {
        Mail::fake();

        $userNoEmail = User::factory()->create([
            'name' => 'Penternak Tiada Emel',
            'email' => 'invalid-email-address',
            'ic_number' => '890101035555',
            'role' => 'penternak',
        ]);

        $notification = UserNotification::send(
            $userNoEmail->id,
            'Ujian Sistem',
            'Mesej pengujian sistem tanpa ralat emel.',
            'sistem'
        );

        $this->assertNotNull($notification);
        Mail::assertNothingSent();
    }

    public function test_course_attendance_verification_sends_email_with_direct_certificate_download_link(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'name' => 'Pegawai Kursus JPVNK',
            'email' => 'admin.kursus@veterinar.kelantan.gov.my',
            'ic_number' => '800101035111',
            'role' => 'admin_kursus',
        ]);

        $participant = User::factory()->create([
            'name' => 'Siti Nurhaliza Penternak',
            'email' => 'siti.peserta@gmail.com',
            'ic_number' => '920202035222',
            'role' => 'penternak',
        ]);

        $course = Course::create([
            'title' => 'Kursus Pengurusan Penternakan Ruminan Moden',
            'code' => 'KURSUS-2026-001',
            'category' => 'Ruminan',
            'description' => 'Kursus komprehensif teknologi pembiakan ruminan moden.',
            'trainer_name' => 'Dr. Zulkifli Veterinar',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(6),
            'time' => '08:30 Pagi - 04:30 Petang',
            'location' => 'Pusat Latihan Veterinar Telaga Papan',
            'jajahan' => 'Pasir Puteh',
            'capacity' => 30,
            'registered_count' => 1,
            'status' => 'Buka',
            'created_by' => $admin->id,
        ]);

        $application = CourseApplication::create([
            'course_id' => $course->id,
            'user_id' => $participant->id,
            'registration_number' => 'REG-2026-0099',
            'status' => 'Disahkan',
        ]);

        // Pegawai Latihan mengesahkan kehadiran peserta
        $response = $this->actingAs($admin)->post("/kursus/pemohon/{$application->id}/hadir");
        $response->assertStatus(302);

        $application->refresh();
        $this->assertEquals('Hadir', $application->status);
        $this->assertNotEmpty($application->certificate_number);

        // Pastikan emel notifikasi sijil digital dihantar ke emel peserta
        Mail::assertSent(ActivityNotificationMail::class, function ($mail) use ($participant, $application) {
            $expectedCertUrl = route('kursus.sijil', $application->id);
            return $mail->hasTo('siti.peserta@gmail.com')
                && $mail->isCertificate === true
                && $mail->certificateUrl === $expectedCertUrl
                && str_contains($mail->notification->title, 'Sijil Digital');
        });

        // Pemohon boleh mengakses dan memuat turun / mencetak sijil digital secara terus
        $certResponse = $this->get(route('kursus.sijil', $application->id));
        $certResponse->assertStatus(200);
        $certResponse->assertSee($application->certificate_number);
        $certResponse->assertSee('Siti Nurhaliza Penternak');
        $certResponse->assertSee('Kursus Pengurusan Penternakan Ruminan Moden');
    }

    public function test_email_view_renders_properly_with_certificate_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Muhammad Ali',
            'email' => 'ali@example.com',
            'ic_number' => '950505035333',
            'role' => 'penternak',
        ]);

        $notification = new UserNotification([
            'user_id' => $user->id,
            'type' => 'kursus',
            'title' => 'Sijil Digital Kursus Dikeluarkan',
            'message' => 'Tahniah! Sijil Digital rasmi bagi kursus anda telah sedia.',
            'action_url' => url('/kursus/sijil/123'),
        ]);
        $notification->id = 999;
        $notification->created_at = now();

        $mailable = new ActivityNotificationMail($notification, $user);
        $renderedHtml = $mailable->render();

        $this->assertStringContainsString('Muhammad Ali', $renderedHtml);
        $this->assertStringContainsString('Sijil Digital Kursus Dikeluarkan', $renderedHtml);
        $this->assertStringContainsString('Muat Turun / Cetak Sijil Digital (PDF)', $renderedHtml);
        $this->assertStringContainsString(url('/kursus/sijil/123'), $renderedHtml);
    }
}
