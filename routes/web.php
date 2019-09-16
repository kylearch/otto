<?php

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

use App\Helpers\Plaid\Client;
use App\Models\Finance\Institution;
use App\Models\Finance\Item;
use App\Models\Storage\Document;
use ColorThief\ColorThief;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
    $client = new Client();
    foreach (Item::all() as $item) {
        $institution_data = $client->item($item)->institutionData();
        $file_name        = "public/img/institutions/logos/{$item->institution->institution_id}_round.png";
        Storage::put($file_name, base64_decode($institution_data->logo));

        $filePath = storage_path("app/public/img/institutions/logos/{$item->institution->institution_id}_round.png");
        $savePath = str_replace('_round.png', '.png', $filePath);

        $palette  = ColorThief::getPalette($filePath, 2);
        $dominant = array_shift($palette);

        $img    = @imagecreatefrompng($filePath);
        $width  = imagesx($img);
        $height = imagesy($img);

        $backgroundImg = @imagecreatetruecolor($width, $height);
        $color         = imagecolorallocate($backgroundImg, $dominant[0], $dominant[1], $dominant[2]);

        imagefill($backgroundImg, 0, 0, $color);
        imagecopy($backgroundImg, $img, 0, 0, 0, 0, $width, $height);
        imagepng($backgroundImg, $savePath, 0);
    }
});

Route::get('doc', function () {
    /** @var Document $document */
    $document = Document::orderByDesc('id')->first();
    $document->addMedia(storage_path('app/public/tmp/image.png'))->toMediaCollection('tmp');
    $media = $document->getFirstMedia('tmp')->getTypeFromMime();
    dd($media);
});
Route::get('dashboard', 'DashboardController@index')->name('dashboard');

Route::resource('accounts', 'AccountController');

Route::prefix('plaid')->name('plaid.')->group(function () {
    Route::get('link', 'PlaidController@link')->name('link');
});

Route::get('documents/{document}/download', 'DocumentController@download')->name('documents.download');
Route::resource('documents', 'DocumentController');

