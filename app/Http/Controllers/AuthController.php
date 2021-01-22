<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; // this verry important to sho current User

use Validator;
use App\Models\User;

class AuthController extends Controller
{

    
    public function index(Request $request)  // this function show data in Table for template
    {
      $user = Auth::user();   // for this play i must introduce currentUser to framework 
        return User::where('id', $user->id)->paginate();  // framework find all things of user in database and result show that in template 
    }	

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:3|confirmed',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        if($request->photo){  
            //  this code make image or convert file in controller and for play that i install require image in First
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/public/').$name); // this code add image that we upload in folder (public/img/public)
            $request->merge(['photo' => $name]);
           
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->photo = $request->photo;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['status' => 'success'], 200);
    }
	
	
	
	
	  /**
     * Login user and return a token
     */
   public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json(['status' => 'success', 'token' => $token], 200)->header('Authorization', $token);
        }
        return response()->json(['error' => 'login_error'], 401);
    }



    public function update(Request $request, $id)
    {
        $upload = user::find($id);

        $this->validate($request,[
            
            'photo' => 'required'
        ]);
  
        $currentPhoto = $upload->photo;  // $currentPhoto is that image there are in table and file of framework
  
        if($request->photo != $currentPhoto){
             //  this code make image or convert file in controller and for play that i install require image in First
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/public/').$name);  // this code add image that we upload in folder (public/img/public)
            $request->merge(['photo' => $name]);
  
            $userPhoto = public_path('img/public/').$currentPhoto;
  
            if(file_exists($userPhoto)){   // if file or image exist in publucFolder move and add the new image 
  
                @unlink($userPhoto);
                
            }
           
        }
  
        $upload->update($request->all());
  
        return ['message' => 'Success'];
    } 

    /**
     * Logout User
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    /**
     * Return auth guard
     */
    private function guard()
    {
        return Auth::guard();
    }
}
