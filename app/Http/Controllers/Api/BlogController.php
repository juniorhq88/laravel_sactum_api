<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    use ApiResponse;

    protected $repository;

    public function __construct(Blog $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id; //capturamos el ID del usuario
        $blogs = $this->repository->where('user_id', $user_id)->paginate();

        return $this->successApiResponse([
            'status' => 1,
            'data' => new BlogResource($blogs)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required",
            "description" => "required"
        ]);

        // Tenemos que traer el id del usuario logueado
        $user_id = auth()->user()->id;
        $blog = new Blog();
        $blog->user_id = $user_id; //aqui tenemos el user_id
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->save();

        return $this->createdApiResponse([
            "status" => 1,
            "msg" => "¡Blog creado exitosamente!"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id = auth()->user()->id;

        if (Blog::where(["id" => $id, "user_id" => $user_id])->exists()) {
            $info = Blog::where(["id" => $id, "user_id" => $user_id])->get();
            return $this->successApiResponse([
                "status" => 1,
                "msg" => BlogResource::collection($info)
            ]);
        }

        return $this->errorApiResponse([
            "status" => 0,
            "msg" => "No se encontró el Blog"
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_id = auth()->user()->id; //capturamos el ID del usuario
        if (Blog::where(["user_id" => $user_id, "id" => $id])->exists()) {


            $blog = Blog::find($id);
            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->save();

            return $this->successApiResponse([
                "status" => 1,
                "msg" => "Blog actualizado correctamente."
            ]);
        }

        return $this->errorApiResponse([
            "status" => 0,
            "msg" => "No se encontró el Blog"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $user_id = auth()->user()->id; //capturamos el ID del usuario
        if (Blog::where(["id" => $id, "user_id" => $user_id])->exists()) {
            $blog = Blog::where(["id" => $id, "user_id" => $user_id])->first();
            $blog->delete();

            //responde la API
            return $this->successApiResponse([
                "status" => 1,
                "msg" => "El blog fue eliminado correctamente."
            ]);
        }

        return $this->errorApiResponse([
            "status" => 0,
            "msg" => "No se encontró el Blog"
        ]);
    }
}
