<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TermConditionService;

class TermConditionController extends Controller
{
    public function __construct(
        private TermConditionService $termConditionService,
    ) {}

    public function index() {
        $termCondition = $this->termConditionService->getTermCondition();

        return view('term_condition', [
            'term_condition' => $termCondition
        ]);
    }
}
