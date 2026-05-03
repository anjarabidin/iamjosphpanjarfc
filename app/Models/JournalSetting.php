<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalSetting extends Model
{
    protected $fillable = [
        'journal_id',
        'setting_name',
        'setting_value',
        'setting_type',
        'group',
    ];

    /**
     * Get the journal that owns this setting.
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the properly typed value.
     */
    public function getValueAttribute()
    {
        return match ($this->setting_type) {
            'boolean' => filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->setting_value, true),
            'integer' => (int) $this->setting_value,
            default => $this->setting_value,
        };
    }

    /**
     * Set a setting for a journal.
     */
    public static function set(Journal|string $journal, string $name, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        $journalId = $journal instanceof Journal ? $journal->id : $journal;

        // Handle value based on type
        $storedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };

        return self::updateOrCreate(
            ['journal_id' => $journalId, 'setting_name' => $name],
            ['setting_value' => $storedValue, 'setting_type' => $type, 'group' => $group]
        );
    }

    /**
     * Get a setting for a journal.
     */
    public static function get(Journal|string $journal, string $name, mixed $default = null): mixed
    {
        $journalId = $journal instanceof Journal ? $journal->id : $journal;

        $setting = self::where('journal_id', $journalId)
            ->where('setting_name', $name)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Get all settings for a journal, optionally filtered by group.
     */
    public static function getAllForJournal(Journal|string $journal, ?string $group = null): array
    {
        $journalId = $journal instanceof Journal ? $journal->id : $journal;

        $query = self::where('journal_id', $journalId);

        if ($group) {
            $query->where('group', $group);
        }

        return $query->get()->pluck('value', 'setting_name')->toArray();
    }
}
