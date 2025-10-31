<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendComplaintReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
        $this->onQueue('emails'); // konsisten dengan controller
    }

    public function handle(): void
    {
        // TODO: ganti dengan Mail::to(...)->send(...)
        Log::info('Kwitansi pengaduan dikirim', ['code' => $this->code]);
    }
}
