<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Admin;
use App\Models\Author;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\AuthorExport;
use App\Imports\AuthorImport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuthorTemplateExport;
use App\Http\Requests\Admin\StoreAuthorRequest;
use App\Http\Requests\Admin\UpdateAuthorRequest;

class AuthorController extends Controller
{
    public function __construct()
    {
        // Add middleware if needed, e.g. permissions
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = Author::with('admin')->latest();

            return DataTables::of($query)
                ->addColumn('avatar', function ($data) {
                    if ($data->admin && $data->admin->avatar) {
                        return '<img src="' . asset($data->admin->avatar) . '" alt="Avatar" class="h-50px w-50px rounded-circle object-fit-cover" />';
                    }
                    $initials = strtoupper(substr($data->admin?->name ?? 'A', 0, 1));
                    return '<span class="symbol-label fs-2x fw-bold text-primary bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">' . $initials . '</span>';
                })
                ->addColumn('registration_number', function ($data) {
                    return $data->registration_number;
                })
                ->addColumn('name', function ($data) {
                    return $data->admin?->name ?? '-';
                })
                ->addColumn('email', function ($data) {
                    return $data->admin?->email ?? '-';
                })
                ->addColumn('institution', function ($data) {
                    return $data->institution ?? '-';
                })
                ->addColumn('is_active', function ($data) {
                    $badge = $data->admin?->is_active ? 'badge-light-success' : 'badge-light-danger';
                    $label = $data->admin?->is_active ? 'Aktif' : 'Nonaktif';
                    return '<span class="badge ' . $badge . '">' . $label . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $actionEdit = route('author.edit', $data->id);
                    $actionShow = route('author.show', $data->id);
                    $actionDelete = route('author.destroy', $data->id);
                    return "<div class='d-flex justify-content-center'>" .
                        view('components.action.show', ['action' => $actionShow]) .
                        view('components.action.edit', ['action' => $actionEdit]) .
                        view('components.action.delete', ['action' => $actionDelete, 'id' => $data->id]) .
                        "</div>";
                })
                ->filter(function ($instance) {
                    if (request()->has('search') && !empty(request('search')['value'])) {
                        $keyword = request('search')['value'];
                        $instance->where(function ($query) use ($keyword) {
                            $query->where('registration_number', 'like', "%{$keyword}%")
                                ->orWhere('institution', 'like', "%{$keyword}%")
                                ->orWhereHas('admin', function ($q) use ($keyword) {
                                    $q->where('name', 'like', "%{$keyword}%")
                                        ->orWhere('email', 'like', "%{$keyword}%");
                                });
                        });
                    }

                    if (request()->has('status') && !empty(request('status'))) {
                        $status = request('status');
                        $instance->whereHas('admin', function ($q) use ($status) {
                            $q->where('is_active', $status);
                        });
                    }
                })
                ->rawColumns(['avatar', 'is_active', 'action'])
                ->make(true);
        }

        return view('admins.author.index');
    }

    public function create()
    {
        return view('admins.author.create-edit');
    }

    public function store(StoreAuthorRequest $request)
    {
        try {
            DB::beginTransaction();

            $adminData = $request->only('name', 'email', 'avatar');
            $adminData['password'] = Hash::make($request->input('password'));
            $adminData['type'] = Admin::TYPE_AUTHOR;
            $adminData['is_active'] = $request->input('is_active', true);

            // Handle avatar upload if exists
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = 'avatars/' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('storage/avatars'), basename($filename));
                $adminData['avatar'] = 'storage/avatars/' . basename($filename);
            }
             $roles = Role::where('name', Role::ROLE_AUTHOR)->first();
            if (!$roles) {
                $roleId = Role::create(['name' => Role::ROLE_AUTHOR])->id;
            } else {
                $roleId = $roles->id;
            }
            $adminData['role_id'] = $roleId;

            $admin = Admin::create($adminData);
            $admin->assignRole(Role::find($roleId)->name);
            // Generate registration number
            $registrationNumber = $this->generateRegistrationNumber();

            $authorData = [
                'admin_id' => $admin->id,
                'avatar' => $adminData['avatar'] ?? null,
                'registration_number' => $registrationNumber,
                'institution' => $request->input('institution'),
                'phone' => $request->input('phone'),
            ];

            Author::create($authorData);

            DB::commit();
            return redirect()->route('author.index')->with('success', 'Berhasil menambah author');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            dd($th);
            return redirect()->back()->with('error', 'Gagal menambah author: ' . $th->getMessage())->withInput();
        }
    }

    public function show(Author $author)
    {
        return view('admins.author.show', compact('author'));
    }

    public function edit(Author $author)
    {
        return view('admins.author.create-edit', compact('author'));
    }

    public function update(UpdateAuthorRequest $request, Author $author)
    {
        try {
            DB::beginTransaction();
            $admin = $author->admin;

            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->is_active = $request->input('is_active', $admin->is_active);

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = 'avatars/' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('storage/avatars'), basename($filename));
                $admin->avatar = 'storage/avatars/' . basename($filename);
            }

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->input('password'));
            }

            $admin->save();

            $author->institution = $request->input('institution');
            $author->phone = $request->input('phone');
            // avatar no separate change; we mirror admin avatar
            $author->avatar = $admin->avatar;
            $author->save();

            DB::commit();
            return redirect()->route('author.index')->with('success', 'Berhasil update author');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('error', 'Gagal update author: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy(Author $author)
    {
        try {
            DB::transaction(function () use ($author) {
                $admin = $author->admin;

                if ($admin->avatar && file_exists(public_path($admin->avatar))) {
                    unlink(public_path($admin->avatar));
                }

                $author->delete();
                $admin->delete();
            });

            return redirect()->route('author.index')->with('success', 'Berhasil menghapus author');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Gagal menghapus author: ' . $e->getMessage());
        }
    }

    /**
     * Export authors to Excel
     */
    public function exportData()
    {
        try {
            return Excel::download(
                new AuthorExport(),
                'Authors_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    /**
     * Download template import
     */
    public function downloadTemplate()
    {
        try {
            return Excel::download(
                new AuthorTemplateExport(),
                'Template_Author.xlsx'
            );
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }

    /**
     * Import authors from Excel
     */
    public function importData(Request $request)
    {
        try {
            $request->validate([
                'import_file' => 'required|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new AuthorImport(), $request->file('import_file'));

            return redirect()->route('author.index')->with('success', 'Import data berhasil');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    private function generateRegistrationNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'AUTH-'. $year . '-';

        // get last registration for this year
        $last = Author::where('registration_number', 'like', $prefix . '%')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$last) {
            $number = 1;
        } else {
            $suffix = str_replace($prefix, '', $last->registration_number);
            $number = intval($suffix) + 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
