<?php

namespace Duplicator\Utils\Logging;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\DynamicGlobalEntity;
use Error;
use Exception;

/**
 * Class TraceNameMng
 *
 * This class implements the Singleton pattern to manage a single instance
 * of the TraceNameMng class. It prevents direct instantiation and provides
 * a global point of access to the instance.
 */
final class TraceLogMng
{
    const NAME_HASH_LENGTH       = 12;
    const NAME_PREFIX            = 'dup_trace_';
    const NAME_SUFFIX            = '_log.txt';
    const NAME_BACKUP_SUFFIX     = '_log_bak.txt';
    const TRACE_PATH             = DUPLICATOR_PRO_SSDIR_PATH;
    const TRACE_URL              = DUPLICATOR_PRO_SSDIR_URL;
    const MAX_FILE_SIZE          = 2 * MB_IN_BYTES;
    const DEFAULT_MAX_TOTAL_SIZE = 10 * MB_IN_BYTES;
    const TRACE_MAX_SIZE_KEY     = 'trace_max_total_size';

    /** @var ?TraceLogMng The single instance of the TraceNameMng class */
    private static ?TraceLogMng $instance = null;

    /** @var string The current file name of the trace */
    private string $currentFileName = '';

    /** @var int The maximum total size for all trace files */
    private int $maxTotalSize = self::DEFAULT_MAX_TOTAL_SIZE;

    /** @var bool */
    private bool $isInitialized = false;

    /**
     * Private constructor to prevent direct instantiation.
     *
     * Trace name: PREFIX + HASH + SUFFIX
     * Trace backup name: PREFIX + DATE + HASH + BACKUP_SUFFIX
     */
    private function __construct()
    {
        $this->maxTotalSize = DynamicGlobalEntity::getInstance()->getValInt(self::TRACE_MAX_SIZE_KEY, self::DEFAULT_MAX_TOTAL_SIZE);

        $traceFiles = SnapIO::regexGlob(
            self::TRACE_PATH,
            [
                'regexFile'   => '/^' . self::NAME_PREFIX . '.*' . self::NAME_SUFFIX . '$/',
                'regexFolder' => false,
            ]
        );

        switch (count($traceFiles)) {
            case 0:
                $traceFile = '';
                break;
            case 1:
                $traceFile = $traceFiles[0];
                break;
            default:
                $traceFile = $traceFiles[0];
                for ($i = 1; $i < count($traceFiles); $i++) {
                    unlink($traceFiles[$i]);
                }
                break;
        }

        if (strlen($traceFile) > 0) {
            $this->currentFileName = basename($traceFile);
        } else {
            $this->createNewTraceFile();
        }

        $this->isInitialized = true;
    }

    /**
     * Creates a new trace file with a unique hash.
     *
     * This function generates a new hash for the trace, constructs the file path,
     * and ensures that any existing file with the same name is deleted before
     * creating a new empty file.
     *
     * @return void
     * @throws Exception If the file cannot be created.
     */
    private function createNewTraceFile(): void
    {
        try {
            $this->currentFileName = self::NAME_PREFIX . self::generateHash() . self::NAME_SUFFIX;
            $filepath              = self::TRACE_PATH . '/' . $this->currentFileName;


            if (file_exists($filepath)) {
                if (!unlink($filepath)) {
                    throw new Exception("Failed to delete existing trace file: $filepath");
                }
            } else {
                if (!file_exists(self::TRACE_PATH)) {
                    wp_mkdir_p(self::TRACE_PATH);
                }

                if (!is_dir(self::TRACE_PATH)) {
                    throw new Exception("Trace path is not a directory: " . self::TRACE_PATH);
                }
            }

            if (file_put_contents($filepath, '') === false) { // Don't use touch because in some hosting is disabled
                throw new Exception("Failed to create new trace file: $filepath");
            }

            $this->isInitialized = true;
        } catch (Exception | Error $e) {
            // No trace is available disable trace mode until the next script execution
            $this->currentFileName = '';
            $this->isInitialized   = false;
            SnapUtil::errorLog("Error creating new trace file: " . $e->getMessage());
            SnapUtil::errorLog("No trace is available");
        }
    }

    /**
     * Gets the current file name of the trace.
     *
     * This function constructs the file name using the defined prefix,
     * the current hash, and the defined suffix.
     *
     * @return string The current file name of the trace.
     */
    public function getCurrentFileName(): string
    {
        return $this->currentFileName;
    }

    /**
     * Gets the current filepath of the trace.
     *
     * @return string The current filepath of the trace.
     */
    public function getCurrentFilepath(): string
    {
        if (!$this->isInitialized) {
            return '';
        }
        return self::TRACE_PATH . '/' . $this->currentFileName;
    }

    /**
     * Gets the current URL of the trace.
     *
     * @return string The current URL of the trace.
     */
    public function getCurrentURL(): string
    {
        if (!$this->isInitialized) {
            return '';
        }
        return self::TRACE_URL . '/' . $this->currentFileName;
    }

    /**
     * Gets cumulative size of all trace files.
     *
     * @return int returns the cumulative size of all trace files in bytes, 0 if no trace files exist
     */
    public function getTraceFilesSize(): int
    {
        $files = $this->getTraceFiles();
        $size  = 0;

        foreach ($files as $file) {
            $size += (int)filesize($file);
        }
        return $size;
    }

    /**
     * Gets the single instance of the TraceNameMng class.
     *
     * @return TraceLogMng The instance of the TraceNameMng class.
     */
    public static function getInstance(): TraceLogMng
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generates a random hash for the trace file.
     *
     * @return string The generated hash.
     */
    private static function generateHash(): string
    {
        return SnapUtil::generatePassword(self::NAME_HASH_LENGTH, false, false);
    }

    /**
     * Writes a message to the trace log.
     * If the file size exceeds the maximum limit, it will rename the current file
     * and create a new one.
     *
     * @param string $message The message to write to the trace log.
     *
     * @return bool True if the message was written successfully, false otherwise.
     */
    public function write(string $message): bool
    {
        if (!$this->isInitialized) {
            return false;
        }

        try {
            $filepath = $this->getCurrentFilepath();

            // Add timestamp to the message
            $timestamp        = date('Y-m-d H:i:s', time() + SnapWP::getGMTOffset());
            $formattedMessage = "[{$timestamp}] {$message}\n";

            // Append the message to the file
            file_put_contents($filepath, $formattedMessage, FILE_APPEND);
            // Don't generate errors if the file is not writable

            // Check if the file size exceeds the maximum limit
            if (filesize($filepath) > self::MAX_FILE_SIZE) {
                $this->rotateTraceFile();
            }
        } catch (Exception | Error $e) {
            SnapUtil::errorLog("Error writing to trace file: " . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Rotates the trace file by renaming the current file with a timestamp
     * and creating a new one.
     *
     * @return void
     */
    private function rotateTraceFile(): void
    {
        $currentFilepath = $this->getCurrentFilepath();

        // Generate backup filename with date and current hash
        $date           = date('Ymd_His');
        $backupFilename = self::NAME_PREFIX . $date . '_' . self::generateHash() . self::NAME_BACKUP_SUFFIX;
        $backupFilepath = self::TRACE_PATH . '/' . $backupFilename;

        // Rename the current file to the backup file
        if (!rename($currentFilepath, $backupFilepath)) {
            SnapUtil::errorLog("Failed to rename trace file from {$currentFilepath} to {$backupFilepath}");
            // If can't rename, delete the current file
            unlink($currentFilepath);
        }
        $this->isInitialized = false;

        // Check if total size exceeds the maximum and delete oldest backup if needed
        $this->cleanupOldBackups();

        // Create a new trace file
        $this->createNewTraceFile();
    }

    /**
     * Deletes the oldest backup files if the total size exceeds the maximum.
     *
     * @return void
     */
    private function cleanupOldBackups(): void
    {
        if ($this->maxTotalSize <= 0) {
            // 0 means no limit
            return;
        }

        $totalSize = $this->getTraceFilesSize();

        if ($totalSize <= $this->maxTotalSize) {
            return;
        }

        // Get all backup files
        $backupFiles = SnapIO::regexGlob(
            self::TRACE_PATH,
            [
                'regexFile'   => '/^' . self::NAME_PREFIX . '.*' . self::NAME_BACKUP_SUFFIX . '$/',
                'regexFolder' => false,
            ]
        );

        // Sort by modification time (oldest first)
        usort($backupFiles, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Delete oldest backups until we're under the limit
        foreach ($backupFiles as $file) {
            $fileSize = filesize($file);
            if (unlink($file)) {
                $totalSize -= $fileSize;
                if ($totalSize <= $this->maxTotalSize) {
                    break;
                }
            }
        }
    }

    /**
     * Gets a list of all trace log files (current and backups).
     *
     * @return string[] An array of trace log file paths.
     */
    public function getTraceFiles(): array
    {
        $currentFiles = SnapIO::regexGlob(
            self::TRACE_PATH,
            [
                'regexFile'   => '/^' . self::NAME_PREFIX . '.*' . self::NAME_SUFFIX . '$/',
                'regexFolder' => false,
            ]
        );

        $backupFiles = SnapIO::regexGlob(
            self::TRACE_PATH,
            [
                'regexFile'   => '/^' . self::NAME_PREFIX . '.*' . self::NAME_BACKUP_SUFFIX . '$/',
                'regexFolder' => false,
            ]
        );

        $legacyFiles = SnapIO::regexGlob(
            self::TRACE_PATH,
            [
                'regexFile'   => '/^dup_pro_.*_log.txt$/',
                'regexFolder' => false,
            ]
        );

        return array_merge($currentFiles, $backupFiles, $legacyFiles);
    }

    /**
     * Deletes all trace log files (current and backups).
     *
     * @return bool True if all files were deleted successfully, false otherwise.
     */
    public function deleteAllTraceFiles(): bool
    {
        $files   = $this->getTraceFiles();
        $success = true;

        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        // Create a new trace file if the current one was deleted
        if ($success) {
            $this->createNewTraceFile();
        }

        return $success;
    }

    /**
     * Sets the maximum total size for all trace files.
     *
     * @param int $maxSizeInBytes The maximum size in bytes
     *
     * @return void
     */
    public function setMaxTotalSize(int $maxSizeInBytes): void
    {
        $this->maxTotalSize = max(self::MAX_FILE_SIZE, $maxSizeInBytes);
        DynamicGlobalEntity::getInstance()->setValInt(self::TRACE_MAX_SIZE_KEY, $this->maxTotalSize, true);
    }

    /**
     * Get the maximum total size for all trace files, 0 means no limit
     *
     * @return int
     */
    public function getMaxTotalSize(): int
    {
        return $this->maxTotalSize;
    }
}
