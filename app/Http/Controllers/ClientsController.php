<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\client;
use Illuminate\Support\Facades\Validator;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $client = new client();
        $clients = $client->getAll();
        return response()->json($clients);
        // return view('welcome');
    }

    /**
     * Store a newly created resource in storage.
     * 3del ya heshaaaaaam b3d el data
     * 7eta security
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['password'];
        
        $validator =  Validator::make($request->all(),[
            'name' => 'required|string|max:20',
            'phone' => 'required|numeric|max:11',
            'password' => 'required|string|min:8',
            'salt' => 'required|string',
            'finger_print' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'validation faild',
                'data' => $validator->errors()
            ], 400);
        }
        else{
            $client = new client();
            $createclient = $client->create($data);
        }
        if ($createclient)
            return response()->json(['success' => true, 'data' => $createclient], 200);
        else
            return response()->json(['error' => 'Something went wrong'], 500);
    }

    /**
     * Display the client resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = new client();
        $client = $client->find($id);
        if ($client)
            return $client;
        else
            return response()->json(['error' => 'client not found']);
    }

    /**
     * Update the client resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $request->validate([
            'name' => 'required|string|max:20',
            'phone' => 'required|string',
            'password' => 'required|string|min:8',
            'salt' => 'required|string',
            'finger_print' => 'required|string'

        ]);
        $client = new client();
        $updateclient = $client->edit($id, $data);
        if ($updateclient)
            return $updateclient;
        else
            return response()->json(['error' => 'Something went wrong'], 500);
    }

    /**
     * get all payments of client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payments(Request $request)
    {
        ini_set('max_execution_time', '300');
        $client = new client();
        $payments = $client->payments($request->id);
        if ($payments)
            return response()->json([
                'status' => true,
                'data' => [
                    'payments' => $payments
                ]
            ]);
        else
            return response()->json(['error' => 'client not have payments']);
    }

    public function wallet(Request $request)
    {
        $client = new client();
        $wallet = $client->wallet($request->client_id);
        if ($wallet)
            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $wallet->id(),
                    'balance' => $wallet->data()['balance']
                ]
            ]);
        else
            return response()->json([
                'status' => false,
                'message' => 'client not have wallet'
            ]);
    }

    /**
     * Remove all users from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = new client();
        $user->deleteAll();
        return response()->json([
            'status' => true,
            'message' => 'All users deleted successfully'
        ]);
    }

    /**
     * Remove the client resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyClient($id)
    {
        $client = new client();
        $client->deleteClient($id);
        return response()->json([
            'status' => true,
            'message' => 'client deleted successfully'
        ]);
    }
}
