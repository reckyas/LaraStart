<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Gate;
class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            return User::latest()->paginate(3);
        }
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

    public function profile() {
        return auth('api')->user(); 
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
            'password' => 'sometimes|required|min:8|string|max:191',
            'type' => 'required'
        ]);
        if(!empty($request->password)){
            $request->merge(["password" => Hash::make($request['password'])]);
        }
        $user->update($request->all());
        return ['message' => 'User upadated'];
    }
    public function updateProfile(Request $request) {
        $user = auth('api')->user();
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,'.$user->id.'|string|max:191',
            'password' => 'sometimes|min:8|string|max:191',
            // 'type' => 'required'
        ]);
        $curentPhoto = $user->photo;
        if($request->photo != $curentPhoto) {
            $name = 'img_' . time() . '.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(["photo" => $name]);

            $oldPhoto = public_path('img/profile/').$curentPhoto;

            if(file_exists($oldPhoto)) {
                @unlink($oldPhoto);
            }
        }
        if(!empty($request->password)){
            $request->merge(["password" => Hash::make($request['password'])]);
        }

        $user->update($request->all());
        // return ['message' => 'User upadated'];
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        // Find User
        $user = User::findOrFail($id);
        // Deleting data
        $user->delete();
        // Response the message
        return ['message' => 'User deleted'];
    }

    public function search(Request $request) {
        if($search = $request->get('q')) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%")
                      ->orWhere('type', 'LIKE', "%$search%")
                      ->orWhere('created_at', 'LIKE', "%$search%");
            })->paginate(3);
        }

        return $users;
    }
}
