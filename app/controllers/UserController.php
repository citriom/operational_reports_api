<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	  try{
            $response = [
                'users' => []
            ];
            $statusCode = 200;
            $users = DB::select('select * from users');
	    $users = User::all();

            foreach($users as $user){
		if( $user->type=="User"){
                    $response['users'][] = [
                        'id' => $user->id,
                        'login' => $user->login,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
		        'last_login_on' => $user->last_login_on
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
                'user' => []
            ];
            $statusCode = 200;
            $user = User::find($id);	
            $response = [
                'id'          => $user->id,
                'login'       => $user->login,
                'firstname'   => $user->firstname,
                'lastname'    => $user->lastname,
		'admin'	      => $user->admin,
		'timeEntries' => $user->timeEntries(),
		'projects'    => $user->projects()
            ];
	    
          }catch (Exception $e){
            $statusCode = 404;
          }finally{
            return Response::json($response, $statusCode);
          }
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
