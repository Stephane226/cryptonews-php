<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Redis;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        // Attempt to retrieve news from Redis
        $newsJson = Redis::get('news_articles');

        // If there's no data in Redis, fetch from the database
        if (!$newsJson) {
            $news = News::orderBy('published_at', 'desc')->take(20)->get(); 
            Redis::set('news_articles', json_encode($news));
        } else {
            // Use the cached data from Redis
            $news = json_decode($newsJson, true);
            usort($news, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });
           
            $news = array_slice($news, 0, 20);
        }

        if ($request->ajax()) {
            return response()->json($news);
        }

        return view('news.index', [
            'news' => $news,
        ]);
    }



    public function readme()
    {
        
        return view('news.readme', [
          
        ]);
    }

}
