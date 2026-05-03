@extends('layouts.portal')

@section('title', $settings['about_title'] ?? 'About Us')
@section('description', 'Tentang IAMJOS - Platform Jurnal Akademik Indonesia')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-accent-900 py-20 lg:py-32 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 hero-pattern"></div>
        
        <!-- Floating Shapes -->
        <div class="absolute top-10 left-10 w-64 h-64 bg-primary-500/20 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-accent-500/20 rounded-full blur-3xl float-animation" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/3 w-40 h-40 bg-pink-500/15 rounded-full blur-2xl float-animation" style="animation-delay: -1.5s;"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-8">
                <i class="fas fa-info-circle text-primary-300 mr-2"></i>
                <span class="text-white/90 text-sm font-medium">Tentang Kami</span>
            </div>

            <!-- Title with Serif Font -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold font-serif text-white mb-6 leading-tight">
                {{ $settings['about_title'] ?? 'Empowering Knowledge Sharing Worldwide' }}
            </h1>

            <p class="text-lg lg:text-xl text-white/70 max-w-2xl mx-auto">
                Menghubungkan peneliti, akademisi, dan institusi untuk kemajuan ilmu pengetahuan Indonesia
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Content Container -->
            <article class="prose-content">
                {!! clean($settings['about_content'] ?? '
                <h2>Selamat Datang di IAMJOS</h2>
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
                <p>Apakah Anda seorang peneliti, akademisi, atau pengelola jurnal? Bergabunglah dengan komunitas IAMJOS untuk mengakses dan berbagi karya ilmiah berkualitas. Daftarkan jurnal Anda atau mulai berkontribusi sebagai penulis hari ini.</p>
                ') !!}
            </article>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $journalCount = \App\Models\Journal::where('enabled', true)->count();
                    $articleCount = \App\Models\Submission::where('status', \App\Models\Submission::STATUS_PUBLISHED)->count();
                    $authorCount = \App\Models\User::role('Author')->count();
                    $issueCount = \App\Models\Issue::where('is_published', true)->count();
                @endphp
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/25">
                        <i class="fas fa-book-open text-white text-2xl"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-bold text-gray-900 font-display mb-1">{{ $journalCount }}</div>
                    <div class="text-gray-500 font-medium">Jurnal Aktif</div>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/25">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-bold text-gray-900 font-display mb-1">{{ $articleCount }}</div>
                    <div class="text-gray-500 font-medium">Artikel Publikasi</div>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-amber-500/25">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-bold text-gray-900 font-display mb-1">{{ $authorCount }}</div>
                    <div class="text-gray-500 font-medium">Penulis Terdaftar</div>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-pink-500/25">
                        <i class="fas fa-newspaper text-white text-2xl"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-bold text-gray-900 font-display mb-1">{{ $issueCount }}</div>
                    <div class="text-gray-500 font-medium">Issue Terbit</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary-600 via-accent-600 to-primary-800 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold font-display text-white mb-6">
                Siap Berkontribusi?
            </h2>
            <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan peneliti dan akademisi yang telah mempercayakan karya mereka di platform kami.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-primary-600 bg-white hover:bg-gray-100 rounded-xl transition-colors shadow-xl">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar Sekarang
                    </a>
                @endguest
                <a href="{{ route('portal.journals') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-white/10 hover:bg-white/20 rounded-xl transition-colors border border-white/30">
                    <i class="fas fa-book-open mr-2"></i>
                    Jelajahi Jurnal
                </a>
            </div>
        </div>
    </section>
@endsection
