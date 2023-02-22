<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;

class AjaxBOOKCRUDController extends Controller
{

    public function index()
    {
        $data['books'] = Book::orderBy('id','desc')->paginate(8);
        if(Book::exists()){
            return view('ajax-book-crud',$data);
        }else{
            $data['empty']      =   true;
            return view('ajax-book-crud',$data);
        }
        
    }
    
    public function store(Request $request)
    {
        // $request->validate([
        //     'title'   => 'required',
        //     'code'   => 'required',
        //     'author'   => 'required'
        // ]);
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'code'   => 'required',
            'author'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->getMessageBag()->toArray()]);
        }else{
            $book   =   Book::updateOrCreate(
                [
                    'id' => $request->id
                ],
                [
                    'title' => $request->title, 
                    'code' => $request->code,
                    'author' => $request->author,
                ]);
    
            return response()->json(['status' => true,'book' =>$book]);
        }
    }
    
    
    public function edit(Request $request)
    {   

        $where = array('id' => $request->id);
        $book  = Book::where($where)->first();
 
        return response()->json($book);
    }
 
   
    public function destroy(Request $request)
    {
        $book = Book::where('id',$request->id)->delete();
   
        return response()->json(['success' => true]);
    }
}