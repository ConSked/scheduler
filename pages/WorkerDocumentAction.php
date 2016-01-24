<?php // $Id: WorkerDocumentAction.php 1648 2012-09-01 15:37:14Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Document.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('util/ReviewEnum.php');
require_once('swwat/gizmos/parse.php');

$author = getWorkerAuthenticated();

$document = getParamItem(PARAM_LIST2, PARAM_LIST2_INDEX);
unset($_SESSION[PARAM_LIST2]); // not needed anymore

if (!is_null($document))
{
    try
    {
        $status = $_REQUEST[PARAM_STATUSTYPE];
        $status = swwat_parse_enum($status, ReviewEnum::$REVIEW_ARRAY, FALSE);
        // do nothing if no status change
        if (0 != strcmp($status, $document->reviewStatus))
        {
            if (0 == strcmp(DELETE, $status))
            {
                // cannot delete reviewed doc; set it unreviewed first
                // either organizer or self can delete if has not been reviewed
                if ((0 == strcmp(UNREVIEWED, $document->reviewStatus)) &&
                    ($author->isOrganizer() || ($author->workerid == $document->workerid)))
                {
                    $document->delete();
                }
                else
                {
                    throw new ParseSWWATException(); // they futzed the client
                }
            }
            else if ($author->isOrganizer())
            {
                $document->reviewStatus = $status;
                $document->update();
            }
            else
            {
                throw new ParseSWWATException(); // they futzed the client
            }
        }
    }
    catch (ParseSWWATException $ex)
    {
        // ignore; but means they aren't using the client
        header('Location: WorkerLoginPage.php');
        include('WorkerLoginPage.php');
        return;
    }
}
// see button defn in DocumentList; note has nothing to do with $isOrganizer
if (isset($_REQUEST[PARAM_LASTNAME]))
{
    header('Location: WorkerViewPage.php');
    include('WorkerViewPage.php');
}
else
{
    header('Location: WorkerDocumentPage.php');
    include('WorkerDocumentPage.php');
}
return;
?>
