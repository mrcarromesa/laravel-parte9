<?php

namespace App\Jobs;

use App\Mail\SendEmailDevs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $devs = [];
    private $details = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($devs, $details)
    {
        $this->devs = $devs;
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $email = new SendEmailDevs($this->devs);
        Mail::to($this->details['email'])->send($email);
    }
}
