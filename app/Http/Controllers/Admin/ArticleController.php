<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use Carbon\Carbon;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Article::orderBy('published_at', 'desc');
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $actionEdit = route('article.edit', $data->id);
                    $actionDelete = route('article.destroy', $data->id);
                    $actionToggleStatus = route('article.toggle-status', $data->id);
                    return "<div class='d-flex justify-content-center'>" .
                        view('components.action.toggle-status', [
                            'action' => $actionToggleStatus,
                            'id' => $data->id,
                            'isActive' => $data->is_active
                        ]) .
                        // view('components.action.edit', ['action' => $actionEdit]) .
                        view('components.action.delete', ['action' => $actionDelete, 'id' => $data->id]) .
                        "</div>";
                })
                ->addColumn('status', function ($data) {
                    return $data->is_active ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-danger">Draft</span>';
                })
                ->addColumn('publish_date', function ($data) {
                    return $data->published_at ? Carbon::parse($data->published_at)->format('d M Y H:i') : 'Not Published';
                })
                ->rawColumns(['action', 'status', 'publish_date'])
                ->make(true);
        }
        return view('admins.article.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.article.create-edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleRequest $request)
    {
        Article::create($request->validated());
        return redirect()->route('article.index')->with('success', 'Article created successfully.');
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
    public function edit(Article $article)
    {
        return view('admins.article.create-edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article->update($request->validated());
        return redirect()->route('article.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('article.index')->with('success', 'Artikel Berhasil Dihapus.');
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Article $article)
    {
        $article->update(['is_active' => !$article->is_active]);

        $status = $article->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Artikel telah {$status}.",
                'status' => $article->is_active
            ]);
        }

        return redirect()->route('article.index')->with('success', "Status artikel telah diubah menjadi {$status}.");
    }
}
