<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

Route::get('/media-audio', function () {
    $files = collect(Storage::files())
        ->filter(fn ($file) => $file !== '.gitignore')
        ->values();

    return Inertia\Inertia::render('Home', compact(
        'files',
    ));
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

    // file_put_contents(storage_path('app/public/' . Str::slug($video_title, '-') . '.mp3'), fopen($audio['url'], 'r'));
    Storage::put(Str::slug($video_title, '-') . '.txt', $videoDetails['title']);

    return Redirect::back()->with('success', 'File saved.');
})->name('media-audio.store');

Route::delete('/media-audio/{file_path}', function (Request $request) {

    Storage::delete($request->file_path);

    return Redirect::back()->with('success', 'File deleted.');
})->name('media-audio.destroy');
