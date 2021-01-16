<?php

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/media-audio', function () {
    $files = collect(Storage::files())
        ->filter(fn ($file) => $file !== '.gitignore')
        ->values();

    return response()->json($files);
})->name('media-audio.index');

Route::post('/media-audio', function (Request $request) {
    $vid = $request->media_audio_source_id;

    parse_str(
        file_get_contents(
            sprintf('https://www.youtube.com/get_video_info?video_id=%s', $vid)
        ),
        $info
    );

    $videoData = json_decode(
        data_get($info, 'player_response', []),
        true
    );


    $videoDetails =  data_get($videoData, 'videoDetails', []);
    $streamingData = data_get($videoData, 'streamingData', []);

    $video_title = data_get($videoDetails, 'title', 'untitled');

    $audio = last(data_get($streamingData, 'adaptiveFormats', []));

    //Storage::put(Str::slug($video_title, '-') . '.mp3', file_get_contents(data_get($audio, 'url', '')));
    Storage::put(Str::slug($video_title, '-') . '.txt', $video_title);

    return response()->json([
        'status' => 'success',
        'message' => 'File saved',
    ]);
})->name('media-audio.store');

Route::delete('/media-audio/{file_path}', function (Request $request) {

    Storage::delete($request->file_path);

    return Redirect::back()->with('success', 'File deleted.');
})->name('media-audio.destroy');
