<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function currentGame(Request $request) {
        $coin = $request->coin;
        $game = Game::where('coin', $coin)->orderBy('created_at', 'desc')->first();
        return $game;
    }
}
