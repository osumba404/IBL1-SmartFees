<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('course')
            ->latest()
            ->paginate(25);
            
        $courses = Course::active()->get();
        
        return view('admin.students.index', compact('students', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::active()->get();
        return view('admin.students.create', compact('courses'));
    }

    /**
     * Import students from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return back()->with('success', 'Students imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    /**
     * Export students to Excel.
     */
    public function export()
    {
        return Excel::download(new StudentsExport, 'students_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport, 'students_import_template.xlsx');
    }

    // ... other CRUD methods (show, store, edit, update, destroy) ...

    /**
     * Get students for DataTable AJAX.
     */
    public function getStudents()
    {
        return DataTables::of(Student::with('course'))
            ->addColumn('action', function($student) {
                return view('admin.students.partials.actions', compact('student'))->render();
            })
            ->addColumn('status_badge', function($student) {
                return $student->is_active 
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }
}
