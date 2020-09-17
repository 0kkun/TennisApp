<?php

namespace App\Modules;

use Illuminate\Support\Facades\Log;

class BatchLogger
{
    private $_className = '';
    private $_prefix = '';
    private $_start = 0;
    private $_status = false;
    private $_level = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
    ];
    public function __construct($className)
    {
        $this->_className = $className;
        $this->_prefix = '['. $this->_className. '] ';
        $this->_start = microtime(true);
        Log::info('[START] '. $this->_className);
    }
    public function __destruct()
    {
        $end = microtime(true);
        $status = '';
        if (!$this->_status) {
            $status = ' [異常終了]';
        }
        Log::info('[ END ] '. $this->_className. $status. ' 処理時間: '. ($end - $this->_start). '秒');
    }

    /**
     * update status.
     */
    public function success()
    {
        $this->_status = true;
    }

    /**
     * @param string $message
     * @param string $lv
     * @param bool   $t
     */
    public function write($message, $lv = 'debug', $t = false)
    {
        $time = '';
        if ($t === true) {
            $end = microtime(true);
            $time = ' : ' . ($end - $this->_start) . '秒';
        }
        if(in_array($lv, $this->_level)) {
            Log::$lv($this->_prefix.$message.$time);
        }
    }

    /**
     * @param \Exception $e
     */
    public function exception(\Throwable $e)
    {
        $this->write($e->getMessage() . '   FILE:' . $e->getFile() . ' LINE:' . $e->getLine() . ' ERROR:' . $e->getTraceAsString(), 'error');
    }
}