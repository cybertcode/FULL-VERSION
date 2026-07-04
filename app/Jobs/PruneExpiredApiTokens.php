<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PruneExpiredApiTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $deleted = DB::table('personal_access_tokens')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        Log::info("PruneExpiredApiTokens: {$deleted} tokens eliminados.");
    }
}
