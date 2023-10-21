<?php

namespace App\Models;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Elasticsearch;
use Illuminate\Support\Facades\Redis;

use App\Jobs\MotivateUser;

class User extends Authenticatable implements ElasticsearchHelperInterface, RedisHelperInterface
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast as dates.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'email_verified_at',
        'last_email_sent_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Motivate the user
     *
     * @return void
     */
    public function motivate()
    {
        MotivateUser::dispatchNow($this);
    }

    /**
     * Create a greeting that we can display to the user.
     *
     * @param  bool  $smallTalk
     * @param  string  $salutation
     * @return string
     */
    public function getGreeting(bool $smallTalk = true, string $salutation): string
    {
        $greeting = "$salutation, {$this->name}!";

        if ($smallTalk) {
            $greeting .= " Lovely weather we are having!";
        }

        return $greeting;
    }

    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        $data = [
            'body' => [
                'body' => $messageBody,
                'subject' => $messageSubject,
                'email' => $toEmailAddress
            ],
            'index' => time(),
            'user_id' => $this->id
        ];
        $return = Elasticsearch::index($data);
        return  true;
    }

    public function storeRecentMessage(mixed $id, string $messageSubject, string $toEmailAddress): void
    {
        Redis::set($id, $messageSubject, $toEmailAddress);

    }
}
