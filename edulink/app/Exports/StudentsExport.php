<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Student::with('course')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Admission Number',
            'Full Name',
            'Email',
            'Phone',
            'Course',
            'Course Code',
            'Gender',
            'Date of Birth',
            'Status',
            'Address',
            'City',
            'State',
            'Country',
            'Created At',
            'Last Updated'
        ];
    }

    /**
     * @param mixed $student
     *
     * @return array
     */
    public function map($student): array
    {
        return [
            $student->admission_number,
            $student->name,
            $student->email,
            $student->phone,
            $student->course->name ?? 'N/A',
            $student->course->code ?? 'N/A',
            $student->gender,
            $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '',
            $student->is_active ? 'Active' : 'Inactive',
            $student->address,
            $student->city,
            $student->state,
            $student->country,
            $student->created_at->format('Y-m-d H:i:s'),
            $student->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9EAD3']
                ]
            ],
        ];
    }
}
