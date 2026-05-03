<?php

use App\Models\Journal;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$journal = Journal::where('slug', 'medika')->first();
$editorRoles = ['Editor', 'Editor-in-Chief', 'Section Editor', 'Journal Manager'];

foreach ($editorRoles as $roleName) {
    $users = $journal->usersWithRole($roleName)->get();
    echo "Checking role: $roleName\n";
    echo "Type of users: " . get_class($users) . " Count: " . $users->count() . "\n";
    foreach ($users as $user) {
        echo "Element type: " . gettype($user) . "\n";
    }
}
