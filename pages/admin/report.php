<?php // $Id: report.php 2196 2012-09-22 18:07:50Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');

class Report
{

private static function dataRow($row, $truncate)
{
    echo "<tr>";
    foreach ($row as $column)
    {
        if (is_null($column))
        {
            $column = "NULL";
        }
        if ($truncate > 0)
        {
            $column = substr($column, 0, $truncate);
        }
        echo "<td>$column</td>";
    }
    echo "</tr>\n";
} // dataRow

public static function tableReport($table, $suffix, $params, $truncate = 20)
{
    $headers = self::tableSchema($table);
    self::columnNames($headers);
    $sql = "SELECT * FROM $table $suffix";
    $rows = self::tableData($sql, $params);
    foreach ($rows as $row)
    {
        self::dataRow($row, $truncate);
    } // $row
    $rows = NULL;
} // tableReport

public static function tableReportRaw($table, $raw, $params, $truncate = 20)
{
    $headers = self::tableSchema($table);
    self::columnNames($headers);
    $rows = self::tableData($raw, $params);
    foreach ($rows as $row)
    {
        self::dataRow($row, $truncate);
    } // $row
    $rows = NULL;
    $headers = NULL;
} // tableReport

private static function tableData($sql, $params)
{
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $pe)
    {
        logMessage("Report::tableData($sql, count:" . count($params) . ")", $pe->getMessage());
        return array();
    }
} // tableReport

private static function tableSchema($table)
{
    $sql = "DESCRIBE $table";
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $pe)
    {
        logMessage("Report::tableSchema($table)", $pe->getMessage());
        return array();
    }
} // tableSchema

private static function columnNames($headers)
{
    echo "<tr>";
    foreach ($headers as $row)
    {
        echo "<th>" . $row['Field'] . "</th>";
    }
    echo "</tr>\n";
} // columnNames

public static function schemaReport($table)
{
    $headers = self::tableSchema($table);
    foreach ($headers as $key => $val)
    {
        echo "<tr><th>" . $key . "</th>";
        echo "<td>" . $val . "</td></tr>\n";
    }
    $headers = NULL;
} // schemaReport



} // Report

?>
