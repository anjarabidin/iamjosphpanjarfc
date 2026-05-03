<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Journal;
use App\Models\Issue;
use App\Models\Submission;
use App\Models\Section;
use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NativeXmlExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_native_xml_with_sections()
    {
        \Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        
        $user = User::factory()->create();
        $user->assignRole(['Super Admin']);

        $journal = Journal::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Test Journal',
            'path' => 'test-journal',
            'slug' => 'test-journal',
            'enabled' => true,
        ]);
        
        $issue = Issue::create([
            'journal_id' => $journal->id,
            'volume' => 1,
            'number' => 1,
            'year' => 2024,
            'title' => 'Test Issue',
            'is_published' => true,
            'url_path' => 'vol1-no1',
        ]);
        
        $section = Section::create([
            'journal_id' => $journal->id,
            'name' => 'Articles',
            'abbreviation' => 'ART',
        ]);
        
        $submission = Submission::create([
            'journal_id' => $journal->id,
            'section_id' => $section->id,
            'issue_id' => $issue->id,
            'user_id' => $user->id,
            'title' => 'Test Article',
            'submitted_at' => now(),
        ]);
        
        Publication::create([
            'submission_id' => $submission->id,
            'section_id' => $section->id,
            'issue_id' => $issue->id,
            'version' => 1,
            'status' => 3, // PUBLISHED
            'title' => 'Test Article',
            'url_path' => 'test-article-pub-' . uniqid(),
            'date_published' => now(),
        ]);

        // Mock author
        \App\Models\SubmissionAuthor::create([
            'submission_id' => $submission->id,
            'given_name' => 'John',
            'family_name' => 'Doe',
            'email' => 'john@example.com',
            'country' => 'US',
            'seq' => 1,
        ]);
        
        // Manually bind current journal for middleware/service access in test
        app()->instance('currentJournal', $journal);

        $url = route('journal.settings.tools.native.export.issues', ['journal' => $journal->path]);

        $response = $this->actingAs($user)->post($url, [
            'issue_ids' => [$issue->id],
        ]);
        
        $response->assertStatus(200);

        // Capture streamed content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        $this->assertStringContainsString('<issue ', $content);
        $this->assertStringContainsString('<sections>', $content);
        $this->assertStringContainsString('ref="ART"', $content);
        $this->assertStringContainsString('<articles>', $content);
        $this->assertStringContainsString('<article ', $content);
        $this->assertStringContainsString('<author ', $content);
        $this->assertStringContainsString('John', $content);
    }
}
