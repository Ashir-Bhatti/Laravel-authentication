<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PositionBoard;
use Illuminate\Http\Request;

class PositionBoardController extends Controller
{
    function index()
    {
        return json_response(
            200,
            "Position on Board fetched successfully.",
            PositionBoard::all()
        );
    }
}
