<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function(){
	$response = ['Not found.'];
    	$statusCode = 404;
	return Response::json($response, $statusCode);
});

Route::get('/api', function(){
	$response = ['Not found.'];
    	$statusCode = 404;
	return Response::json($response, $statusCode);
});

Route::get('/api/hoursreport/{project_id}/{user_id}/{start}/{end}', function($project_id, $user_id, $start, $end){
	$start=date("Y-m-d", $start);
	$end=date("Y-m-d", $end);
	try{
            $response = [
              'timeEntries' => []
            ];
            $statusCode = 200;

            $range = [$start, $end];
            $timeEntries = TimeEntry::where('user_id', $user_id)->where('project_id', $project_id)->whereBetween('spent_on', $range)->get();
            $hours=0;
            foreach($timeEntries as $timeEntry){
              $response['timeEntries'][] = [
                'id'            => $timeEntry->id,
                'spent_on'      => $timeEntry->spent_on,
                'hours'         => $timeEntry->hours,
              ];
              $hours+=$timeEntry->hours;
            }

            $response['hours']=$hours;
            $response['start']=$start;
            $response['end']=$end;

            $response['user']=User::find($user_id);
            $response['project']=Project::find($project_id);
          }catch (Exception $e){
            $statusCode = 404;
          }finally{
            return Response::json($response, $statusCode);
        }
});

Route::group( array('prefix' => 'api/'), function(){
	
	Route::resource('users', 'UserController');
	Route::resource('projects', 'ProjectController');
	//Route::resource('hoursreport', 'HoursreportController');
});

Route::get('/testprojectshours', function(){
	return $users = DB::table('users')
            ->join('time_entries', 'users.id', '=', 'time_entries.user_id')
            ->join('projects', 'time_entries.project_id', '=', 'projects.id')
            ->select('users.login', 'time_entries.id', 'time_entries.spent_on', 'time_entries.hours', 'projects.name')
	    ->where('projects.id', '14')
            ->get();	
	
});

Route::get('/api/hoursproject/{project_id}/{start}/{end}', function($project_id, $start, $end){
	$start=date("Y-m-d", $start);
	$end=date("Y-m-d", $end);
	try{
            $response = [
              'timeEntries' => []
            ];
            $statusCode = 200;

            $range = [$start, $end];
            $timeEntries = TimeEntry::where('project_id', $project_id)->whereBetween('spent_on', $range)->get();
            $hours=0;

	    $response['start'] = $start;
	    $response['end'] = $end;

            foreach($timeEntries as $timeEntry){
              $response['timeEntries'][] = [
                'id'            => $timeEntry->id,
		'user_id'	=> $timeEntry->user_id,
                'spent_on'      => $timeEntry->spent_on,
                'hours'         => $timeEntry->hours,
              ];
              $hours+=$timeEntry->hours;
            }

	    $resources = array();
            foreach( $timeEntries as $timeEntry ){
                $user = User::find($timeEntry->user_id);
                if( !in_array( $user, $resources)  ){
                    array_push($resources, $user);
                }
            }

	    $response['resources']=$resources;
            $response['hours']=$hours;
            $response['project']=Project::find($project_id);
          }catch (Exception $e){
            $statusCode = 404;
          }finally{
            return Response::json($response, $statusCode);
        }
});

Route::get('api/lastloggedhours', function()
{
    try {	
	$response = [ 'timeEntries'=>[] ];
	$statusCode = 200;

	$timeEntries = TimeEntry::orderBy('id', 'desc')->take(5)->get();

	foreach($timeEntries as $timeEntry){
            $response['timeEntries'][] = [
                'id'            => $timeEntry->id,
                'user_id'       => $timeEntry->user_id,
                'project_id'    => $timeEntry->project_id,
                'spent_on'      => $timeEntry->spent_on,
                'comments'      => $timeEntry->comments,
                'hours'         => $timeEntry->hours,
                'project'       => Project::find($timeEntry->project_id),
		'user'		=> User::find($timeEntry->user_id)
            ];
        }
    }catch (Exception $e){
        $statusCode = 404;
    }finally{
        return Response::json($response, $statusCode);
    }

    
});

Route::get('api/projectsuser/{user_id}', function($user_id)
{

    try {	
	$response = [ 'projects'=>[] ];
	$statusCode = 200;
	
	$timeEntries = TimeEntry::where('user_id', $user_id)->get();
	$projects=array();
	foreach($timeEntries as $timeEntry){
	    $project = Project::find($timeEntry->project_id);
	    if( !in_array($project, $projects) )
		    array_push($projects, $project);
	}
	$response['projects']=$projects;
    }catch (Exception $e){
        $statusCode = 404;
    }finally{
        return Response::json($response, $statusCode);
    }
    
});

Route::get('/api/hoursuser/{user_id}/{project_id}/{start}/{end}', function($user_id, $project_id, $start, $end)
{
    $start=date("Y-m-d", $start);
    $end=date("Y-m-d", $end);

    try
    {
	$response = [ 'timeEntries' => [] ];
	$statusCode = 200;

	//User Data
	$response['user'] = User::find($user_id);
	$response['start'] = $start;
	$response['end'] = $end;

	//Project and Time Entries Between Range
	$range = [$start, $end];
	if( $project_id!="null" ){
            $response['projects'][]=Project::find($project_id);
            $timeEntries = TimeEntry::where('project_id', $project_id)->where('user_id', $user_id)->whereBetween('spent_on', $range)->orderBy('spent_on', 'ASC')->get();
	}
	else{
            $response['project']=NULL;
	    $timeEntries = TimeEntry::where('user_id', $user_id)->whereBetween('spent_on', $range)->orderBy('spent_on', 'ASC')->get();	    
	    $projects=array();
	    foreach( $timeEntries as $timeEntry){
		$project = Project::find($timeEntry->project_id);
		if( !in_array($project, $projects) )
		    array_push($projects, $project);
	    }
	    $response['projects']=$projects;
	}

	// Hours
        $hours = 0;
        foreach($timeEntries as $timeEntry){
            $response['timeEntries'][] = [
                'id'            => $timeEntry->id,
		'user_id'	=> $timeEntry->user_id,
		'project_id'	=> $timeEntry->project_id,
                'spent_on'      => $timeEntry->spent_on,
                'comments'      => $timeEntry->comments,
                'hours'         => $timeEntry->hours,
		'project'  => Project::find($timeEntry->project_id)
            ];
            $hours+=$timeEntry->hours;
        }
        $response['hours']=$hours;

    }catch (Exception $e){
        $statusCode = 404;
    }finally{
        return Response::json($response, $statusCode);
    }
});
