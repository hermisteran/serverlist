<?php

namespace App\Jobs;

use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PurgeDeletedServers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $cutoff = Carbon::now()->subDays(7);

        $servers = Server::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();

        foreach ($servers as $server) {
            /**
             * eliminar imagen si existe
             */
            if ($server->image && Storage::disk('public')->exists($server->image)) {
                Storage::disk('public')->delete($server->image);
            }
            /**
             * Eliminar físicamente el registro
             */
            $server->forceDelete();
        }
    }
}
