<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\AuthenticateRequest;
use Modules\UserManagement\Http\Requests\UserCreateRequest;
use Modules\UserManagement\Http\Requests\UserRegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(AuthenticateRequest $request)
    {

        try{
            $page = $request->input('page');
            $user = JWTAuth::authenticate($request->input('token'));
            $users = User::paginate($request->input('per_page'));
            if(!$users){
                return response()->json([
                    'status'  =>  false,
                    'message' => 'Failed to fetch the list'
                ],Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'status'  =>  true,
                'message' => 'Users List Fetched Successfully',
                'data'    => $users
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch the users list'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(UserCreateRequest $request)
    {
        try{
            JWTAuth::authenticate($request->input('token'));
            $user = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]);
            if($user){
                return response()->json([
                    'status'  => true,
                    'message' => 'User Registered Successfully',
                    'data'    => $user
                ],Response::HTTP_OK);
            }else{
                return response()->json([
                    'status'  => false,
                    'message' => 'Failed To Register User',
                    'data'    => array()
                ],Response::HTTP_BAD_REQUEST);
            }
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Access'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(AuthenticateRequest $request,$id)
    {

        try{
            JWTAuth::authenticate($request->input('token'));
            $user = User::findOrFail($id);
            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to fetch the details'
                ],Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Details Fetched Successfully',
                'data'    => $user
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Access',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UserRegisterRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UserCreateRequest $request)
    {
        try{
            JWTAuth::authenticate($request->input('token'));
            $user = User::firstOrCreate(['id'=> $request->input('id')],[
                'name' => $request->input('name'),
                'email'=> $request->input('email')
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'User Updated Successfully'
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Access',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(AuthenticateRequest $request,$id)
    {

        try{
            JWTAuth::authenticate($request->input('token'));
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'status'  => true,
                'message' => 'User Deleted Successfully'
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Access',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}