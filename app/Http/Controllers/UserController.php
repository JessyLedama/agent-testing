<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Make the authenticated user an artist.
     */
    public function becomeArtist(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isArtist()) {
            return response()->json([
                'message' => 'User is already an artist'
            ], 400);
        }
        
        $user->becomeArtist();
        
        return response()->json([
            'message' => 'You are now an artist!',
            'user' => $user
        ]);
    }
}
