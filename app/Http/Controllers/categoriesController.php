<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Categories;

class categoriesController extends Controller
{
    //
    public function create(Request $request){
        $validator = Validator::make($request->all(),['name' => 'required|max:255|string']);

        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }   
        $validated = $validator->validate();


        $oldCategories = Categories::where('name',$validated['name'])->first();
        
        if($oldCategories){
            return response()->json('Kategori sudah ada')->setStatusCode(422);
        }
        Categories::create([
            'name' => $validated['name']
        ]);
        return response()->json('Kategori berhasil disimpan')->setStatusCode(201);
    }
    public function read(){
        $categories = Categories::all();
        return response()->json($categories)->setStatusCode(200);
    }
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(),['name' => 'required|max:255|string']);
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $Category = Categories::find($id);


        if($Category){
            
            $Category->update($validator->validated());
            return response()->json('Kategori berhasil diubah')->setStatusCode(201);
        }
        return response()->json('Data kategori tidak ditemukan')->setStatusCode(404);        
    }
    public function delete($id){

        //cari id yang akan di delete
        $checkData = Categories::find($id);

        //kondisi jika datanya ada
        if($checkData){
            //Code untuk hapus
            Categories::where('id', $id)->delete();

            return response()->json('Kategori berhasil dihapus')->setStatusCode(200);
        }
        return response()->json('Data kategori tidak ditemukan')->setStatusCode(404);
    }
}
