<?php

namespace App\Imports;

use App\Models\Applicant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\Institution;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;

class ApplicantsImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas, HasReferencesToOtherSheets
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $validated = [];
        foreach ($collection as $row) {
            $v = Validator::make($row->toArray(), [
                'roll_no' => 'required|integer',
                'allotment_id' => 'nullable|integer',
                'status' => 'nullable|string|in:Selected,Not Selected',
            ]);
            if ($v->fails()) {
                continue;
            }
            $row_data = $v->validated();
            $row_data['id'] = ((int) $row_data['roll_no']) - 1000;
            unset($row_data['roll_no']);
            if (settings('selectable_max') > 0) {
                $row_data['allotment_id'] = (!empty($row_data['allotment_id']) && $row_data['allotment_id'] > 0) ? $row_data['allotment_id'] : null;
                $row_data['status'] = $row_data['allotment_id'] ? 1 : 0;
            } else {
                $row_data['status'] = (!empty($row_data['status']) && $row_data['status'] == 'Selected') ? 1 : 0;
                $row_data['allotment_id'] = null;
            }
            $validated[] = $row_data;
        }
        if (!empty($validated)) {
            $ids = array_column($validated, 'id');
            $applicants = Applicant::whereIn('id', $ids)->get();

            $allotment_ids = array_column($validated, 'allotment_id');
            $allotment_ids = array_filter($allotment_ids);
            $allotment_ids = array_unique($allotment_ids);
            $allotment_ids = array_values($allotment_ids);
            if (!count($allotment_ids)) {
                $institutions = [];
            } else
                $institutions = Institution::whereIn('id', $allotment_ids)->get();
            foreach ($validated as $each) {
                $applicant = $applicants->where('id', $each['id'])->first();
                if (!count($institutions) || !isset($each['allotment_id']) || !$each['allotment_id']) {
                    $each['allotment_id'] = null;
                } else {
                    $each['allotment_id'] = $institutions->where('id', $each['allotment_id'])->first()?->id;
                }
                if ($applicant) {
                    $applicant->update($each);
                }
            }
        }
    }
}
