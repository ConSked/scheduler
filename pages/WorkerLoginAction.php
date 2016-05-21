<?php // $Id: WorkerLoginAction.php 2431 2003-01-07 20:24:44Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Invitation.php');
require_once('db/ShiftPreference.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

try
{
    $email = swwat_parse_string(html_entity_decode($_POST[PARAM_EMAIL]), true);
    $password = swwat_parse_string(html_entity_decode($_POST[PARAM_PASSWORD]), true);

    if (is_null($email))
    {
        throw new LoginException('username required');
    }
    if (is_null($password))
    {
        throw new LoginException('password required');
    }
    // else
    try
    {
        WorkerLogin::password_authenticate($email, $password);
        $password = NULL;
        $worker = getWorkerAuthenticated();
        // see if any invites
        $invitations = Invitation::selectWorker($worker->workerid);
        // look for explicit-only
        foreach ($invitations as $invite)
        {
            if ($invite->workerid == $worker->workerid)
            {
                // default to Registation page
				header('Location: WorkerRegistrationPage.php');
				include('WorkerRegistrationPage.php');
				return;
            }
        } // $invite
		//Go to the proper page
		$expo = Expo::selectActive($worker->workerid);
		if ($worker->isOrganizer())
		{
            // maybe the organizer is not assigned to anything!
			if(!is_null($expo) && $expo->isRunning())
			{
				setExpoCurrent($expo);

				header('Location: ExpoViewPage.php');
				include('ExpoViewPage.php');
				return;
			}
			else
			{
				header('Location: SiteAdminPage.php');
				include('SiteAdminPage.php');
				return;
			}
		}
		else
		{
            // maybe the crew is not assigned to anything!
			if(!is_null($expo) && $expo->isRunning())
			{
				header('Location: WorkerSchedulePage.php');
				include('WorkerSchedulePage.php');
				return;
			}
			else
			{
				$expoList = Expo::selectWorker($worker->workerid);
				if (count($expoList) == 1)
				{
					$future = $expoList[0]->isFuture();
					$preferncesEntered = ShiftPreference::preferencesEntered($expoList[0]->expoid, $worker->workerid);
				}

				if (count($expoList) == 1 && $future && !$preferencesEntered)
				{
					setExpoCurrent($expoList[0]);

				  header('Location: PreferenceWelcomePage.php');
    			include('PreferenceWelcomePage.php');
    			return;
				}
				else
				{
					header('Location: WorkerViewPage.php');
					include('WorkerViewPage.php');
					return;
				}
			}
		}
    }
    catch (RequirePasswordReset $ex)
    {
        header('Location: WorkerLoginChangePage.php');
        include('WorkerLoginChangePage.php');
        return;
    }
}
catch (LoginException $ex)
{
    $password = NULL;
	$error_message = $ex->getMessage();
	//logMessage('login exception for ' . $email, $ex->getMessage());

    header('Location: WorkerLoginPage.php?error='.$error_message);
    include('WorkerLoginPage.php');
}
?>
