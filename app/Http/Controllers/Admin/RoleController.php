<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['permission:role']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Role::latest();
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $actionEdit = route('role.edit', $data->id);
                    $actionDelete = route('role.destroy', $data->id);
                    return "<div class='d-flex justify-content-center'>" .
                        view('components.action.edit', ['action' => $actionEdit]) .
                        view('components.action.delete', ['action' => $actionDelete, 'id' => $data->id]) .
                        "</div>";
                })
                ->editColumn('permissions', function ($query) {
                    $permission = "";
                    foreach ($query->permissions as $value) {
                        $permission .= "<span class='badge badge-primary m-1'>{$value->label}</span>";
                    }
                    return $permission;
                })
                ->rawColumns(['action', 'permissions'])
                // ->filterColumn('permissions', function ($query, $keyword) {
                //     $query->whereHas(
                //         'permissions',
                //         fn($q) =>
                //         $q->where('name', 'ilike', "%{$keyword}%")
                //     );
                // })
                ->make(true);
        }
        return view('admins.role.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::get();
        $permissionValue = [];
        return view('admins.role.create-edit', compact('permissions', 'permissionValue'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'required|array',
        ]);
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
            ]);

            if ($request->permissions) {
                $role->givePermissionTo($request->permissions);
            }
            DB::commit();
            return redirect()->route('role.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return redirect()->route('role.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permissions = Permission::get();
        $role = Role::with('permissions')->find($id);
        $permissionValue = $role->permissions->pluck('id')->toArray();
        return view('admins.role.create-edit', compact('role', 'permissions', 'permissionValue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'required|array',
            'is_allowed_edit' => 'boolean',
            'is_allowed_delete' => 'boolean',
            'is_allowed_create' => 'boolean',
            'is_superadmin' => 'boolean',
        ]);
        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'is_allowed_edit' => $request->is_allowed_edit ?? null,
                'is_allowed_delete' => $request->is_allowed_delete ?? null,
                'is_allowed_create' => $request->is_allowed_create ?? null,
                'is_superadmin' => $request->is_superadmin ?? false,
            ]);
            $role->syncPermissions($request->permissions);
            DB::commit();
            return redirect()->route('role.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            return redirect()->route('role.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index')->with('success', 'Data berhasil dihapus');
    }
}
