<?php

use App\Http\Controllers\Webhooks\PagueDevWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/paguedev', PagueDevWebhookController::class);
