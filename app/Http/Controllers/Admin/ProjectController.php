<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Type;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects=Project::all();

        return view("admin.project.index", compact("projects"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $categories =  Type::all();
        return view("admin.project.create", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {   
        $data=$request->validated();
        $data=$request->all();
        
        if(key_exists("cover_img",$data)){
            $path=Storage::disk('public')->put("projects", $data["cover_img"]);
        }else{
            $path="";
        };
    
        $project=new Project();
        $project->name=$data["name"];
        $project->description=$data["description"];
        $project->cover_img=$path;
        $project->github_link=$data["github_link"];
        $project->type_id=$data["type_id"];
        $project->save();

        return redirect()->route("admin.projects.show", $project->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project=Project::find($id);

        return view("admin.project.show", compact("project"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project=Project::find($id);
        if(!$project){
            abort(404, "Ritenta");
        }
        $categories =  Type::all();

        return view("admin.project.edit", compact("project", "categories"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $data=$request->validated();
        $data = $request->all();
        

        
        if (key_exists("cover_img", $data)) {
            
            $path=Storage::disk('public')->put("projects", $data["cover_img"]);

            
            Storage::disk('public')->delete($project->cover_img);
        }

        $project->update([
            ...$data,
            
            "cover_img" => $path ?? $project->cover_img
        ]);

        return redirect()->route("admin.projects.show", $project->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project=Project::findOrFail($id);
        

        if ($project->cover_img) {
            Storage::disk('public')->delete($project->cover_img);
        }

        $project->delete();

        return redirect()->route("admin.projects.index");
    }
}
