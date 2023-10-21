<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\UserMail;
use App\Models\User;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Http\Request;


class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(User $user, Request $request)
    {

        $emails = $request->all();
         SendEmailJob::dispatch($emails, $user);

    }

    //  TODO - BONUS: implement list method
    public function list()
    {

    }
}
