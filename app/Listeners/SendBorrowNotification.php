<?php

namespace App\Listeners;

use App\Events\BookBorrowed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Books;

class SendBorrowNotification
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(BookBorrowed $event): void
    {
        $user = $event->user;
        $book = $event->book;
        Log::info("Book borrowed: {$user->name} is borrowed book is {$book->title}");
    }
}
