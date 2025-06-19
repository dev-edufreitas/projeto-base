<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// GraphQL Playground - só em desenvolvimento
if (config('app.debug')) {
    Route::get('/graphql-playground', function () {
        return view('lighthouse::playground');
    })->name('graphql.playground');
}
