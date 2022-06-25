<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\PublicKey;
use Spatie\Crypto\Rsa\PrivateKey;
use Illuminate\Contracts\Encryption\Encrypter;
use Spatie\Crypto\Exceptions\CouldNotDecryptData;

class TestController extends Controller
{
    public function index ()
    {
        $data = [
            'phone' => '01007220514',
            'name' => 'hambozo',
            'password' => '379dfbc420b5b0ffb262739c8b38e9551a1997f5c7570c359ae2f83f639414ac',
            'typeOfUser' => 'user'
        ];
        $data = json_encode($data);
        // $data = json_decode($data, true);
        // dd($data);

        $publicKey = PublicKey::fromFile(storage_path('publicKey.pem'), 'my-password');
        $encryptedData = $publicKey->encrypt($data); // returns something unreadable
        $encryptedData = base64_encode($encryptedData);


        $key = base64_decode($encryptedData);
        $privateKey = PrivateKey::fromFile(storage_path('privateKey.pem'), 'my-password');
        $decryptedData = $privateKey->decrypt($key);
        $decryptedData = json_decode($decryptedData, true);
        
        dd($encryptedData , $decryptedData);
    }

    public function decrypt(Request $request)
    {
        $key = $request->key;
        $key_decode = base64_decode($key);
        dd($key, $key_decode);
        $privateKey = PrivateKey::fromFile(storage_path('privateKey.pem'), 'my-password');
        $decryptedData = $privateKey->decrypt($key); 
        $decryptedData = json_decode($decryptedData, true);
        return $decryptedData;
    }
}


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


    // [$privateKey, $publicKey] = (new KeyPair())->generate();
    // [$passwordProtectedPrivateKey, $publicKey] = (new KeyPair())->password('my-password')->generate();
    // dd($privateKey, $publicKey);

    // $data = [
    //     'name' => 'John Doe',
    //     'email' => 'mo@gmail.com',
    //     'subject' => 'Test Subject'
    // ];
    // $data = json_encode($data);
    // $data= json_decode($data, true);
    // dd($data);

    // $privateKey = PrivateKey::fromFile(storage_path('privateKey.pem'), 'my-password');
    // $encryptedData = $privateKey->encrypt($data); // returns something unreadable
    // // dd($encryptedData);
    
    // $publicKey = PublicKey::fromFile(storage_path('publicKey.pem'));
    // $decryptedData = $publicKey->decrypt($encryptedData); // returns 'my secret data'
    
    // dd($encryptedData, $decryptedData);