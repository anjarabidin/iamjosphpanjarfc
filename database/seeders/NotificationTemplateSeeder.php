<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            'welcome' => [
                'body' => "Selamat datang {name} di IAMJOS. Akun Anda berhasil dibuat.",
                'variables' => ['name'],
            ],
            'submission_received' => [
                'body' => "Halo {name}, naskah Anda berjudul '{title}' telah berhasil disubmit. Pantau statusnya di dashboard.",
                'variables' => ['name', 'title'],
            ],
            'decision_update' => [
                'body' => "Halo {name}, ada update status untuk naskah '{title}'. Status saat ini: {status}. Silakan cek dashboard.",
                'variables' => ['name', 'title', 'status'],
            ],
            'revision_request' => [
                'body' => "Halo {name}, editor meminta revisi untuk naskah '{title}'. Silakan cek dashboard untuk detailnya.",
                'variables' => ['name', 'title'],
            ],
            'submission_accepted' => [
                'body' => "Halo {name}, selamat! Naskah Anda '{title}' telah diterima untuk dipublikasikan.",
                'variables' => ['name', 'title'],
            ],
            'submission_rejected' => [
                'body' => "Halo {name}, mohon maaf naskah Anda '{title}' tidak dapat kami terima. Silakan cek dashboard untuk feedback dari editor.",
                'variables' => ['name', 'title'],
            ],
            'new_submission_notification' => [
                'body' => "Halo {name}, ada naskah baru berjudul '{title}'. Silakan tinjau di dashboard.",
                'variables' => ['name', 'title'],
            ],
            'reviewer_assigned' => [
                'body' => "Halo {name}, Anda telah ditugaskan untuk mereview naskah '{title}' (Round {round}). Silakan cek dashboard untuk detailnya.",
                'variables' => ['name', 'title', 'round'],
            ],
            'discussion_message' => [
                'body' => "Halo {name}, ada pesan baru di diskusi '{subject}' untuk naskah '{title}'. Silakan cek dashboard untuk detailnya.",
                'variables' => ['name', 'subject', 'title'],
            ],
            'review_submitted' => [
                'body' => "Halo {name}, reviewer telah mengirimkan review untuk naskah '{title}'. Silakan cek dashboard untuk detailnya.",
                'variables' => ['name', 'title'],
            ],
            'reviewer_accepted' => [
                'body' => "Halo {name}, Reviewer {reviewer_name} telah menerima undangan review untuk naskah '{title}'.",
                'variables' => ['name', 'reviewer_name', 'title'],
            ],
            'reviewer_declined' => [
                'body' => "Halo {name}, Reviewer {reviewer_name} telah menolak undangan review untuk naskah '{title}'.",
                'variables' => ['name', 'reviewer_name', 'title'],
            ],
        ];

        foreach ($templates as $key => $data) {
            NotificationTemplate::updateOrCreate(
                ['event_key' => $key, 'channel' => 'whatsapp'],
                [
                    'body' => $data['body'],
                    'variables' => $data['variables'],
                    'is_active' => true,
                ]
            );
        }
    }
}
