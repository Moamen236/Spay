<?php

namespace App\Http\Controllers;

use App\Models\otp;
use App\Mail\OTPMail;
use App\Models\client;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     /**
     * Register new client.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // $data = $request->all();
        $clients = new client();
        $companies = new Company();

        $validator = $this->validateRegisterData($request);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], );
        }

        if($request->typeOfUser == 'user'){
            $client = $clients->findByPhone($request->phone);
            if ($client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number already exist'
                ]);
            }else{
                $password = $request->password;
                $salt = $request->salt;
                $password = str_replace($salt, '', $password);

                ini_set('memory_limit', '2048M');
                $file = file_get_contents(storage_path('app/hash.txt'));
                $file = explode("\n", $file);
                foreach ($file as $line) {
                    if($line == $password){
                        return response()->json([
                            'status' => false,
                            'message' => 'Password is not secure'
                        ]);
                    }
                }
                
                $client = $clients->create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'salt' => $request->salt,
                ]);
                $this->generateOtp($client['id'], $client['data']['name'], $client['data']['email']);
                return response()->json([
                    'status' => true,
                    'message' => 'client registered successfully',
                    'data' => [
                        'id' => $client['id'],
                        'name' => $client['data']['name'],
                        'phone' => $client['data']['phone'],
                        'email' => $client['data']['email'],
                    ]
                ]);
            }
        }elseif($request->typeOfUser == 'company'){
            $company = $companies->findByEmail($request->email);
            if ($company) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email Address already exist'
                ]);
            }else{
                $password = $this->generatePasswordForCompany();
                $company = $companies->create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'service' => $request->service,
                    'password' => $password, //hash('sha256', $password)
                    'bank_account' => $request->bank_account,
                    'commercial' => $request->commercial,
                    'tax_number' => $request->tax_number,
                    'personal_id' => $request->personal_id,
                ]);
                // $this->generateOtp($company);
                return response()->json([
                    'status' => true,
                    'message' => 'Company registered successfully',
                    'data' => [
                        'id' => $company->id(),
                        'name' => $company->data()['name'],
                        'service' => $company->data()['service'],
                        'email' => $company->data()['email'],
                        'bank_account' => $company->data()['bank_account'],
                        'commercial' => $company->data()['commercial'],
                        'tax_number' => $company->data()['tax_number'],
                        'personal_id' => $company->data()['personal_id'],
                    ]
                ]);
            }

        }
    }

    /**
     * Login client and create token
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $clients = new client();
        $companies = new Company();

        $validator = $this->validateLoginData($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        } 

        if($request->typeOfUser == 'user'){
            $client = $clients->findByPhone($request->phone);
            if ($client) {
                $client_data = $client->data();
                $client_password = $client_data['password'];
                $request_password = $request->password . $client_data['salt'];
                if ($client_password == $request_password) {
                    return response()->json([
                        'status' => true,
                        'message' => 'login success',
                        'data' => [
                            'id' => $client->id(),
                            'name' => $client->data()['name'],
                            'phone' => $client->data()['phone'],
                        ],
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Password is incorrect'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number is incorrect'
                ]);
            }
        }elseif($request->typeOfUser == 'company'){ 
            $company = $companies->findByEmail($request->email);
            if ($company) {
                $company_data = $company->data();
                $company_password = $company_data['password'];
                $request_password = $request->password;
                if ($company_password == $request_password) {
                    return response()->json([
                        'status' => true,
                        'message' => 'login success',
                        'data' => [
                            'id' => $company->id(),
                            'name' => $company->data()['name'],
                            'email' => $company->data()['email'],
                            'bank_account' => $company->data()['bank_account'],
                            'commercial' => $company->data()['commercial'],
                            'tax_number' => $company->data()['tax_number'],
                            'personal_id' => $company->data()['personal_id'],
                        ],
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Password is incorrect'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Email Address is incorrect'
                ]);
            }
        }
        
    }

    /**
     * validate Register data
     *
     * @return \Illuminate\Http\Response
     */
    public function validateRegisterData(Request $request)
    {
        if ($request->typeOfUser == 'user') {
            $validator =  Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:11',
                'password' => 'required|string',
                'salt' => 'required|string'
            ]);
        } elseif ($request->typeOfUser == 'company') {
            $validator =  Validator::make($request->all(), [
                'name' => 'required|string',
                'service' => 'required|string',
                'email' => 'required|email',
                // 'password' => 'required|string',
                'bank_account' => 'required|string',
                'commercial' => 'required|string',
                'tax_number' => 'required|string',
                'personal_id' => 'required|string',
            ]);
        }
        
        return $validator;
    }

    /**
     * validate Login data
     *
     * @return \Illuminate\Http\Response
     */
    public function validateLoginData(Request $request)
    {
        if ($request->typeOfUser == 'user') {
            $validator =  Validator::make($request->all(), [
                'phone' => 'required|string|max:11',
                'password' => 'required|string',
            ]);
        } elseif ($request->typeOfUser == 'company') {
            $validator =  Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        }

        return $validator;
    }


    /**
     * generate otp code
     *
     * @return \Illuminate\Http\Response
     */
    public function generateOtp($client_id , $client_name , $client_email)
    {
        $random_otp = rand(1000, 9999);
        // $otp_hash = hash('sha256', $random_otp);
        $otp_hash = $random_otp;
        $otp = new Otp();
        $user_otp = $otp->userOtp($client_id);
        if($user_otp){
            $get_otp = $otp->findByOtp($user_otp->data()['otp']);
            $otp->edit($get_otp->id() , [
                'client_id' => $client_id,
                'otp' => $otp_hash
            ]);
            $client = [
                'name' => $client_name,
                'otp' => $random_otp,
            ];
            \Mail::to($client_email)->send(new OTPMail($client));
        }else{
            $otp->create([
                'client_id' => $client_id,
                'otp' => $otp_hash
            ]);
            
            $client = [
                'name' => $client_name,
                'otp' => $random_otp,
            ];
            \Mail::to($client_email)->send(new OTPMail($client));
        }
    }

    /**
     * generate password for company
     * 
     * @return string 
     */
    public function generatePasswordForCompany()
    {
        $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@$%&_';
        $password = array();
        $char_length = strlen($char) - 1;

        for ($i = 0; $i < 16; $i++) {
            $rand = rand(0, $char_length);
            $password[] = $char[$rand];
        }
        return implode($password);
    }


    /**
     * check user and send otp
     * 
     * @param $id
     * @return \Illuminate\Http\Response
     * 
     * 1- phone
     * 2- otp -> password - confirm                                                      
     */
    public function checkUserAndSendOtp(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'phone' => 'required|string|max:11',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }

        $client = new client();
        $find_client = $client->findByPhone($request->phone);

        if($find_client){
            $this->generateOtp($find_client->id());
            return response()->json([
                'status' => true,
                'message' => 'otp sent',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'client not found'
            ]);
        }
    }

    /**
     * check otp and update password
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     *                                                      
     */
    public function updatePassword(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }

        $client = new client();
        $find_client = $client->findByPhone($request->phone);

        if($find_client){
            $client_data = $find_client->data();
            $client_data['password'] = $request->password;
            $client->edit($find_client->id(), $client_data);
            return response()->json([
                'status' => true,
                'message' => 'password updated',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'client not found'
            ]);
        }
    }


}
