<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            // This is a template, so we return an empty array
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'admission_number',
            'name',
            'email',
            'phone',
            'course',
            'course_code',
            'gender',
            'date_of_birth',
            'address',
            'city',
            'state',
            'country',
            'status',
            'password'
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Students Import Template';
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20); // admission_number
        $sheet->getColumnDimension('B')->setWidth(30); // name
        $sheet->getColumnDimension('C')->setWidth(30); // email
        $sheet->getColumnDimension('D')->setWidth(15); // phone
        $sheet->getColumnDimension('E')->setWidth(25); // course
        $sheet->getColumnDimension('F')->setWidth(15); // course_code
        $sheet->getColumnDimension('G')->setWidth(10); // gender
        $sheet->getColumnDimension('H')->setWidth(15); // date_of_birth
        $sheet->getColumnDimension('I')->setWidth(30); // address
        $sheet->getColumnDimension('J')->setWidth(15); // city
        $sheet->getColumnDimension('K')->setWidth(15); // state
        $sheet->getColumnDimension('L')->setWidth(15); // country
        $sheet->getColumnDimension('M')->setWidth(10); // status
        $sheet->getColumnDimension('N')->setWidth(20); // password

        // Add data validation for status and gender
        $statusValidation = $sheet->getCell('M1')->getDataValidation();
        $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowInputMessage(true);
        $statusValidation->setShowErrorMessage(true);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setErrorTitle('Input error');
        $statusValidation->setError('Value is not in list');
        $statusValidation->setPromptTitle('Pick from list');
        $statusValidation->setPrompt('Please pick a value from the drop-down list');
        $statusValidation->setFormula1('"Active,Inactive"');

        $genderValidation = $sheet->getCell('G1')->getDataValidation();
        $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $genderValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $genderValidation->setAllowBlank(true);
        $genderValidation->setShowInputMessage(true);
        $genderValidation->setShowErrorMessage(true);
        $genderValidation->setShowDropDown(true);
        $genderValidation->setErrorTitle('Input error');
        $genderValidation->setError('Value is not in list');
        $genderValidation->setPromptTitle('Pick from list');
        $genderValidation->setPrompt('Please pick a value from the drop-down list');
        $genderValidation->setFormula1('"Male,Female,Other"');

        // Add instructions
        $sheet->setCellValue('A3', 'Instructions:');
        $sheet->setCellValue('A4', '1. Required fields: admission_number, name, email, course or course_code');
        $sheet->setCellValue('A5', '2. For date fields, use format: YYYY-MM-DD');
        $sheet->setCellValue('A6', '3. Status must be either "Active" or "Inactive"');
        $sheet->setCellValue('A7', '4. If password is not provided, default "password" will be used');
        $sheet->setCellValue('A8', '5. For gender, use: Male, Female, or Other');

        // Style the headers
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ]
            ],
            'A3:A8' => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']]
            ]
        ];
    }
}
