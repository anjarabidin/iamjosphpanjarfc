<?php

namespace App\Http\Controllers;

use App\Traits\ResponseWithHttpStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, ResponseWithHttpStatus;
}
