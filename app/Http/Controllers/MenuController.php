<?php

namespace App\Http\Controllers;

use Exception; // Corrected namespace
use App\Models\Menu;
use App\Models\MenuImage;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse; // Corrected namespace
use Illuminate\Database\Eloquent\ModelNotFoundException;
class MenuController extends Controller
{
    public function index()
    {
        try {
            $menu = Menu::with('menuImages')->get();
            return response()->json($menu);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching menus', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['name', 'price', 'availability', 'restaurant_code', 'images']), [
                'name' => 'required|string',
                'price' => 'required|string',
                'availability' => 'string',
                'restaurant_code' => 'required|exists:restaurants,restaurant_code',
                'images' => 'array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $restaurant = Restaurant::where('restaurant_code', $request->input('restaurant_code'))->first();

            $menuData = $request->only(['name', 'price', 'availability']);
            $menuData['restaurant_id'] = $restaurant->id;
            $menuData['restaurant_name'] = $restaurant->name;

            $menu = Menu::create($menuData);

            $this->storeImages($menu, $request->file('images'));

            return response()->json(['message' => 'Menu created successfully', 'data' => $menu]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating menu', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $menu = Menu::with('menuImages')->findOrFail($id);
            return response()->json($menu);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Menu not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching menu', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['name', 'price', 'availability', 'restaurant_code', 'images']), [
                'name' => 'required|string',
                'price' => 'required|string',
                'availability' => 'required|string',
                'restaurant_code' => 'required|exists:restaurants,restaurant_code',
                'images' => 'array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $restaurant = Restaurant::where('restaurant_code', $request->input('restaurant_code'))->first();

            $menu = Menu::with('menuImages')->findOrFail($id);
            $menu->update($request->only(['name', 'price', 'availability']));
            $menu->restaurant_id = $restaurant->id;
            $menu->restaurant_name = $restaurant->name;
            $menu->save();

            $this->storeImages($menu, $request->file('images'));

            return response()->json(['message' => 'Menu updated successfully', 'data' => $menu]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Menu not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating menu', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::with('menuImages')->findOrFail($id);
    
            // Delete related images
            foreach ($menu->menuImages as $image) {
                // Delete the image file from storage
                Storage::delete($image->image_path);
    
                // Delete the image record from the database
                $image->delete();
            }
    
            // Delete the menu itself
            $menu->delete();
    
            return response()->json(['message' => 'Menu and related images deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Menu not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting menu', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function storeImages(Menu $menu, $images)
    {
        if ($images) {
            foreach ($images as $image) {
                $imagePath = $image->store('menu_images', 'public');

                $menu->menuImages()->create([
                    'restaurant_id' => $menu->restaurant_id,
                    'restaurant_name' => $menu->restaurant_name,
                    'image_path' => $imagePath,
                ]);
            }
        }
    }
}
