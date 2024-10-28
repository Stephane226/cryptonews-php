<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class NewsFilterController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query();

        // Apply filters
        if ($request->has('coin') && $request->input('coin') != '') {
            $query->where('coin', $request->input('coin'));
        }
        if ($request->has('start_date') && $request->input('start_date') != '') {
            $query->where('published_at', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date') && $request->input('end_date') != '') {
            $query->where('published_at', '<=', $request->input('end_date'));
        }

        $news = $query->get()->toArray(); // Fetch filtered results from the database

        // Paginate the results
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedNews = array_slice($news, $offset, $perPage);
        $total = count($news);
        $lastPage = ceil($total / $perPage);

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json($paginatedNews);
        }

        // Return filtered view
        return view('news.filtered', [
            'news' => $paginatedNews,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
        ]);
    }
}
