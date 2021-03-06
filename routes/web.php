<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', 'CategoriesController@index')->name('home');

// other routes that requires auth middleware
Route::middleware('auth')->group(function () {
    Route::post('topics/{topic}', 'RepliesController@store')->name('replies.store');
});

// categories
Route::prefix('categories')->group(function () {
    Route::name('moderators.')->group(function () {
        Route::get('{category}/moderators/create', 'ModeratorController@create')->name('create');
        Route::post('{category}/moderators', 'ModeratorController@store')->name('store');
        Route::delete('{category}/moderators/{user}', 'ModeratorController@destroy')->name('destroy');
    });

    Route::name('categories.')->group(function () {
        Route::middleware('role:administrator')->group(function () {
            Route::get('create', 'CategoriesController@create')->name('create');
            Route::post('', 'CategoriesController@store')->name('store');
            Route::get('{category}/edit','CategoriesController@edit')->name('edit');
            Route::patch('{category}','CategoriesController@update')->name('update');
            Route::delete('{category}', 'CategoriesController@destroy')->name('destroy');
        });
    });
});

// moderators
Route::prefix('moderators')->group(function () {
    Route::name('moderators.')->group(function () {
        Route::middleware('role:administrator')->group(function () {
            Route::get('', 'ModeratorController@list')->name('list');
        });
    });
});

// channels
Route::prefix('channels')->group(function () {
    Route::name('channels.')->group(function () {
        Route::middleware('role:administrator')->group(function () {
            Route::get('/create', 'ChannelsController@create')->name('create');
            Route::post('', 'ChannelsController@store')->name('store');
            Route::get('{channel}/edit','ChannelsController@edit')->name('edit');
            Route::patch('{channel}','ChannelsController@update')->name('update');
            Route::delete('{channel}', 'ChannelsController@destroy')->name('destroy');
        });

        Route::get('{channel}', 'ChannelsController@show')->name('show');
    });
});

// topics
Route::name('topics.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('channels/{channel}/topics/create', 'TopicsController@create')->name('create');
        Route::post('channels/{channel}/topics', 'TopicsController@store')->name('store');
    });

    Route::prefix('topics')->group(function () {
        Route::get('{topic}', 'TopicsController@show')->name('show');

        Route::middleware('can:manage,topic')->group(function () {
            Route::get('{topic}/edit','TopicsController@edit')->name('edit');
            Route::patch('{topic}','TopicsController@update')->name('update');
            Route::delete('{topic}', 'TopicsController@destroy')->name('destroy');
        });
    });
});

// replies
Route::prefix('replies')->group(function () {
    Route::name('response.')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('{reply}', 'RepliesController@createResponse')->name('create');
            Route::post('{reply}', 'RepliesController@storeResponse')->name('store');
        });
    });

    Route::name('replies.')->group(function () {
        Route::middleware('can:manage,reply')->group(function () {
            Route::get('{reply}/edit','RepliesController@edit')->name('edit');
            Route::patch('{reply}','RepliesController@update')->name('update');
            Route::delete('{reply}', 'RepliesController@destroy')->name('destroy');
        });
    });
});

// users
Route::prefix('users')->group(function () {
    Route::name('users.')->group(function () {
        Route::get('stats', 'UsersController@stats')->name('stats');

        Route::middleware('auth')->group(function () {
            Route::get('{user}', 'UsersController@show')->name('show');
        });

        Route::middleware('can:manage,user')->group(function () {
            Route::get('{user}/edit','UsersController@edit')->name('edit');
            Route::patch('{user}','UsersController@update')->name('update');
            Route::delete('{user}', 'UsersController@destroy')->name('destroy');
        });
    });
});

// reports
Route::name('report.')->group(function () {
    Route::prefix('report')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('{reply}', 'ReportController@create')->name('create');
            Route::post('{reply}', 'ReportController@store')->name('store');
        });
    });

    Route::middleware('role:moderator|administrator')->group(function () {
        Route::prefix('reports')->group(function () {
            Route::get('', 'ReportController@index')->name('index');
            Route::post('{report}/ignore', 'ReportController@ignore')->name('ignore');
            Route::post('{report}/delete', 'ReportController@delete')->name('delete');
        });

        Route::get('/users/{user}/reports', 'ReportController@show')->name('show');
    });
});

// ban
Route::prefix('users')->group(function () {
    Route::name('ban.')->group(function () {
        Route::middleware('role:moderator|administrator')->group(function () {
            Route::get('{user}/ban', 'BanController@create')->name('create');
            Route::post('{user}/ban', 'BanController@store')->name('store');
        });
    });
});

// pages
Route::name('pages.')->group(function () {
    Route::view('/terms', 'pages.terms')->name('terms');
    Route::view('/policy', 'pages.policy')->name('policy');
});