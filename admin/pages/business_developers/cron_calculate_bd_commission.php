<?php

/**
 * Cron job script for automated monthly BD Commission calculations
 * This file is designed to be executed by a server cron job scheduler
 * Recommended schedule: Monthly (1st day of each month)
 * 
 * Example cron expression: 0 0 1 * * (Runs at midnight on the 1st day of each month)
 */

// Disable direct access if accessed via browser
if (isset($_SERVER['REMOTE_ADDR'])) {
  die('This script can only be executed via cron job');
}

// Set error reporting for cron job
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set a custom error handler to log PHP errors
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
  logMessage("PHP Error [$errno]: $errstr in $errfile on line $errline");
  return true;
});

// Initialize logging
$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/bd_commission_' . date('Y-m') . '.log';

// Create logs directory if it doesn't exist
if (!file_exists($logDir)) {
  try {
    mkdir($logDir, 0755, true);
    file_put_contents($logDir . '/test.txt', 'Log directory created successfully');
  } catch (Exception $e) {
    // If we can't create the log directory, log to /tmp
    $logDir = '/tmp';
    $logFile = $logDir . '/bd_commission_' . date('Y-m') . '.log';
  }
}

/**
 * Function to log messages to the log file
 */
function logMessage($message)
{
  global $logFile;
  $timestamp = date('Y-m-d H:i:s');
  $logMessage = "[$timestamp] $message" . PHP_EOL;
  file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Start time for performance tracking
$startTime = microtime(true);
logMessage("Starting BD Commission calculation process");

// Include the necessary files
try {
  // Use truly absolute paths for server environment
  $basePath = __DIR__; // This is more reliable than dirname(__FILE__)

  // Log the actual paths we're using
  logMessage("Base directory path: " . $basePath);

  // Check if the files exist before attempting to include them
  $initFile = $basePath . '/../../../includes/init.php';
  $commissionFile = $basePath . '/calculate_bd_commission.php';

  logMessage("Checking for init.php at: " . $initFile);
  logMessage("Checking for calculate_bd_commission.php at: " . $commissionFile);

  if (!file_exists($initFile)) {
    logMessage("ERROR: init.php not found at " . $initFile);
    // Try looking for it in common alternate locations
    $possiblePaths = [
      '/home/u650672385/public_html/phonemate/includes/init.php',
      '/home/u650672385/public_html/includes/init.php',
      $basePath . '/includes/init.php'
    ];

    foreach ($possiblePaths as $path) {
      logMessage("Trying alternate path: " . $path);
      if (file_exists($path)) {
        $initFile = $path;
        logMessage("Found init.php at alternate path: " . $initFile);
        break;
      }
    }
  }

  if (!file_exists($commissionFile)) {
    logMessage("ERROR: calculate_bd_commission.php not found at " . $commissionFile);
  }

  // Include the files if they exist
  if (file_exists($initFile)) {
    include_once($initFile);
    logMessage("Successfully included init.php");
  } else {
    throw new Exception("Could not find init.php at any location");
  }

  if (file_exists($commissionFile)) {
    include_once($commissionFile);
    logMessage("Successfully included calculate_bd_commission.php");
  } else {
    throw new Exception("Could not find calculate_bd_commission.php");
  }
} catch (Exception $e) {
  logMessage("Error including required files: " . $e->getMessage());
  exit(1);
}

// Run the calculation for all BDs for the previous month
try {
  // Calculate for previous month by default
  $previousMonth = date('Y-m-01', strtotime('first day of last month'));
  $result = calculateMonthlyCommissions($previousMonth);

  if ($result['success']) {
    logMessage("Commission calculation completed successfully");
  } else {
    logMessage("Commission calculation failed: " . $result['message']);
  }

  // Calculate execution time
  $executionTime = microtime(true) - $startTime;
  logMessage("Execution completed in " . number_format($executionTime, 2) . " seconds");

  // Exit with appropriate status code
  exit($result['success'] ? 0 : 1);
} catch (Exception $e) {
  logMessage("Fatal error during commission calculation: " . $e->getMessage());
  exit(1);
}
