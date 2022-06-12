<?php

use App\Mail\OTP;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CompaniesController;

use Spatie\Crypto\Rsa\PrivateKey;
use Spatie\Crypto\Rsa\PublicKey;
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

Route::get('/test', function () {

    // Generate a new private (and public) key pair
    // $privkey = openssl_pkey_new(array(
    //     "digest_alg" => 'md5',
    //     "private_key_bits" => 2048,
    //     "private_key_type" => OPENSSL_KEYTYPE_RSA,
    // ));
    // $key_details = openssl_pkey_get_details($privkey);
    // print_r($key_details);

    // new keys
    // $config = array(
    //     "digest_alg" => "sha512",
    //     "private_key_bits" => 2048,
    //     "private_key_type" => OPENSSL_KEYTYPE_RSA,
    // );

    // // Create the keypair  
    // $res = openssl_pkey_new($config);
    // // Get private key  
    // openssl_pkey_export($res, $privkey);
    // // Get public key  
    // $pubkey = openssl_pkey_get_details($res);

    // $pubkey = $pubkey["key"];

    // echo "====PKCS1 RSA Key in Non Encrypted Format ====\n";
    // var_dump($privkey);
    // echo "\n";
    // echo "====PKCS1 RSA Key in Encrypted Format====\n ";

    // // Get private key in Encrypted Format  
    // openssl_pkey_export($res, $privkey, "myverystrongpassword");
    // // Get public key  
    // $pubkey = openssl_pkey_get_details($res);
    // $pubkey = $pubkey["key"];
    // var_dump($privkey);
    // echo "\n";
    // echo "RSA Public Key \n ";
    // var_dump($pubkey);
    // dd($privkey , $pubkey);

    $data = 'my secret data';

    $privateKey = PrivateKey::fromFile(storage_path('app/privateKey.pem'));
    $encryptedData = $privateKey->encrypt($data); // returns something unreadable

    $publicKey = PublicKey::fromFile(storage_path('app/publicKey.pem'));
    $decryptedData = $publicKey->decrypt($encryptedData); // returns 'my secret data'

    dd($encryptedData, $decryptedData);

});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/charge_wallet', [WalletController::class, 'charge'])->name('charge');
Route::post('/charge_wallet', [WalletController::class, 'update'])->name('charge_wallet');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// get route request
Route::get('/clients/payments', [ClientsController::class, 'payments']);
Route::get('/clients/wallet', [ClientsController::class, 'wallet']);

Route::get('/companies/payments', [CompaniesController::class, 'payments']);
Route::get('/companies_service', [CompaniesController::class, 'findByService']);
