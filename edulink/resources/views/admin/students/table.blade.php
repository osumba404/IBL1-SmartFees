<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Enrolled</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td>{{ $student->student_id }}</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->email }}</td>
                <td>
                    <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </td>
                <td>{{ $student->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No students found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($students->hasPages())
<div class="d-flex justify-content-center">
    {{ $students->links() }}
</div>
@endif