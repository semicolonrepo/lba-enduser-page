<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TermConditionService
{
    public function __construct() {
        //
    }

    public function getTermCondition() {
        return DB::table('settings')
        ->where('identifier', 'term_condition')
        ->select('title', 'content')
        ->first();
    }
}
