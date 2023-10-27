<?php

namespace App\Http\Controllers\Api;

use App\Models\favourite;
use Exception;
use in;
use auth;
use App\Models\book;
use App\Models\User;
use App\Models\rating;
use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageProcessing;

class BooksController extends Controller
{
    use ImageProcessing;

    public function index(request $request){
        /*['photos' => function ($query) {
            $query->select('src');
         
        }]*/
        //$book=book::all();
        Log::info('try to get  books');
        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 1 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
        $book = Book::with('categories')->with('addresses')->with('users:id,name,email')->Paginate(
            $pageSize,
            ['*'],
            'page',
            $currentPage
        );

       //$book->load('photos');
       foreach ($book as $key => $onebook) {
        $onebook['image']=$onebook->getfirsturl();
       }

      //  $book['image']=$book->photos()->src;
        Log::info($book);
   //     $photo=$book->photos->select('src')->first();
      //  $book['image']=$photo;
        if($book->count()>0){
            if ($book->hasMorePages()) {
                $totalPages = $book->lastPage();
                } else {
                $totalPages = 1; // or whatever you want to display when there's only one page
                }
            $data=[
                'status'=>200,
                'lastpage'=>$totalPages,
                'books'=>$book
    
            ];
                return  response()->json($data, 200 );
            
        }
        else{
            $data=[
                'status'=>404,
                'message'=>'no record found'
            ];
            return  response()->json($data, 200 );
        }
        }
    public function get_category(){
            $category=category::all();
            if($category->count()>0){
                $data=[
                    'status'=>200,
                    'recordernumber'=>$category->count(),
                    'categorys'=>$category
        
                ];
                    return  response()->json($data, 200 );
                
            }
            else{
                $data=[
                    'status'=>404,
                    'message'=>'no record found'
                ];
                return  response()->json($data, 200, );
            }
            }
       // "required|string :pending,active,inactive,rejected | max:191"
    
    public function store(Request $request){
            $validator=Validator::make( $request->all(),[
                'image' => 'required|array',
                'image.*' => 'image|mimes:jpg,jpeg,png,gif,svg,tiff,webp ',
                "name"=>"required|string| max:191",
                "status"=> Rule::in(['جديد', 'مستعمل بحاله جيد','مستعمل بحاله متوسط','فاقد بعض الورق','بحالة سئ']),
                "price"=>"required|numeric",
                "author"=>"string| max:70",
                "addresse_id"=>"required|exists:Addresses,id",
                "discription"=>"string |max:191",
                "category"=> "required|array|exists:categories,id",
            ]

        );
       // $user_id=auth::user();
        $user_id=auth('sanctum')->user()->id;
         Log::info(gettype($request->category));
         Log::info(($request->category));
         Log::info(gettype($request->image));
         Log::info(($request->image));

        if($validator->fails()){
            Log::info($validator->messages());
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 200);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        else{
            try{
            $book=book::create([
                'user_id'=>$user_id,
                'name'=>$request->name,
                'status'=>$request->status,
                'price'=>$request->price,
                'author'=>$request->author,
                'addresse_id'=>$request->addresse_id,
                'discription'=>$request->discription,
            ]);
        }
        catch(Exception $e){
            Log::info($e);
        }

        if ($request->has('category')) {
            $book->categories()->sync($request->category);
        }
        
        if ($book) {

            Log::info('before enter');
           // Log::info($data);
           $files = $request->file('image');

           $image= $this->storeimage($book->id,$files );

             Log::info('after enter');

                return  response()->json([
                    'status'=>200,
                    'message'=>'book created right',
                    'book'=>$book,
                    "image"=> $image
                ], 200);
            }else{
                return  response()->json([
                    'status'=>404,
                    'message'=>'their is an error happened',
                ], 404);
            }
        }
    
        }
    
    public function show($id){
            
            $book = book::with('categories')->with('addresses')->with('users')->find($id);
            $book["users"]['image'] =$book->users->getprofileimage();
           // $book->users->load('getprofileimageurlAttribute');
            $user_id=auth('sanctum')->user()->id;
            $seller_id=$book->user_id;
            $rate = rating::where('user_id',$user_id)->where('seller_id',$seller_id)->first('rating');
            
            $avgrate = rating::where('seller_id',$id)->avg('rating');
            Log::info('print');
           $book['image']=$book->getallurl();
            if($avgrate==null){
                $avgrate='has no rate';
            }
            if($rate==null){
                $rate['rating']='not rate';
            }
           
            $book['users']['myrate']=$rate['rating'];
            $book['favourite']=favourite::where('user_id',$user_id)->where('book_id',$id)->first()!=null ;
            Log::info($book['favourite']);
            $book['users']['avergerate']=$avgrate;
                
            
           // 
            if ($book){
              return response()->json([ 
              'data'=>$book,
                 'status'=>200
              ], 200);
            }
            else{
                return   response()->json('not found');
            }
        }
    
    public function edit($id){
            $book = book::find($id);
            if ($book){
                
       return response()->json([ 
        'data'=>$book,
        'status'=>200
      ], 200);
            }
            else{
                return   response()->json('not found');
            }
    
        }

       
    public function destroy($id){
            $book = book::find($id);
             $user_id=auth('sanctum')->user()->id;

            if($book){
                if($book->user_id != $user_id ){
                
                    return  response()->json(['message' => 'This user doesn\'t have the permison to do that  ','status'=>'403'],403 );
                }
                $book ->delete();
                return response()->json([
                    'status'=>200 ,
                    'message'=>'book deleted succesfully ',
                ]
                , 200);
            }
            else{
                return   response()->json([ 'status'=>404
                ,'message'=>'No book found'],404);
            }
        }
    public function userproduct($id){

      $user = User::find($id);
      if(! $user){
       return response()->json([ 
        'message'=>'NO USER WITH THIS ID',
           'status'=>404
        ], 404);
          }
        $list_books=  book::with('categories')->with('addresses')->where('user_id',$id)->get();
        if (count($list_books)>0){
            return response()->json([ 
            'data'=>$list_books,
               'status'=>200
            ], 200);
          }
          else{
              return   response()->json(['message'=>'THIS USER HAS NO BOOKS ',
              'status'=>404],402);
          }
    }
    public function bookfilterM($model, Request $request) {
        
        if ($request->has('author')) {
            $model->where('author', 'LIKE', $request->get('%author%'));
        }
        if ($request->has('name')) {
            $model->where('name', 'LIKE', $request->get('name'));
        }

   

     if ($request->has('lowestprice')) {
      $model->where('price', '>', $request->get('lowestprice'));
        }


       if ($request->has('highprice')) {
      $model->where('price', '<', $request->get('highprice'));
      }


     $categoryFilter = request()->category;
     if ($categoryFilter != '') {
        $model->whereHas('category', function ($query) use ($categoryFilter) {
           $query->where('id', 'LIKE', "%{$categoryFilter}%");
       });

     }
     return $model;

   
    }
    public function filterbook(Request $request){
        
        $books=book::query();
        
        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 1 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
        // Filter by author
        $authorFilter = $request->author;
        if ($authorFilter != '') {
            
            $books = $books->where('author', 'LIKE', "%$authorFilter%");
        }

        // Filter by name of book
        $nameFilter = request()->bookname;
        if ($nameFilter != '') {
            $books = $books->where('name', 'LIKE', "%{$nameFilter}%");
           
        }
        $usernameFilter = request()->username;
        if ($usernameFilter != '') {
            $books = $books->whereHas('users', function($query) use ($usernameFilter){
                $query->where('name', $usernameFilter);
            });
           
        }
         // Filter by lowestprice
        $lowestpriceFilter = $request->lowestprice;
        if ($lowestpriceFilter != '') {
            $books = $books->where('price', '>', (int)$lowestpriceFilter);
         
        }
        $highpriceFilter = request()->highprice;
        if ($highpriceFilter != '') {
            $books = $books->where('price', '<', (int)$highpriceFilter);
         
        }
        $categoryFilter = request()->category;
        if ($categoryFilter != '') {

            $books = $books->when( $categoryFilter, function($query) use ($categoryFilter){
                foreach( $categoryFilter as $category){
                    $query->whereHas('categories', function($query) use ($category){
                        $query->where('id', $category);
                    });
                }
            });

      }
      $books=$books->with('categories')->with('addresses')->Paginate(
        $pageSize,
        ['*'],
        'page',
        $currentPage
    );
;
     if($books){
         $data=[
        'status'=>200,
        'data'=>$books,
        'lastpage'=>$books->lastPage(),


     ];
        return  response()->json($data, 200 );
        }
        else{
            $data=[
                'status'=>404,
                'message'=>'no books found',
    
            ];
                return  response()->json($data, 404 );
        }
      

     }
    /*         $distanceFilter = request()->input('distance');
        if ($distanceFilter != '') {
            $location1={
                'latitude':,
                'longitude':,
            } ;
            $location2={
                'latitude':,
                'longitude':,
            } ;

          $books = $books->whereHas('Addresse', function ($query) use ($distanceFilter) {

              $query->where('name', 'LIKE', "%{$distanceFilter}%");
         });
        }
    */
    function calculatHaversineDistance($location1, $location2)
    {
        $earthRadius = 6371; // kilometers
    
        $dLat = deg2rad($location2['latitude'] - $location1['latitude']);
        $dLon = deg2rad($location2['longitude'] - $location1['longitude']);
    
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($location1['latitude'])) * cos(deg2rad($location2['latitude'])) * sin($dLon / 2) ** 2;
    
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        return $earthRadius * $c;
    }
    
    function deg2rad($degrees)
    {
        return $degrees * pi() / 180;
    }
    
    function rad2deg($radians)
    {
        return $radians * 180 / pi();
    }

    public function storeimage($book_id,$files )
    {

        $user=auth('sanctum')->user();
        info('1' .$book_id);
       // info('122'.$files);
        info('122'.gettype($files));
  
            if(!$book_id){
                
                return   response()->json(["message"=>"please provide book id"],501);
            }

            foreach ($files as $key => $file) {
                $path =  $this->saveImageAndThumbnail($file,false);

                $result= $this->bookImages($user,$path,$book_id);
            }

                return response()->json($result, 200);
        

    }
    

    public function bookImages($user,$path,$book_id){
        $book=book::where('id',$book_id)->first();

        if($user->id!=$book->user_id){
            return ['mesage'=>"you are not authorized to access this"];
        }
         $book->photos()->create([
            'src'=>$path,
            'type'=>'photo',
            
        ]);

        return ['message'=>'update image success','data'=>$book->photos()->get()];   
     }





    }
