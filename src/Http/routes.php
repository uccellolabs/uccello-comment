<?php

Route::middleware('web', 'auth')
->namespace('Uccello\Comment\Http\Controllers')
->name('uccello.comment.')
->group(function() {

    // This makes it possible to adapt the parameters according to the use or not of the multi domains
    if (!uccello()->useMultiDomains()) {
        $domainParam = '';
        $domainAndModuleParams = '{module}';
    } else {
        $domainParam = '{domain}';
        $domainAndModuleParams = '{domain}/{module}';
    }

    Route::post($domainParam.'/comment/save', 'CommentController@save')
    ->name('save');

    Route::post($domainParam.'/comment/delete', 'CommentController@delete')
    ->name('delete');
});
