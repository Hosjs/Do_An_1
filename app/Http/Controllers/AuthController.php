<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\RoleUser;

class AuthController extends Controller
{
   public function login(Request $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'roles' => $user->roles->pluck('name')
        ]);
    }
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('roles');

        return response()->json($user);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:Student,Teacher',
        ]);
        $role_id = Role::where('name', $data['role'])->first()->id;
        $user = \App\Models\User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),    
        ]);

        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => $role_id,
        ]);

        return response()->json(['message' => 'Đăng ký thành công'], 201);
    }
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
