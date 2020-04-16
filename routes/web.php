<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/cms/dashboard', 'CMS\DashboardController@index')->middleware('auth')->name('dashboard');

Route::get('/cms/sitemap', 'CMS\SitemapController@index')->name('sitemap');
Route::post('/cms/sitemap', 'CMS\SitemapController@store');
Route::get('/cms/sitemap/create', 'CMS\SitemapController@create');
Route::get('/cms/sitemap/{sitemap}/edit', 'CMS\SitemapController@edit');
Route::put('/cms/sitemap/{sitemap}', 'CMS\SitemapController@update');
Route::put('/cms/sitemap/{sitemap}/published', 'CMS\SitemapController@published');
Route::delete('/cms/sitemap/{sitemap}', 'CMS\SitemapController@destroy');