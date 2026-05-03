@php $title = 'Author Guidelines'; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <section class="bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Author Guidelines</h1>

            <div class="prose prose-lg max-w-none">
                <h2>Submission Requirements</h2>
                <ul>
                    <li>Manuscripts must be original and not under consideration elsewhere</li>
                    <li>File format: Microsoft Word (.doc, .docx) or PDF</li>
                    <li>Maximum file size: 20MB</li>
                    <li>Language: English or Indonesian</li>
                </ul>

                <h2>Manuscript Structure</h2>
                <ol>
                    <li><strong>Title:</strong> Clear and concise</li>
                    <li><strong>Abstract:</strong> 150-300 words</li>
                    <li><strong>Keywords:</strong> 3-5 keywords</li>
                    <li><strong>Introduction</strong></li>
                    <li><strong>Methods</strong></li>
                    <li><strong>Results</strong></li>
                    <li><strong>Discussion</strong></li>
                    <li><strong>Conclusion</strong></li>
                    <li><strong>References</strong></li>
                </ol>

                <h2>References Style</h2>
                <p>Use APA 7th edition citation style.</p>
            </div>

            <div class="mt-8">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
                    Submit Manuscript
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
