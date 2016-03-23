<?php

class HoursreportController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function show($data)
	{
	  try{
            $response = [
              'timeEntries' => []
            ];
            $statusCode = 200;

	    $range = ['2015-08-15', '2015-08-20'];
            $timeEntries = TimeEntry::where('user_id', 1)->where('project_id', 15)->whereBetween('spent_on', $range)->get();
	    $hours=0;
            foreach($timeEntries as $timeEntry){
              $response['timeEntries'][] = [
                'id'            => $timeEntry->id,
		'spent_on'	=> $timeEntry->spent_on,
                'hours'         => $timeEntry->hours,
              ];
	      $hours+=$timeEntry->hours;
            }

	    $response['hours']=$hours;
	    $response['user']=User::find(1);
	    $response['project']=Project::find(15);
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
	 
	public function show($id)
	{
		//
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
