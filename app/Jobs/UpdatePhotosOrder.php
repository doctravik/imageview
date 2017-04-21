<?php

namespace App\Jobs;

use App\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePhotosOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Album $album
     */
    public $album;

    /**
     * Updated photos.
     * 
     * @var \Illiminate\Support\Collection
     */
    public $photos;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Album $album, Collection $photos)
    {
        $this->album = $album;
        $this->photos = $photos;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->album->photos->each(function ($photo) {
            if($newPhoto = $this->photos->where('id', $photo->id)->first()) {
                $photo->setOrder($newPhoto['sort_order']);
            }
        });
    }
}
