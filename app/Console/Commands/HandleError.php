<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class HandleError extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'common:errorhandle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically resend mail or message when had error';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //todo
    }
}
