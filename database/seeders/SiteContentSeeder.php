<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            // Hero Section
            [
                'key' => 'hero_title',
                'value' => 'Discover Indonesia\'s Breakthrough Research',
                'group' => 'hero',
                'type' => 'text',
                'label' => 'Hero Title',
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Platform publikasi jurnal ilmiah terakreditasi SINTA dan berstandar internasional. Temukan ribuan artikel berkualitas dari peneliti Indonesia.',
                'group' => 'hero',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
            ],
            [
                'key' => 'hero_search_placeholder',
                'value' => 'Cari Jurnal, Artikel, atau Penulis...',
                'group' => 'hero',
                'type' => 'text',
                'label' => 'Search Placeholder',
            ],
            [
                'key' => 'hero_popular_tags',
                'value' => json_encode(['SINTA 2', 'Kesehatan', 'Teknik Informatika', 'Hukum', 'Pendidikan']),
                'group' => 'hero',
                'type' => 'json',
                'label' => 'Popular Search Tags',
            ],

            // Featured Section
            [
                'key' => 'featured_title',
                'value' => 'Jurnal Pilihan',
                'group' => 'featured',
                'type' => 'text',
                'label' => 'Featured Section Title',
            ],
            [
                'key' => 'featured_subtitle',
                'value' => 'Jurnal-jurnal terbaik dengan akreditasi nasional dan internasional',
                'group' => 'featured',
                'type' => 'text',
                'label' => 'Featured Section Subtitle',
            ],
            [
                'key' => 'featured_journal_ids',
                'value' => json_encode([]),
                'group' => 'featured',
                'type' => 'json',
                'label' => 'Featured Journal IDs',
            ],

            // Footer Section
            [
                'key' => 'footer_about',
                'value' => 'IAMJOS adalah platform jurnal akademik Indonesia yang menyediakan akses terbuka ke penelitian berkualitas tinggi dari berbagai disiplin ilmu.',
                'group' => 'footer',
                'type' => 'textarea',
                'label' => 'Footer About Text',
            ],
            [
                'key' => 'footer_address',
                'value' => 'Jl. Pendidikan No. 123, Jakarta 10110, Indonesia',
                'group' => 'footer',
                'type' => 'text',
                'label' => 'Footer Address',
            ],
            [
                'key' => 'footer_email',
                'value' => 'info@iamjos.id',
                'group' => 'footer',
                'type' => 'text',
                'label' => 'Footer Email',
            ],
            [
                'key' => 'footer_phone',
                'value' => '+62 21 1234 5678',
                'group' => 'footer',
                'type' => 'text',
                'label' => 'Footer Phone',
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/iamjos',
                'group' => 'social',
                'type' => 'text',
                'label' => 'Facebook URL',
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/iamjos',
                'group' => 'social',
                'type' => 'text',
                'label' => 'Twitter/X URL',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/iamjos',
                'group' => 'social',
                'type' => 'text',
                'label' => 'Instagram URL',
            ],
            [
                'key' => 'social_youtube',
                'value' => '',
                'group' => 'social',
                'type' => 'text',
                'label' => 'YouTube URL',
            ],

            // Subjects/Categories for browsing
            [
                'key' => 'browse_subjects',
                'value' => json_encode([
                    ['name' => 'Teknik & Teknologi', 'icon' => 'fa-microchip', 'color' => 'indigo'],
                    ['name' => 'Kesehatan & Kedokteran', 'icon' => 'fa-heartbeat', 'color' => 'red'],
                    ['name' => 'Sosial & Humaniora', 'icon' => 'fa-users', 'color' => 'blue'],
                    ['name' => 'Ekonomi & Bisnis', 'icon' => 'fa-chart-line', 'color' => 'emerald'],
                    ['name' => 'Pendidikan', 'icon' => 'fa-graduation-cap', 'color' => 'amber'],
                    ['name' => 'Hukum', 'icon' => 'fa-balance-scale', 'color' => 'purple'],
                    ['name' => 'Pertanian', 'icon' => 'fa-leaf', 'color' => 'green'],
                    ['name' => 'Seni & Desain', 'icon' => 'fa-palette', 'color' => 'pink'],
                ]),
                'group' => 'browse',
                'type' => 'json',
                'label' => 'Browse Subjects',
            ],

            // About Page Content
            [
                'key' => 'about_title',
                'value' => 'Empowering Knowledge Sharing Worldwide',
                'group' => 'about',
                'type' => 'text',
                'label' => 'About Page Title',
            ],
            [
                'key' => 'about_content',
                'value' => '<h2>Selamat Datang di IAMJOS</h2>
<p>IAMJOS (Indonesian Academic Journal System) adalah platform publikasi jurnal akademik modern yang dirancang untuk mendukung ekosistem penelitian di Indonesia. Kami berkomitmen untuk menyediakan akses terbuka ke pengetahuan berkualitas tinggi dari berbagai disiplin ilmu.</p>

<h3>Visi Kami</h3>
<p>Menjadi platform jurnal akademik terdepan di Indonesia yang menghubungkan peneliti, akademisi, dan institusi pendidikan dalam berbagi pengetahuan dan inovasi untuk kemajuan bangsa.</p>

<h3>Misi Kami</h3>
<ul>
    <li>Menyediakan platform publikasi jurnal yang modern, mudah digunakan, dan sesuai standar internasional</li>
    <li>Mendorong praktik open access untuk memperluas jangkauan dan dampak penelitian Indonesia</li>
    <li>Memfasilitasi proses peer review yang transparan dan berkualitas</li>
    <li>Mendukung akreditasi jurnal nasional dan indexing internasional</li>
    <li>Memberdayakan peneliti Indonesia untuk berkontribusi dalam kancah akademik global</li>
</ul>

<h3>Mengapa Memilih IAMJOS?</h3>
<p>Platform kami dibangun dengan teknologi modern dan mengikuti best practices dalam pengelolaan jurnal akademik:</p>

<ul>
    <li><strong>Antarmuka Modern</strong> - Desain yang bersih dan intuitif untuk pengalaman pengguna yang optimal</li>
    <li><strong>Workflow Lengkap</strong> - Dari submission hingga publikasi dengan sistem peer review terintegrasi</li>
    <li><strong>Open Access</strong> - Mendukung akses terbuka untuk memperluas dampak penelitian</li>
    <li><strong>Standar Internasional</strong> - Mengikuti protokol OAI-PMH dan standar metadata global</li>
    <li><strong>Multi-Journal</strong> - Satu platform untuk mengelola banyak jurnal</li>
</ul>

<blockquote>
    "Pengetahuan adalah kekuatan. Dengan berbagi pengetahuan, kita membangun masa depan yang lebih baik untuk Indonesia."
</blockquote>

<h3>Bergabung Bersama Kami</h3>
<p>Apakah Anda seorang peneliti, akademisi, atau pengelola jurnal? Bergabunglah dengan komunitas IAMJOS untuk mengakses dan berbagi karya ilmiah berkualitas. Daftarkan jurnal Anda atau mulai berkontribusi sebagai penulis hari ini.</p>',
                'group' => 'about',
                'type' => 'html',
                'label' => 'About Page Content',
            ],
        ];

        foreach ($contents as $content) {
            SiteContent::updateOrCreate(
                ['key' => $content['key']],
                $content
            );
        }
    }
}
