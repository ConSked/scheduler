<?php  // $Id: Document.php 1649 2012-09-01 15:46:49Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/crypt.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');

define("DOCUMENT_SELECT_PREFIX", "SELECT DISTINCT documentid, expoid, workerid, uploadDate, reviewDate, reviewStatus, docType, docMime, docName FROM ");
define("DOCUMENT_SELECT_EXPO",   DOCUMENT_SELECT_PREFIX . " document WHERE expoid = ? ");
define("DOCUMENT_SELECT_WORKER", DOCUMENT_SELECT_PREFIX . " document WHERE workerid = ? ");
define("DOCUMENT_SELECT_ID",     DOCUMENT_SELECT_WORKER . " AND documentid = ? ");
define("DOCUMENT_SELECT_TYPE",   DOCUMENT_SELECT_WORKER . " AND docType = ? ");
// because content is typically big, you have to go back for a 2nd bite at the apple
define("DOCUMENT_SELECT_CONTENT",  "SELECT content FROM document WHERE workerid = ? AND documentid = ?");

class Document
{

public $documentid;
public $expoid;
public $workerid;
public $uploadDate;
public $reviewDate;
public $reviewStatus;
public $docType;
public $docMime;
public $docName;
public $content;

private function fixDates()
{
	if (is_string($this->uploadDate))
	{
		$this->uploadDate = swwat_parse_date($this->uploadDate);
	}
	if (is_string($this->reviewDate))
	{
		$this->reviewDate = swwat_parse_date($this->reviewDate);
	}
} // fixDates

public function selectContent()
{
    $dbh = getPDOConnection();
    $stmt = $dbh->prepare(DOCUMENT_SELECT_CONTENT);
    $stmt->execute(array($this->workerid, $this->documentid));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (1 != count($rows))
    {
        return NULL;
    }
    $this->content = decryptField($rows[0]['content']);
    return;
} // selectContent

// require selector to know workerId - i.e. Fred cannot select Frank's document
public static function selectID($documentId, $workerId)
{
	try
	{
		$rows = simpleSelect("Document", DOCUMENT_SELECT_ID, array($workerId, $documentId));
		if (1 != count($rows))
		{
			return NULL;
		}
		$rows[0]->fixDates();
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage("Document::selectID($documentId, $workerId)", $pe->getMessage());
	}
} // selectID

public static function selectExpo($expoId)
{
	try
	{
		$rows = simpleSelect("Document", DOCUMENT_SELECT_EXPO, array($expoId));
		for ($k = 0; $k < count($rows); $k++)
		{
			$rows[$k]->fixDates();
		} // $k
		return $rows;
	}
	catch (PDOException $pe)
	{
		logMessage('Document::selectExpo(' . $expoId . ')', $pe->getMessage());
	}
} // selectExpo

public static function selectWorker($workerId)
{
	try
	{
		$rows = simpleSelect("Document", DOCUMENT_SELECT_WORKER, array($workerId));
		for ($k = 0; $k < count($rows); $k++)
		{
			$rows[$k]->fixDates();
		} // $k
		return $rows;
	}
	catch (PDOException $pe)
	{
		logMessage('Document::selectWorker(' . $workerId . ')', $pe->getMessage());
	}
} // selectWorker


public function insert()
{
    $dbh = getPDOConnection();
    $dbh->beginTransaction();
    $stmt = $dbh->prepare("INSERT INTO document (expoid, workerid, docType, docMime, docName, content) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(array($this->expoid, $this->workerid,
                            $this->docType, $this->docMime,
                            $this->docName, encryptField($this->content)));
    $this->documentid = $dbh->lastInsertId(); // note before commit
    $dbh->commit();
    return $this;
} // insert

public function update()
{
	try
	{
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE document SET reviewDate = CURRENT_TIMESTAMP, reviewStatus = ? WHERE documentid = ?");
        $stmt->execute(array($this->reviewStatus, $this->documentid));
        $dbh->commit();
        return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('Document::update()', $pe->getMessage());
	}
} // insert

public function delete()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("DELETE FROM document WHERE documentid = ? AND reviewStatus = 'UNREVIEWED'");
		$stmt->execute(array($this->documentid));
		$dbh->commit();
		return NULL;
	}
	catch (PDOException $pe)
	{
		logMessage('Document::delete()', $pe->getMessage());
	}
} //delete

} // Document

?>
