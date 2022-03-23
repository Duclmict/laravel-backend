<?php

namespace App\Console\Commands\Example;
use Illuminate\Console\Command;
use App\Services\CustomLogService;
use App\Helpers\Helper;

class Example extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'ex:example {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example';

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
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "[Custom ExactlyRunSendMail] Send Mail start");

        $date = empty($this->argument('date')) 
                ? null : $this->argument('date');

        if(!Helper::checkDateFormat($date, 'YYYY-MM-DD'))
        {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, 
                "[Custom ExactlyRunSendMail] Send Mail 
                date format not found (YYYY-MM-DD)");
            return;
        }
    
        // todo
        
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "[Custom ExactlyRunSendMail] Send Mail success");
    }
}