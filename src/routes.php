<?php

Route::get('timezones/{timezone}',
    'Masoudjahromi\LaravelCassandra\CassandraController@index');