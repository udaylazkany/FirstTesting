<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $employee = Employee::where('email', $request->email)->first();

            if (! $employee || ! Hash::check($request->password, $employee->password)) {
                return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
            }

            $token = $employee->createToken('employee-token')->plainTextToken;

            return response()->json([
                'message' => 'Login Succefuly',
                'token' => $token,
                'employee' => $employee,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء تسجيل الدخول'], 410);
        }
    }
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|min:6'
        ]);

        try {
            $employee = Employee::create([
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $employee->createToken('employee-token')->plainTextToken;

            return response()->json([
                'message' => 'تم التسجيل بنجاح',
                'employee' => $employee,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء التسجيل'], 410);
        }
    }


}
