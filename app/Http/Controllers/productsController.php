<?php

namespace App\Http\Controllers;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class productsController extends Controller
{

    public function read(){
        $Products = Products::all();
        return response()->json($Products)->setStatusCode(200);
    }
    public function create(Request $request){
        
       
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|max:255|string',
            'description' => 'sometimes|string',
            'price'=> 'required|numeric',
            'category_id' => 'required|exists:categories,name',
            'expired_at'=>'required|date',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ]);
        $imageName = null;
        $jwt = $request->bearerToken();
        $decode = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
        $email = $decode->email;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images',$imageName);
            
        }
       
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }   
       
        $validated = $validator->validate();

        $category = Categories::where('name', $validated['category_id'])->first();

        Products::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $category->id,
            'expired_at' => $validated['expired_at'],
            'image' => 'https://alyaif22a.ylladev.my.id/storage/images/'.$imageName,
            'modified_by' => $email,
            
        ]);
        return response()->json('Produk berhasil disimpan')->setStatusCode(201);
    }
    public function update(Request $request,$id){
        $jwt = $request->bearerToken();
        $decode = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
        $email = $decode->email;

        $validator = Validator::make($request->all(),
        [
            'name' => 'required|max:255|string',
            'description' => 'sometimes|string',
            'price'=> 'required|numeric',
            'category_id' => 'required|exists:categories,name',
            'expired_at'=>'required|date',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ]);
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images',$imageName);
            
            
        }
        $validated = $validator->validate();

        $Products = Products::find($id);

        $category = Categories::where('name', $validated['category_id'])->first();
        if($Products){
    
            $Products->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category_id' => $category->id,
                'expired_at' => $validated['expired_at'],
                'image' => 'https://alyaif22a.ylladev.my.id/storage/images/'.$imageName?? $Products->image,
                'modified_by' => $email,
            ]);
        
            
            return response()->json('Produk berhasil diubah')->setStatusCode(201);
        }
        return response()->json('Data produk tidak ditemukan')->setStatusCode(404); 
    }
    public function delete($id){

        $checkData = Products::find($id);

        
        if($checkData){
            Products::where('id', $id)->delete();

            return response()->json('produk berhasil dihapus')->setStatusCode(200);
        }
        return response()->json('Data produk tidak ditemukan')->setStatusCode(404);
    }
}
