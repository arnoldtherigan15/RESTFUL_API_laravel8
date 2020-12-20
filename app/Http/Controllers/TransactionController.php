<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

// use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::orderBy('created_at','DESC')->get();
        $response = [
            'message' => 'Transaction Lists',
            'data' => $transactions
        ];
        return response()->json($response,Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,revenue'
        ]);
        if($validator->fails()) return response()->json($validator->errors(),Response::HTTP_BAD_REQUEST);
        try {

            $transaction = Transaction::create($request->all());
            
            $response = [
                'message' => 'Success add new transaction',
                'data'=> $transaction
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json($e->errorInfo,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            
            $respose = [
                'message' => 'Transaction Detail',
                'transaction' => $transaction
            ];
            return response($respose, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Transaction with id:'. $id . ' not found'
            ], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Transaction with id:'. $id . ' not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,revenue'
        ]);
        if($validator->fails()) return response()->json($validator->errors(),Response::HTTP_BAD_REQUEST);
        try {

            $transaction->update($request->all());
            
            $response = [
                'message' => 'Success edit transaction data',
                'data'=> $transaction
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json($e->errorInfo,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Transaction with id:'. $id . ' not found'
            ], 404);
        }
        
        try {

            $transaction->delete();
            
            $response = [
                'message' => 'Success delete transaction data',
                'data'=> $transaction
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json($e->errorInfo,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
