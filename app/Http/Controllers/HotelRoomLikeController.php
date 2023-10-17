<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelRoomLike;

class HotelRoomLikeController extends Controller
{
    public function like($hotelRoomId)
{
    $user = auth()->user();

    // Check if the user already liked the hotel room
    $existingLike = HotelRoomLike::where('user_id', $user->id)
        ->where('hotel_room_id', $hotelRoomId)
        ->first();

    if ($existingLike) {
        $existingLike->delete(); // Delete the like record
    } else {
        $like = new HotelRoomLike([
            'user_id' => $user->id,
            'hotel_room_id' => $hotelRoomId,
            'like_type' => 'like',
        ]);

        $like->save();
    }

    return response()->json(['message' => 'Action completed successfully']);
}

public function dislike($hotelRoomId)
{
    $user = auth()->user();

    // Check if the user already disliked the hotel room
    $existingDislike = HotelRoomLike::where('user_id', $user->id)
        ->where('hotel_room_id', $hotelRoomId)
        ->first();

    if ($existingDislike) {
        $existingDislike->delete(); // Delete the dislike record
    } else {
        $dislike = new HotelRoomLike([
            'user_id' => $user->id,
            'hotel_room_id' => $hotelRoomId,
            'like_type' => 'dislike',
        ]);

        $dislike->save();
    }

    return response()->json(['message' => 'Action completed successfully']);
}

}
