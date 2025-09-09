<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class StudentAuthController extends Controller
{
    /**
     * Display the student registration view.
     */
    public function create(): View
    {
        $courses = Course::where('is_active', true)->get();
        return view('auth.student.register', compact('courses'));
    }

    /**
     * Handle an incoming student registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Student::class],
            'phone' => ['required', 'string', 'max:20', 'unique:'.Student::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:100'],
            'id_number' => ['required', 'string', 'max:50', 'unique:'.Student::class],
            'course_id' => ['required', 'exists:courses,id'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
        ]);

        $student = Student::create([
            'student_id' => Student::generateStudentId(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'id_number' => $request->id_number,
            'course_id' => $request->course_id,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'status' => 'pending_verification',
            'email_verified_at' => null,
        ]);

        event(new Registered($student));

        Auth::guard('student')->login($student);

        return redirect()->route('student.dashboard');
    }

    /**
     * Display the student login view.
     */
    public function showLoginForm(): View
    {
        return view('auth.student.login');
    }

    /**
     * Handle an incoming student authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('student')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $student = Auth::guard('student')->user();
            
            // Check if student account is active
            if ($student->status === 'suspended') {
                Auth::guard('student')->logout();
                return back()->withErrors([
                    'email' => 'Your account has been suspended. Please contact administration.',
                ]);
            }

            if ($student->status === 'inactive') {
                Auth::guard('student')->logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive. Please contact administration.',
                ]);
            }

            // Update last login
            $student->update(['last_login_at' => now()]);

            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated student session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }

    /**
     * Display the student dashboard.
     */
    public function dashboard(): View
    {
        $student = Auth::guard('student')->user();
        
        // Get current enrollment
        $currentEnrollment = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure'])
            ->where('status', 'active')
            ->first();

        // Get recent payments
        $recentPayments = $student->payments()
            ->with(['enrollment.course', 'enrollment.semester'])
            ->latest()
            ->take(5)
            ->get();

        // Get pending payments
        $pendingPayments = $student->payments()
            ->where('status', 'pending')
            ->with(['enrollment.course', 'enrollment.semester'])
            ->get();

        // Calculate financial summary
        $financialSummary = $student->getFinancialSummary();

        // Get notifications
        $notifications = $student->notifications()
            ->where('read_at', null)
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'currentEnrollment',
            'recentPayments',
            'pendingPayments',
            'financialSummary',
            'notifications'
        ));
    }

    /**
     * Display student profile
     */
    public function profile(): View
    {
        $student = Auth::guard('student')->user();
        return view('student.profile', compact('student'));
    }

    /**
     * Update student profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:students,phone,' . $student->id],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $student->update($request->only([
            'first_name',
            'last_name',
            'phone',
            'emergency_contact_name',
            'emergency_contact_phone',
            'address'
        ]));

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Change student password
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password:student'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $student = Auth::guard('student')->user();
        $student->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('student.profile')->with('success', 'Password changed successfully.');
    }
}
