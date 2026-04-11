<?php

namespace App\Services;

use App\Models\Province;
use Illuminate\Support\Facades\DB;

class ProvinceStatsService
{
    private const SQL_MEMBERS_DISTINCT = 'users.province_id, COUNT(DISTINCT assessment_attempts.user_id) as cnt';

    private const SQL_PROVINCE_TOTAL_CNT = 'users.province_id, COUNT(*) as cnt';

    /**
     * Get province stats (members, passed, attempts, enrolments, certificates, pass_rate) using batched queries.
     *
     * @return \Illuminate\Support\Collection<int, array{province: Province, members: int, passed: int, attempts: int, enrolments: int, certificates: int, pass_rate: float|null}>
     */
    public function getStatsForAllProvinces(): \Illuminate\Support\Collection
    {
        $provinces = Province::orderBy('sort_order')->get()->keyBy('id');

        // Members = distinct users from province who have passed at least one assessment (Zanu PF membership)
        $membersByProvince = DB::table('assessment_attempts')
            ->join('assessments', 'assessment_attempts.assessment_id', '=', 'assessments.id')
            ->join('users', 'assessment_attempts.user_id', '=', 'users.id')
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->whereRaw('assessment_attempts.score >= assessments.pass_mark')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_MEMBERS_DISTINCT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        $passedByProvince = DB::table('assessment_attempts')
            ->join('assessments', 'assessment_attempts.assessment_id', '=', 'assessments.id')
            ->join('users', 'assessment_attempts.user_id', '=', 'users.id')
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->whereRaw('assessment_attempts.score >= assessments.pass_mark')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_PROVINCE_TOTAL_CNT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        $attemptsByProvince = DB::table('assessment_attempts')
            ->join('users', 'assessment_attempts.user_id', '=', 'users.id')
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_PROVINCE_TOTAL_CNT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        $enrolmentsByProvince = DB::table('enrolments')
            ->join('users', 'enrolments.user_id', '=', 'users.id')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_PROVINCE_TOTAL_CNT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        $certificatesByProvince = DB::table('certificates')
            ->join('users', 'certificates.user_id', '=', 'users.id')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_PROVINCE_TOTAL_CNT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        return $provinces->map(function (Province $province) use (
            $membersByProvince,
            $passedByProvince,
            $attemptsByProvince,
            $enrolmentsByProvince,
            $certificatesByProvince
        ) {
            $members = (int) ($membersByProvince[$province->id] ?? 0);
            $passed = (int) ($passedByProvince[$province->id] ?? 0);
            $attempts = (int) ($attemptsByProvince[$province->id] ?? 0);
            $enrolments = (int) ($enrolmentsByProvince[$province->id] ?? 0);
            $certificates = (int) ($certificatesByProvince[$province->id] ?? 0);
            $passRate = $attempts > 0 ? round(($passed / $attempts) * 100, 1) : null;

            return [
                'province' => $province,
                'members' => $members,
                'passed' => $passed,
                'attempts' => $attempts,
                'enrolments' => $enrolments,
                'certificates' => $certificates,
                'pass_rate' => $passRate,
            ];
        })->values();
    }

    /**
     * Get province leaderboard (ranked by passed) and rank for a specific province.
     *
     * @return array{rank: int|null, passed: int, total_with_activity: int}
     */
    public function getProvinceRankContext(int $provinceId): array
    {
        $passedByProvince = DB::table('assessment_attempts')
            ->join('assessments', 'assessment_attempts.assessment_id', '=', 'assessments.id')
            ->join('users', 'assessment_attempts.user_id', '=', 'users.id')
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->whereRaw('assessment_attempts.score >= assessments.pass_mark')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_PROVINCE_TOTAL_CNT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        $attemptsByProvince = DB::table('assessment_attempts')
            ->join('users', 'assessment_attempts.user_id', '=', 'users.id')
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->whereNotNull('users.province_id')
            ->selectRaw(self::SQL_PROVINCE_TOTAL_CNT)
            ->groupBy('users.province_id')
            ->pluck('cnt', 'province_id');

        $leaderboard = $passedByProvince->filter(fn ($passed, $pid) => ($attemptsByProvince[$pid] ?? 0) > 0)
            ->sortDesc()
            ->keys()
            ->values();

        $provincePassed = (int) ($passedByProvince[$provinceId] ?? 0);
        $totalWithActivity = $leaderboard->count();
        $idx = $leaderboard->search($provinceId);
        $rank = $idx !== false ? $idx + 1 : null;

        return [
            'rank' => $rank,
            'passed' => $provincePassed,
            'total_with_activity' => $totalWithActivity,
        ];
    }
}
