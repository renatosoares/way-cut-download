<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessMediaAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $tries = 3;
    protected array $dataSource;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->dataSource = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger('start process', [storage_path()]);

        // $fileName = sprintf('%s.mp3', Str::slug($this->dataSource['title'], '-'));
        // $fileName = sprintf('test_%s.txt', md5(uniqid(rand(), true)));

        // Storage::put(
        //     $fileName,
        //     file_get_contents($this->dataSource['url']),
        //     ['lock' => true]
        // );

        $response = Http::post(route('media-audio.job'));

        // logger('file', [Storage::url($fileName), Storage::get($fileName)]);

    }
}
