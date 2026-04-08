<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Upload PDF and index chunks in Pinecone
Route::post('/upload', [DocumentController::class, 'upload']);

// Ask question based on indexed chunks
Route::post('/ask', [ChatController::class, 'ask']);
