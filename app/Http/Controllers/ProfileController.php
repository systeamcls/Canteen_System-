<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function show()
{
    /** @var User $user */
    $user = Auth::user();
    
    // Load relationships for proper pricing calculations
    $user->load(['orders.orderGroup', 'orders.orderItems', 'orderGroups']);
    
    return view('user-profile', compact('user'));
}

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        // Direct database update using the User model
        User::where('id', $user->id)->update($validated);

        return redirect()->route('user.profile.show')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Direct database update
        User::where('id', $user->id)->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile.show')->with('success', 'Password updated successfully!');
    }

    public function updateSettings(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'preferred_notification_channel' => ['required', 'in:email,sms,both'],
        ]);

        // Direct database update
        User::where('id', $user->id)->update($validated);

        return redirect()->route('user.profile.show')->with('success', 'Settings updated successfully!');
    }

    public function uploadProfilePicture(Request $request)
{
    $request->validate([
        'profile_picture' => [
            'required',
            'image',
            'mimes:jpeg,png,jpg,gif,webp',
            'max:2048', // 2MB max
        ]
    ]);

    /** @var User $user */
    $user = Auth::user();

    // Delete old profile picture if exists
    if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
    }

    // Store new profile picture
    $path = $request->file('profile_picture')->store('profile-pictures', 'public');
    
    // Update user record
    $user->update([
        'profile_photo_path' => $path
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Profile picture updated successfully!',
        'profile_picture_url' => Storage::url($path)
    ]);
}

public function showOrder($orderId)
{
    /** @var User $user */
    $user = Auth::user();
    
    // Find the order and ensure it belongs to the authenticated user
    $order = $user->orders()
        ->with(['orderItems.product', 'orderGroup.payments'])
        ->findOrFail($orderId);
    
    return view('order-detail', compact('order'));
}

public function cancelOrder(Request $request, $orderId)
{
    /** @var User $user */
    $user = Auth::user();
    
    $order = $user->orders()->findOrFail($orderId);
    
    // Check if order can be cancelled
    if ($order->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Order cannot be cancelled. Current status: ' . $order->status
        ]);
    }
    
    // Update order status
    $order->update(['status' => 'cancelled']);
    
    return response()->json([
        'success' => true,
        'message' => 'Order cancelled successfully',
        'status' => 'cancelled'
    ]);
}

public function checkOrderStatus($orderId)
{
    /** @var User $user */
    $user = Auth::user();
    
    $order = $user->orders()->findOrFail($orderId);
    
    return response()->json([
        'status' => $order->status,
        'updated_at' => $order->updated_at->format('M j, Y • g:i A')
    ]);
}

public function checkStatus()
{
    /** @var User $user */
    $user = Auth::user();
    return response()->json([
        'verified' => $user->hasVerifiedEmail()
    ]);
}

}