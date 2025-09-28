<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PurgeDeletedServers;

class PurgeServersCommand extends Command
{
    protected $signature = 'servers:purge';
    protected $description = 'Elimina permanentemente (force delete) servers que llevan más de 7 días en estado eliminado.';

    public function handle(): int
    {
        dispatch_sync(new PurgeDeletedServers());

        $this->info('Proceso finalizado.');
        return Command::SUCCESS;
    }
}
