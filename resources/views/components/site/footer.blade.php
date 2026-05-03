{{--
    Dynamic Portal Footer Component (Dark Theme & Grid Layout)
    Uses $footerMenu from PublicNavigationComposer
--}}
@php
    use App\Models\SiteSetting;

    $footer = SiteSetting::value('footer_content');
@endphp

<footer class="bg-slate-900 text-slate-300 font-sans border-t border-slate-800">

    {{-- MAIN FOOTER CONTENT --}}
    <div class="container mx-auto px-4 py-12">
        @if($footer)
            {{-- CUSTOM FOOTER CONTENT --}}
            <div class="prose prose-invert max-w-none">
                {!! $footer !!}
            </div>
        @endif
    </div>
    </div>

    {{-- BOTTOM BAR: COPYRIGHT --}}
    <div class="bg-slate-950 py-6 border-t border-slate-800">
        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div>
                &copy; {{ date('Y') }} <strong>{{ $settings['site_name'] ?? 'IAMJOS' }}</strong>. All rights reserved.
            </div>
            <div class="flex items-center gap-1">
                Powered by <a href="{{ env('APP_URL') }}" class="text-slate-400 hover:text-white font-bold">IAMJOS</a>
                <span>- Indonesian Academic Journal System</span>
            </div>
        </div>
    </div>
</footer>
