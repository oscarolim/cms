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

Auth::routes(['register' => false]);

Route::post('/files', 'FileController@store')->name('upload-file');

Route::get('/cms', 'CMS\DashboardController@index')->middleware('auth')->name('dashboard');
Route::get('/cms/dashboard', 'CMS\DashboardController@index')->middleware('auth')->name('dashboard');

Route::get('/cms/pages', 'CMS\SitemapController@index')->name('sitemap');
Route::get('/cms/pages/create', 'CMS\SitemapController@create');
Route::post('/cms/pages', 'CMS\SitemapController@store');
Route::get('/cms/pages/{sitemap}/edit', 'CMS\SitemapController@edit');
Route::put('/cms/pages/{sitemap}', 'CMS\SitemapController@update');
Route::delete('/cms/pages/{sitemap}', 'CMS\SitemapController@destroy');
Route::put('/cms/pages/{sitemap}/published', 'CMS\SitemapController@published');
Route::put('/cms/pages/{sitemap}/move/{direction}', 'CMS\SitemapController@move');
Route::get('/cms/pages/{sitemap}/block', 'CMS\SitemapController@createBlock');
Route::post('/cms/pages/{sitemap}/block', 'CMS\SitemapController@updateBlock');
Route::post('/cms/pages/{sitemap}/structure', 'CMS\SitemapController@updateStructure');

Route::get('/', 'HomeController@index')->name('home');
Route::get("{path}", "HomeController@index")->where('path', '.+');