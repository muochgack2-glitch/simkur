<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HolidayApiController extends Controller
{
    /**
     * Get all holiday activities for the active academic year.
     *
     * GET /api/holidays
     *
     * Returns activities where activity_type.is_holiday = true.
     * Used by Absensi system to sync holiday data.
     */
    public function index(Request $request): JsonResponse
    {
        $academicYear = $request->input('academic_year_id')
            ? AcademicYear::find($request->input('academic_year_id'))
            : AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun ajaran tidak ditemukan',
                'data' => [],
            ]);
        }

        $holidays = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->whereHas('activityType', function ($q) {
                $q->where('is_holiday', true);
            })
            ->orderBy('start_date')
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'start_date' => $activity->start_date->format('Y-m-d'),
                    'end_date' => $activity->end_date->format('Y-m-d'),
                    'type' => $activity->activityType->name ?? '-',
                    'description' => $activity->description,
                ];
            });

        return response()->json([
            'success' => true,
            'academic_year' => $academicYear->year,
            'total' => $holidays->count(),
            'data' => $holidays->values(),
        ]);
    }
}
