<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/info', function () {
    phpinfo();
})->name('info');

Route::get('/media-audio', function () {
    return Inertia\Inertia::render('Home');
})->name('media-audio.create');

Route::post('/media-audio', function (Request $request) {
    $vid = $request->media_audio_source_id;

    parse_str(
        file_get_contents(
            sprintf('https://www.youtube.com/get_video_info?video_id=%s', $vid)
        ),
        $info
    );

    $videoData = json_decode($info['player_response'], true);
    $videoDetails = $videoData['videoDetails'];
    $streamingData = $videoData['streamingData'];

    $video_title = $videoDetails['title'];

    $audio = last($streamingData['adaptiveFormats']);

    file_put_contents(storage_path('app/public/' . Str::kebab($video_title) . '.mp3'), fopen($audio['url'], 'r'));
    return redirect()->route('media-audio.create');
})->name('media-audio.store');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');
