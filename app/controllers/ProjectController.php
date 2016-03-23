<?php

class ProjectController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            try{
                $response = [
                    'projects' => []
                ];
                $statusCode = 200;
                $projects = Project::all();
                foreach($projects as $project){
		    if( $project->parent_id ){
                        $response['projects'][] = [
                            'id'		=> $project->id,
               	 	    'name' 		=> $project->name,
               	 	    'summary' 		=> $project->summary,
                	    'created_on' 	=> $project->created_on,
                	    'status' 		=> $project->status
              	        ];
		    }
                }
            }catch (Exception $e){
    	        $statusCode = 404;
            }finally{
                return Response::json($response, $statusCode);
            }
        }
		
	


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
        public function show($id)
        {
	    try{
                $response = [
                    'project' => []
            	];
            	$statusCode = 200;

		$project = Project::find($id);

		$timeEntries = TimeEntry::where('project_id', $id)->get();
		$users = array();
		foreach( $timeEntries as $timeEntry ){
		    $user = User::find($timeEntry->user_id);
		    if( !in_array( $user, $users)  ){
			array_push($users, $user);
		    }
		}

	 	$timeEntriesByUser = DB::table('users')
	            ->join('time_entries', 'users.id', '=', 'time_entries.user_id')
	            ->join('projects', 'time_entries.project_id', '=', 'projects.id')
	            ->select('users.login', 'time_entries.id', 'time_entries.spent_on', 'time_entries.hours', 'projects.name')
	            ->where('projects.id', $id)
	            ->get();

            	$response = [
                    'id'		=> $project->id,
                    'name' 		=> $project->name,
                    'summary' 		=> $project->summary,
                    'created_on' 	=> $project->created_on,
                    'status' 		=> $project->status,
		    'resources'		=> $users,
		    'timeEntries'	=> $timeEntriesByUser
              	];
            }catch (Exception $e){
    	    	$statusCode = 404;
            }finally{
            	return Response::json($response, $statusCode);
            }      
        }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
