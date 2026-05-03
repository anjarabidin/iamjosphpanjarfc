<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->integer('permission_level')->default(3)->after('guard_name');
            $table->boolean('permit_submission')->default(false)->after('permission_level');
            $table->boolean('permit_review')->default(false)->after('permit_submission');
            $table->boolean('permit_copyediting')->default(false)->after('permit_review');
            $table->boolean('permit_production')->default(false)->after('permit_copyediting');
        });

        // Seed Default Roles with correct permissions
        // Levels: 1=Manager/Admin, 2=Editor/Section Editor, 3=Assistant/Reviewer/Author
        
        $roles_config = [
            'Journal Manager' => [
                'level' => 1,
                'permits' => ['permit_submission', 'permit_review', 'permit_copyediting', 'permit_production']
            ],
            'Editor' => [
                'level' => 2,
                'permits' => ['permit_submission', 'permit_review', 'permit_copyediting', 'permit_production']
            ],
            'Section Editor' => [
                'level' => 2,
                'permits' => ['permit_review', 'permit_copyediting']
            ],
            'Reviewer' => [
                'level' => 3,
                'permits' => ['permit_review']
            ],
            'Author' => [
                'level' => 3,
                'permits' => ['permit_submission']
            ],
            'Reader' => [
                'level' => 3,
                'permits' => []
            ],
            'Super Admin' => [
                'level' => 0, 
                'permits' => ['permit_submission', 'permit_review', 'permit_copyediting', 'permit_production']
            ],
            'Admin' => [
                 'level' => 1,
                 'permits' => ['permit_submission', 'permit_review', 'permit_copyediting', 'permit_production']
            ]
        ];

        foreach ($roles_config as $roleName => $config) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->permission_level = $config['level'];
                $role->permit_submission = in_array('permit_submission', $config['permits']);
                $role->permit_review = in_array('permit_review', $config['permits']);
                $role->permit_copyediting = in_array('permit_copyediting', $config['permits']);
                $role->permit_production = in_array('permit_production', $config['permits']);
                $role->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn([
                'permission_level',
                'permit_submission',
                'permit_review',
                'permit_copyediting',
                'permit_production'
            ]);
        });
    }
};
