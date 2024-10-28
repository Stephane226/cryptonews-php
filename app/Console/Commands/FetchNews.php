<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;


class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from CryptoPanic and store them in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */



     public function handle()
     {
         // Fetch news articles from the external API
         $token = env('CRYPTOPANIC_API_TOKEN');
         $response = Http::get("https://cryptopanic.com/api/v1/posts/?auth_token={$token}");
         
         // Retrieve existing news articles from Redis
         $existingNews = json_decode(Redis::get('news_articles'), true) ?? [];
         
         // Initialize an array for new news data
         $newNewsData = [];
     
         foreach ($response['results'] as $newsItem) {
             $publishedAt = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $newsItem['created_at'])->format('Y-m-d H:i:s');
     
           
             $coinName = $newsItem['currencies'][0]['title'] ?? 'N/A';
     
             // Store in database
             $newNewsData[] = News::updateOrCreate([
                 'title' => $newsItem['title'],
                 'published_at' => $publishedAt,
                 'coin' => $coinName,
             ]);
         }
     
         // Merge existing news with new news, avoiding duplicates
         foreach ($newNewsData as $newNewsItem) {
             if (!in_array($newNewsItem->id, array_column($existingNews, 'id'))) {
                 $existingNews[] = $newNewsItem; 
             }
         }
     
         // Cache the updated news articles in Redis
         Redis::set('news_articles', json_encode($existingNews));
     
         $this->info('News articles fetched, stored, and cached successfully!');
     }
     


     
     


}
