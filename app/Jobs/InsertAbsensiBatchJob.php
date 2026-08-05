<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

use App\Models\Absensi;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class InsertAbsensiBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $dataChunk;
    public function __construct(array $dataChunk)
    {
        //
        $this->dataChunk = $dataChunk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    //  dd($this->dataChunk);

        Absensi::create($this->dataChunk);
        logger('✅ Batch absensi berhasil di-insert. Jumlah data: ' . count($this->dataChunk));
    }
}
