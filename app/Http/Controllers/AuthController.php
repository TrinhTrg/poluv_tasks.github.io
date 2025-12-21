<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    // 1. Hiển thị form đăng ký
    public function showRegister()
    {
        return view('auth.register');
    }

    // 2. Xử lý đăng ký
    public function register(Request $request)
    {
        // Validate dữ liệu (Task 3: Server Side Validations)
        $validationRules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // Cần field password_confirmation ở view
        ];
        
        $request->validate($validationRules);

        // Tạo user mới (Task 3: Hash password)
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Đăng nhập luôn sau khi đăng ký
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    // 3. Hiển thị form đăng nhập
    public function showLogin()
    {
        return view('auth.login');
    }

    // 4. Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Thử đăng nhập (Auth Facade)
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate(); // Bảo mật: Chống tấn công Session Fixation

            return redirect()->intended(route('home')); // Chuyển hướng đến trang định vào hoặc home
        }

        // Nếu sai
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    // 5. Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->reflash();
        $request->session()->regenerateToken();

        // Sau khi đăng xuất, chuyển về trang landing page (/)
        return redirect()->route('welcome');
    }

    // 6. Hiển thị form quên mật khẩu
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // 7. Gửi mã xác nhận qua email
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
        }

        // Tạo mã xác nhận 6 chữ số
        $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu token vào password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Gửi email trực tiếp (không qua Job) để hiển thị trong Telescope
        $user->notify(new PasswordResetNotification($token));

        return redirect()->route('password.verify')->with('email', $request->email)->with('success', 'Mã xác nhận đã được gửi đến email của bạn!');
    }
    
    // 8. Hiển thị form nhập mã xác nhận
    public function showVerifyCode()
    {
        return view('auth.verify-reset-code');
    }

    // 9. Xác thực mã và hiển thị form đổi mật khẩu
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetToken) {
            return back()->withErrors(['code' => 'Mã xác nhận không hợp lệ hoặc đã hết hạn.'])->withInput();
        }

        // Kiểm tra mã xác nhận (so sánh hash)
        if (!Hash::check($request->code, $resetToken->token)) {
            return back()->withErrors(['code' => 'Mã xác nhận không đúng.'])->withInput();
        }

        // Kiểm tra mã còn hạn (60 phút)
        $createdAt = Carbon::parse($resetToken->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['code' => 'Mã xác nhận đã hết hạn. Vui lòng yêu cầu mã mới.'])->withInput();
        }

        // Lưu email vào session để dùng ở bước reset password
        session(['reset_password_email' => $request->email]);

        return redirect()->route('password.reset')->with('success', 'Mã xác nhận hợp lệ! Vui lòng nhập mật khẩu mới.');
    }

    // 10. Hiển thị form đổi mật khẩu
    public function showResetPassword()
    {
        if (!session('reset_password_email')) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.']);
        }

        return view('auth.reset-password');
    }

    // 11. Đổi mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = session('reset_password_email') ?? $request->email;

        if ($email !== $request->email) {
            return back()->withErrors(['email' => 'Email không khớp với phiên làm việc.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Người dùng không tồn tại.']);
        }

        // Cập nhật mật khẩu
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa token đã sử dụng
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Xóa session
        $request->session()->forget('reset_password_email');

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đổi thành công! Vui lòng đăng nhập với mật khẩu mới.');
    }
}