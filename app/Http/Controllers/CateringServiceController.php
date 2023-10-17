<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CateringService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\EloquentModelNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CateringServiceController extends Controller
{
    public function index()
    {
        try {
            $cateringServices = CateringService::all();
            return response()->json($cateringServices);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'num_member' => 'required|integer',
                'num_girl' => 'required|integer',
                'num_boy' => 'required|integer',
                'address' => 'required|string|max:255',
                'ifu' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'secret_key' => 'required|string|exists:users,secret_key',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }

            $cateringServiceData = $request->except(['secret_key', 'image']);
            $cateringServiceData['catering_service_code'] = 'CATER_' . uniqid() . '_AFRILINK';
            $cateringServiceData['user_id'] = $user->id;
            $cateringServiceData['manager_firstname'] = $user->firstname;
            $cateringServiceData['manager_lastname'] = $user->lastname;
            $cateringServiceData['manager_phone'] = $user->phone;
            $cateringServiceData['manager_email'] = $user->email;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('catering_images', 'public');
                $cateringServiceData['image'] = $imagePath;
            }

            $cateringService = CateringService::create($cateringServiceData);

            return response()->json(['message' => 'Catering service created successfully', 'data' => $cateringService], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $cateringService = CateringService::findOrFail($id);
            return response()->json($cateringService);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Catering service not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'num_member' => 'required|integer',
                'num_girl' => 'required|integer',
                'num_boy' => 'required|integer',
                'address' => 'required|string|max:255',
                'ifu' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'secret_key' => 'required|string|exists:users,secret_key',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }

            $cateringService = CateringService::findOrFail($id);
            $cateringServiceData = $request->except(['secret_key', 'catering_service_code', 'user_id', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email', 'image']);

            if ($request->hasFile('image')) {
                // Delete the old image, if it exists
                if ($cateringService->image) {
                    Storage::disk('public')->delete($cateringService->image);
                }

                $imagePath = $request->file('image')->store('catering_images', 'public');
                $cateringServiceData['image'] = $imagePath;
            }

            $cateringServiceData['user_id'] = $user->id;
            $cateringServiceData['manager_firstname'] = $user->firstname;
            $cateringServiceData['manager_lastname'] = $user->lastname;
            $cateringServiceData['manager_phone'] = $user->phone;
            $cateringServiceData['manager_email'] = $user->email;

            $cateringService->update($cateringServiceData);

            return response()->json(['message' => 'Catering service updated successfully', 'data' => $cateringService]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Catering service not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $cateringService = CateringService::findOrFail($id);
            // Delete the associated image, if it exists
            if ($cateringService->image) {
                Storage::disk('public')->delete($cateringService->image);
            }
            $cateringService->delete();

            return response()->json(['message' => 'Catering service deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Catering service not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e)
    {
        // Log the exception here if needed

        return response()->json(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
