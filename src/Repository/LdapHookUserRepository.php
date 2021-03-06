<?php
namespace LdapHook\Repository;

use Adldap\Laravel\Facades\Adldap;
use Voyager;
use Config;
use Hash;

/**
 * 
 */
class LdapHookUserRepository
{
	
	private $userModel;
	private $roleVoyagerModel;

	public function __construct()
	{
		$this->userModel = Voyager::modelClass('User');
		$this->roleVoyagerModel = Voyager::modelClass('Role');
	}

	public function insertOrUpdateUser($username,$password)
	{
		$user = Adldap::search()->users()->find($username);
        if ($user) {

        	$dataArray = $this->getFormatUser($user,$password);
        	
        	//get Groups formatted in array ex: [1,2,3]
        	$groupsArray = $this->insertOrUpdateGroups($user->getGroupNames());
        
        	$existOnDb = $this->findUserOnDb($username);

        	if(!$existOnDb) {
        		$userModel = new $this->userModel;
        	} else {
        		$userModel = $existOnDb;
        	}

			$userModel->fill($dataArray);
        	$userModel->username = $dataArray['username'];
        	$userModel->user_type = $dataArray['user_type'];

			if ($userModel->save()) {
				//sync groups
				$userModel->roles()->sync($groupsArray);
				return $userModel;
			}
        	
        	return false;

        }

        return false;
	}


	public function insertOrUpdateGroups($groupNames)
	{
		$groupsId = [];
		foreach ($groupNames as $key => $groupName) {
			if(!$dbGroup = $this->findGroupOnDb($groupName)){
				$dbGroup = new $this->roleVoyagerModel;
			}

			$dbGroup->fill($this->formatGroup($groupName));
			$dbGroup->save();
			//fill array ID
			$groupsId[] = $dbGroup->getKey();
		}

		return $groupsId;
	}


	/**
		validate user on db
	**/
	public function validateEloquentUser($username, $password)
	{
		$userDb = $this->userModel::where('email',$username)->first();
		if ($userDb) {
			if (Hash::check($password, $userDb->getAuthPassword())) {
				return $userDb;
			} else {
				return false;
			}
		}

		return false;
	}


	/**
	 *  return formatted user on array
	 */
	private function getFormatUser($userLdap,$password)
	{
		return [
			'name' => $userLdap->getDisplayName(),
			'email' => $userLdap->getUserPrincipalName(),
			'username' => $userLdap->samaccountname[0], //RARE
			'password' => bcrypt($password),
			'role_id' => $this->setDefaultRole(),
			'user_type' => 'ldap'
		];
	}

	/**
		return formatted group on array
	**/
	public function formatGroup($groupName)
	{
		return [
			'name' => $groupName,
			'display_name' => $groupName
		];
	}


	/**
		Find user on DB
	**/
	private function findUserOnDb($username)
	{
		return $this->userModel::where('username',$username)->first();
	}

	/**
	 * Find a group on DB
	 */
	
	private function findGroupOnDb($groupName) 
	{
		return $this->roleVoyagerModel::where('name',$groupName)->orWhere('display_name',$groupName)->first();
	}


	private function setDefaultRole()
	{
		return $this->roleVoyagerModel::where('name','user')->first()->id;
	}

}