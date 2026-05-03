<?php

namespace App\Livewire\Site;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Models\Role;

class PortalHero extends Component
{
    public $query = '';
    public $suggestions = [];
    public $stats = [];
    public $blockConfig = [];

    public function mount($block = null)
    {
        $this->blockConfig = $block->config ?? [];
        
        // Cache stats for performance (10 minutes)
        $this->stats = Cache::remember('portal_hero_stats', 600, function () {
            return [
                'journals' => Journal::where('enabled', true)->count(),
                'articles' => Submission::where('status', Submission::STATUS_PUBLISHED)->count(),
                'authors' => User::whereHas('roles', function ($query) {
                    $query->where('permission_level', Role::LEVEL_AUTHOR);
                })->count(),
                'downloads' => 50000, 
            ];
        });
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->suggestions = [];
            return;
        }

        // Search Journals
        $journals = Journal::where('enabled', true)
            ->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->query}%")
                  ->orWhere('description', 'ilike', "%{$this->query}%");
            })
            ->take(3)
            ->get()
            ->map(function ($journal) {
                return [
                    'type' => 'journal',
                    'title' => $journal->name,
                    'url' => route('journal.public.home', ['journal' => $journal->slug]),
                    'icon' => 'fa-solid fa-book',
                ];
            });

        // Search Articles
        $articles = Submission::where('status', Submission::STATUS_PUBLISHED)
            ->where('title', 'ilike', "%{$this->query}%")
            ->take(3)
            ->get()
            ->map(function ($article) {
                return [
                    'type' => 'article',
                    'title' => $article->title,
                    'url' => route('journal.article.view', ['journal' => $article->journal->slug, 'article' => $article->slug ?? $article->id]),
                    'icon' => 'fa-solid fa-file-lines',
                ];
            });

        $this->suggestions = $journals->merge($articles)->take(5)->toArray();
    }

    public function render()
    {
        return view('livewire.site.portal-hero');
    }
}
