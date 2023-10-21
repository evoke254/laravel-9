<?php

namespace App\Jobs;

use App\Mail\UserMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $emailData){

            $email = new UserMail($this->data);

              $this->user->storeRecentMessage(
                  $this->job->getJobId(),
                  $this->data['subject'],
                  $this->data['email'],
              );
            $this->user->storeEmail(
                  $this->data['body'],
                  $this->data['subject'],
                  $this->data['email'],
            );
            Mail::to($this->data['email'])->send($email);
        }
    }
}
