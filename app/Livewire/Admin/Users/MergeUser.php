<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Services\MergeUserService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class MergeUser extends Component
{
    public $showModal = false;
    public $sourceUserId;
    public $sourceUser;
    public $targetUserId;
    public $confirmationText = '';
    public $potentialTargets = [];

    protected $listeners = ['openMergeModal'];

    public function openMergeModal($userId)
    {
        $this->sourceUserId = $userId;
        $this->sourceUser = User::find($userId);
        
        if (!$this->sourceUser) {
            session()->flash('error', 'User not found.');
            return;
        }

        // Check if user is Super Admin
        if ($this->sourceUser->hasRole('Super Admin')) {
            session()->flash('error', 'Cannot merge a Super Admin account.');
            return;
        }

        // Load potential target users (exclude source user and Super Admins)
        $mergeService = new MergeUserService();
        $this->potentialTargets = $mergeService->getPotentialTargets($this->sourceUser);

        $this->showModal = true;
        $this->reset(['targetUserId', 'confirmationText']);
    }

    public function executeMerge()
    {
        // Validation
        $this->validate([
            'targetUserId' => 'required|exists:users,id|different:sourceUserId',
            'confirmationText' => 'required|in:MERGE',
        ], [
            'confirmationText.in' => 'You must type "MERGE" to confirm this action.',
            'targetUserId.required' => 'Please select a target user.',
            'targetUserId.different' => 'Cannot merge a user into themselves.',
        ]);

        try {
            $sourceUser = User::findOrFail($this->sourceUserId);
            $targetUser = User::findOrFail($this->targetUserId);

            $mergeService = new MergeUserService();
            $mergeService->merge($sourceUser, $targetUser);

            session()->flash('success', "Successfully merged {$sourceUser->name} into {$targetUser->name}. All records have been transferred.");
            
            $this->showModal = false;
            
            // Redirect to refresh the page
            return redirect()->route(request()->route()->getName(), request()->route()->parameters());

        } catch (\Exception $e) {
            Log::error('Merge User Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to merge users: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['sourceUserId', 'targetUserId', 'confirmationText', 'potentialTargets']);
    }

    public function render()
    {
        return view('livewire.admin.users.merge-user');
    }
}
