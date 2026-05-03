<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Admin;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\AdminProfileRequest;

class AdminController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['permission:admin']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Admin::where('type', Admin::TYPE_ADMIN)->latest();
            return DataTables::of($data)
                ->addColumn('role', function ($query) {
                    $role = "";
                    foreach ($query->roles as $value) {
                        $role .= "<span class='badge badge-primary m-1'>{$value->name}</span>";
                    }
                    return $role;
                })
                ->addColumn('action', function ($data) {
                    $actionEdit = route('admin.edit', $data->id);
                    $actionShow = route('admin.show', $data->id);
                    $actionDelete = route('admin.destroy', $data->id);
                    return "<div class='d-flex justify-content-center'>" .
                        view('components.action.show', ['action' => $actionShow]) .
                        view('components.action.edit', ['action' => $actionEdit]) .
                        view('components.action.delete', ['action' => $actionDelete, 'id' => $data->id]) .
                        "</div>";
                })
                ->rawColumns(['action', 'role'])
                ->make(true);
        }
        return view('admins.admin.index');
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $roles = Role::select('id', 'name')->get();
        return view('admins.admin.create-edit', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(AdminRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->except('password');

                if (!empty($request->password)) {
                    $data['password'] = bcrypt($request->password);
                }

                if ($avatar = $request->file('avatar')) {
                    $data['avatar'] = 'storage/' . $avatar->store('images/avatars', 'public');
                }

                $admin = Admin::create($data);
                $admin->assignRole(Role::find($request->role_id)->name);
            });

            return redirect()->route('admin.index')->with('success', 'Berhasil menambah admin');
        } catch (\Exception $e) {
            // You can keep this for debugging during development
            Log::error($e);
            return redirect()->back()->with('error', 'Gagal menambah admin: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return view('admins.admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        $roles = Role::select('id', 'name')->get();
        return view('admins.admin.create-edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, Admin $admin)
    {
        DB::beginTransaction();
        try {
            $data = $request->except('password');

            if (! empty($request->password)) {
                $data['password'] = bcrypt($request->password);
            }

            if (! empty($avatar = $request->avatar)) {
                if (file_exists($admin->avatar)) {
                    unlink($admin->avatar);
                }
                $data['avatar'] = 'storage/' . $avatar->store('images/avatars', ['disk' => 'public']);
            }

            $admin->update($data);
            $admin->syncRoles(Role::find($request->role_id)->name);

            DB::commit();
            return redirect()->route('admin.index')->with('success', 'Berhasil update data');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('error', 'Gagal menambah admin: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        file_exists($admin->avatar) ? unlink($admin->avatar) : '';
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'Berhasil menghapus admin');
    }

    public function editProfile()
    {
        $admin = Auth::user();
        return view('admins.admin.edit-profile', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(AdminProfileRequest $request)
    {

        DB::beginTransaction();
        try {
            $admin = Admin::find(Auth::id());
            $data = $request->except('password');

            if (! empty($request->password)) {
                $data['password'] = bcrypt($request->password);
            }

            if (! empty($avatar = $request->avatar)) {
                if (file_exists($admin->avatar)) {
                    unlink($admin->avatar);
                }
                $data['avatar'] = 'storage/' . $avatar->store('images/avatars', ['disk' => 'public']);
            }

            $admin->update($data);

            DB::commit();
            return redirect()->back()->with('success', 'Berhasil update profile');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('error', 'Gagal update profile: ' . $th->getMessage())->withInput();
        }
    }
}
