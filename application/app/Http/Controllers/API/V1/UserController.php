<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\Authentication\AuthenticateResponse;
use App\Mail\ForgotPassword;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Validator;
use App\Http\Responses\User\CommonResponse;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Responses\Contacts\UpdateResponse;
use Illuminate\Validation\Rule;
use App\Repositories\AttachmentRepository;
use App\Models\Attachment;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    protected $userrepo;
    protected $clientrepo;

    public function __construct(
        UserRepository $userrepo,
        ClientRepository $clientrepo
    ) {

        //parent
        parent::__construct();
        $this->userrepo = $userrepo;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request()->only('email', 'password');
        $remember = (request('remember_me') == 'on') ? true : false;
        //check credentials
        if (Auth::attempt($credentials, $remember)) {
            $user = User::where('email', request('email'))->first();
            if (!$user) {
                return response()->json(['error' => 'account_not_found'], 409);
            } else {
                if ($user['status'] != 'active') {
                    auth()->logout();
                    return response()->json(['error' => 'account_has_been_suspended'], 409);
                }

                $token = Str::random(60);
                return response()->json([
                    'message' => 'user_logged_in_successfully',
                    'token' => $token,
                    'user' => $user
                ], 201);
            }
        } else {
            return response()->json(['error' => 'invalid_login_details'], 409);
        }
    }



    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'user_log_out_successfully'], 201);
    }

    /**
     * Forgot password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword()
    {
        //validation
        if (!$user = User::Where('email', request('email'))->first()) {
            return response()->json(['error' => 'account_not_found'], 409);
        }

        $code = Str::random(50);

        //update user - set expiry to 3 Hrs
        $user->forgot_password_token = $code;
        $user->forgot_password_token_expiry = Carbon::now()->addHours(3);
        $user->save();

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        if ($user->type == 'client' && config('system.settings_clients_disable_email_delivery') == 'enabled') {
            return response()->json(['error' => 'clients_disabled_login_error'], 409);
        } else {
            Mail::to($user->email)->send(new ForgotPassword($user));
        }

        return response()->json(['forgot_password_token' => $user->forgot_password_token, 'message' => 'password_reset_email_sent'], 201);
    }

    /**
     * Reset password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword()
    {
        //1 hour expiry
        $expiry = Carbon::now()->subHours(1);

        $messages = [];

        //validate code
        if (User::Where('forgot_password_token', request('token'))
            ->where('forgot_password_token_expiry', '>=', $expiry)
            ->doesntExist()
        ) {
            return response()->json(['error' => 'token_expired_or_invalid'], 409);
        }

        //validate password match
        $validator = Validator::make(request()->all(), [
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return response()->json(['error' => $messages], 409);
        }

        $user = User::Where('forgot_password_token', request('token'))->first();
        $user->password = Hash::make(request('password'));
        $user->forgot_password_token = '';
        $user->save();

        return response()->json(['message' => 'password_reset_success'], 201);
    }
    public function updatePassword($id)
    {
        if (User::Where('id', $id)->first()) {
            $user = User::Where('id', $id)->first();

            $validator = Validator::make(request()->all(), [
                'old_password' => 'required|min:6',
                'password' => 'required|confirmed|min:6',
            ]);

            //errors
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['message' => $errors], 409);
            }

            if (!Hash::check(request('old_password'), $user->password)) {
                return response()->json(['message' => 'old password dose not match!'], 409);
            }
            //update database
            $user->password = Hash::make(request('password'));
            $user->force_password_change = 'no';
            $user->save();
            return response()->json(['message' => 'password_change_success'], 200);
        } else {
            return response()->json(['message' => 'user_dose_not_exist'], 409);
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile($id)
    {
        if (User::Where('id', $id)->first()) {
            $user = User::Where('id', $id)->first();
            return response()->json([
                'message' => 'user_profile_get_successfully',
                'user' => $user
            ], 201);
        } else {
            return response()->json(['message' => 'user_dose_not_exist'], 409);
        }
    }
    /**
     * Update the specified contact in storage.
     * @param int $id contact id
     * @return \Illuminate\Http\Response
     */
    public function updateUserInfo($id)
    {
        //validate the form
        $validator = Validator::make(request()->all(), [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id, 'id'),
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }

        //get the user
        if (User::Where('id', $id)->first()) {
            $user = User::Where('id', $id)->first();
            $user->first_name = request('first_name');
            $user->last_name = request('last_name');
            $user->email = request('email');
            if (request('position')) {
                $user->position = request('position');
            }
            if (request('phone')) {
                $user->phone = request('phone');
            }
            if (request('social_facebook')) {
                $user->social_facebook = request('social_facebook');
            }
            if (request('social_twitter')) {
                $user->social_twitter = request('social_twitter');
            }
            if (request('social_linkedin')) {
                $user->social_linkedin = request('social_linkedin');
            }
            if (request('social_github')) {
                $user->social_github = request('social_github');
            }
            if (request('social_dribble')) {
                $user->social_github = request('social_dribble');
            }
            $user->save();
            return response()->json(['message' => 'user_profile_updated_successfully', 'user' => $user], 200);
        } else {
            return response()->json(['message' => 'user_dose_not_exist'], 409);
        }
    }
    /**
     * Update the specified user in storage.
     * @param object AttachmentRepository instance of the repository
     * @param int $id user id
     * @return \Illuminate\Http\Response
     */
    public function updatePicture(Request $request)
    {

        //validate input
        $validator = Validator::make(request()->all(), [

            'avatar_filename' => [
                'required',
            ],
            'userid' => [
                'required',
            ],
        ]);
        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
     
        $directory =Str::random(50);
      
        //get the user
        if (User::Where('id', request('userid'))->first()) {
           
        //check if file exist 
          if($request->hasFile('avatar_filename')){
            $file = $request->file('avatar_filename');
            $filename = $file->getClientOriginalName();
            $new_dir_path = BASE_DIR . "/storage/avatars/" . $directory;
            $file->move($new_dir_path, $filename);  
           
          }
      
            //save data in table
            $user = User::Where('id', request('userid'))->first();
            //update users avatar
            $user->avatar_directory =$directory;
            $user->avatar_filename =$filename;

            $user->save();
            return response()->json(['message' => 'user_profile_picture_successfully', 'user' => $user], 200);
        }else {
            return response()->json(['message' => 'user_dose_not_exist'], 409);
        }
    }
    public function getPicture($id)
    {
        //get the user
        if (User::Where('id', $id)->first()) {
            $user=User::Where('id', $id)->first();
            $image=array( 'img_source' => url('/storage/avatars/' . $user->avatar_directory. '/' . $user->avatar_filename));
            return response()->json(['message' => 'success', 'user' => $image], 200);
        }else {
            return response()->json(['message' => 'user_dose_not_exist'], 409);
        }
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }



    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
