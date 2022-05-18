<?php

namespace App\Http\Controllers;

use App\Models\otp;
use App\Models\client;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /**
     * get all otps
     * 
     * @return array of otp
     */
    public function index ()
    {
        $otp = new Otp();
        $otp = $otp->getAll();
        return $otp;
    }

    /**
     * check otp by user id
     * 
     * @param Request $request
     * @return array
     */
    public function check(Request $request)
    {
        $client_id = $request->client_id;
        // $otp_num  = hash('sha256' , $request->otp_num);
        $otp_num  =  $request->otp_num;
        
        $otp = new otp();
        $client = new client();
        $find_otp = $otp->userOtp($client_id);
        // return response()->json([
        //     'client_id' => $client_id,
        //     'find_otp' => $find_otp->data()['otp'],
        //     'otp' => $otp_num
        // ]);
        if($find_otp){
            if($find_otp->data()['otp'] == (int) $otp_num){
               
                $this->createWallet($request);
                $this->destroy($request);
                return response()->json([
                    'status' => true,
                    'message' => 'OTP is valid and created wallet'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'OTP is invalid'
                ]);
                $client->deleteClient($client_id);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'user not found'
            ]);
        }
    }

    public function checkOtpFromResetpassword(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'phone' => 'required|string|max:11',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }

        $client = new client();
        $otp = new otp();
        $find_client = $client->findByPhone($request->phone);
        if($find_client){
            $client_id = $find_client->id();
            $find_otp = $otp->userOtp($client_id);
            if($find_otp){
                if($find_otp->data()['otp'] == (int) $request->otp){
                    return response()->json([
                        'status' => true,
                        'message' => 'OTP is valid'
                    ]);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'OTP is invalid'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'user not found'
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'user not found'
            ]);
        }

    }

    /**
     * resend otp
     * 
     * @param Request $request
     * @return array
     */
    public function resend(Request $request)
    {
        $this->destroy($request);
        $this->generateOtp($request);
        return response()->json([
            'status' => true,
            'message' => 'OTP is resent successfully'
        ]);

    }

    /**
     * Destroy otp
     * 
     * @param Request $request
     * @return array
     */
    public function destroy(Request $request)
    {
        $client_id = $request->client_id;
        $otp = new otp();
        $otp->deleteOtp($client_id);
    }

    /**
     * generate otp code
     *
     * @param Request $request
     * 
     */
    public function generateOtp(Request $request)
    {
        $random_otp = rand(1000, 9999);
        // $otp_hash = hash('sha256', $random_otp);

        $otp = new otp();
        $user_otp = $otp->userOtp($request->client_id);
        if ($user_otp) {
            $get_otp = $otp->findByOtp($user_otp);
            $otp->edit($get_otp->id(), [
                'client_id' => $request->client_id,
                'otp' => $random_otp
            ]);
            // $client = [
            //     'name' => $client_name,
            //     'otp' => $random_otp,
            // ];
            // \Mail::to($client_email)->send(new OTP($client));
        } else {
            $otp->create([
                'client_id' => $request->client_id,
                'otp' => $random_otp
            ]);
            // $client = [
            //     'name' => $client_name,
            //     'otp' => $random_otp,
            // ];
            // \Mail::to($client_email)->send(new OTP($client));
        }
    }

    /**
     * create wallet
     *
     * @param Request $request
     * 
     */
    public function createWallet(Request $request)
    {
        $client = new client();
        $wallet = new wallet();
        $find_client = $wallet->findByUserId($request->client_id);
        if(!$find_client){
            $wallet->create([
                'client_id' => $request->client_id,
                'balance' => 0
            ]);
        }
    }

    /**
     * delete otps
     *
     * @param Request $request
     * 
     */
    public function deleteOtp()
    {
        $otp_obj = new otp();
        $all_otp =  $otp_obj->getAll();
        foreach($all_otp as $otp){
            $otp_obj->deleteThisOtp($otp->id());
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP is deleted successfully'
        ]);
    }

    /**
     * delete otp
     *
     * @param int $id
     * @return array
     * 
     */
    public function deleteOtpById($id)
    {
        $otp_obj = new otp();
        $otp_obj->deleteThisOtp($id);
        return response()->json([
            'status' => true,
            'message' => 'OTP is deleted successfully'
        ]);
    }
}
