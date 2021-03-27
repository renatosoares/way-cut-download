<?php

use App\Jobs\ProcessMediaAudio;
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

Route::get('/accessible-media-audio', function (Request $request) {
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

    $title = data_get($videoDetails, 'title', 'untitled');

    $adaptiveFormats = collect(data_get($streamingData, 'adaptiveFormats', []));

    $audios = $adaptiveFormats->filter(function ($formats) {
        return Str::startsWith(
            data_get($formats, 'mimeType', ''),
            'audio'
        );
    })
        ->map(function ($formats) use ($title){
            return [
                'title' => $title,
                'approxDurationMs' => data_get($formats, 'approxDurationMs', ''),
                'audioQuality' => data_get($formats, 'audioQuality', ''),
                'mimeType' => data_get($formats, 'mimeType', ''),
                'url' => data_get($formats, 'url', ''),
            ];
        })
        ->values();

    return response()->json($audios);
});

Route::get('/media-audio', function () {
    $files = collect(Storage::files())
        ->filter(fn ($file) => $file !== '.gitignore')
        ->values();

    return response()->json($files);
})->name('media-audio.index');

Route::post('/media-audio', function (Request $request) {
    $fileName = sprintf(
        '%s_%s.mp3',
        Str::slug($request->audio_title, '-'),
        md5(uniqid(rand(), true))
    );

    $handle1 = fopen(
        $request->audio_url, "r"
    );

    $handle2 = fopen(
        storage_path('app/public/' . $fileName), "w"
    );

    stream_copy_to_stream($handle1, $handle2);

    fclose($handle1);
    fclose($handle2);

    logger('file', [Storage::url($fileName)]);

    return response()->json([
        'status' => 'success',
        'message' => 'File saved',
    ]);
})->name('media-audio.store');

Route::post('/media-audio/job', function (Request $request) {

    ProcessMediaAudio::dispatch([
        'audio_title' => $request->audio_title,
        'audio_url' => $request->audio_url,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'File processing',
    ]);
})->name('media-audio.job');

Route::delete('/media-audio/{file_path}', function (Request $request) {

    Storage::delete($request->file_path);

    return Redirect::back()->with('success', 'File deleted.');
})->name('media-audio.destroy');
