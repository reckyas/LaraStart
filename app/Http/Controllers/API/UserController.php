<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'bio' => $request->bio,
            'photo' => $request->photo,
        ]);

        return response()->json($user,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,'.$user->id.'|string|max:191',
            'password' => 'sometimes|min:8|string|max:191',
            'type' => 'required'
        ]);
        $user->update($request->all());
        return ['message' => 'User upadated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find User
        $user = User::findOrFail($id);
        // Deleting data
        $user->delete();
        // Response the message
        return ['message' => 'User deleted'];
    }
}
