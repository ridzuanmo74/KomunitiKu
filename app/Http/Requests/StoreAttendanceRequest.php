<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use App\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $activity = Activity::find((int) $this->integer('activity_id'));

        if (! $activity) {
            return false;
        }

        return Gate::allows('create', [Attendance::class, (int) $activity->association_id]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'status' => ['sometimes', 'in:present,absent'],
        ];
    }
}
