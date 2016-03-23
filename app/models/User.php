<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');


	/** Relation: Time Entries **/
        public function timeEntries()
        {
	    $data = array();
            $timeEntries = TimeEntry::where('user_id', $this->id)->get();
	    foreach( $timeEntries as $timeEntry){
		$project = Project::find($timeEntry->project_id);
		$timeEntry->project_name = $project->name;
		array_push($data, $timeEntry);
	    }
	    return $data;
        }

	/** Relation: Projects **/
        public function projects()
        {
	    $data = array();
	    $timeEntries = TimeEntry::distinct()->select('project_id')->where('user_id', $this->id)->get();
	    foreach( $timeEntries as $timeEntry )
	    {
		$project = Project::where('id', $timeEntry->project_id)->first();
		array_push($data, $project);
	    }
	    return $data;
        }

}
