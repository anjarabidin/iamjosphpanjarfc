<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['permission:permission']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $permissions = Permission::latest();
            return DataTables::of($permissions)
                ->editColumn('created_at', function ($data) {
                    return $data->created_at->format('j M Y, h:i A');
                })
                ->addColumn('action', function ($data) {
                    $actionEdit = route('permission.edit', $data->id);
                    $actionDelete = route('permission.destroy', $data->id);
                    return "<div class='d-flex justify-content-center'>" .
                        view('components.action.edit', ['action' => $actionEdit, 'id' => $data->id]) .
                        view('components.action.delete', ['action' => $actionDelete, 'id' => $data->id]) .
                        "</div>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admins.permission.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.permission.create-edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        Permission::create($request->validated());
        return redirect()->route('permission.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('admins.permission.create-edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        return redirect()->route('permission.index')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permission.index')->with('success', 'Data berhasil dihapus');
    }
}
