<?php
namespace Buddie\Service;

use Buddie\Service\Database;

/**
 * Logger class, write messages to database or to screen
 */
class Logger
{
    private $inBrowser;

    /**
     * Determine if we're in browser or CLI at start
     *
     * @return void
     */
    public function __construct()
    {
        $this->inBrowser = !empty($_SERVER['DOCUMENT_ROOT']) ? true : false;
    }

    private function getDatabase()
    {
        if (empty($this->db)) {
            #$this->db = new Database(false);
        }
    }

    public function write($iLevel, $sError, $aSource = [])
    {
      $aBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
      $sFile = basename($aBacktrace[1]['file']);
      $sFunction = $aBacktrace[1]['function'];
      $iLine = $aBacktrace[1]['line'];
      $sClass = $aBacktrace[1]['class'];
      $sSource = ($aSource ? serialize($aSource) : '');
      echo ROOTPATH;
      file_put_contents(WRITEPATH."logs/logs.log", 
        date("Y-m-d h:m:s")." $sClass [$sError] $sSource $iLevel $iLine $sFile".PHP_EOL
      );
      /**
        $this->getDatabase();
        
        $this->db->query('
            INSERT INTO twitterlog
            (botname, error, source, level, line, file, timestamp)
            VALUES
            (:bot, :error, :source, :level, :line, :file, NOW())',
            [
                ':bot' => $sClass,
                ':error' => $sError,
                ':source' => $sSource,
                ':level' => $this->getErrorLevelName($iLevel),
                ':line' => $iLine,
                ':file' => sprintf('%s->%s', $sFile, $sFunction),
            ]
        );*/
    }

    private function getErrorLevelName($iLevel)
    {
        $aErrorLevels = [
            1 => 'FATAL',
            2 => 'ERROR',
            3 => 'WARN',
            4 => 'INFO',
            5 => 'DEBUG',
            6 => 'TRACE',
        ];

        return (isset($aErrorLevels[$iLevel]) ? $aErrorLevels[$iLevel] : '?');
    }

    /**
     * Output message to screen
     *
     * @param ..string
     *
     * @return void
     */
    public function output()
    {
      //return ;
        $aArgs = func_get_args();

        if ($this->inBrowser) {
          $aArgs[0] .= '<br>' . PHP_EOL;
        } else {
            $aArgs[0] = PHP_EOL . PHP_EOL.strip_tags($aArgs[0]);
        }

        call_user_func_array('printf', $aArgs);
    }

    public function view()
    {
        $this->getDatabase();

        return $this->db->query('
            SELECT *
            FROM twitterlog
            ORDER BY timestamp DESC
            LIMIT 100'
        );

    }

    public function search($sSearch)
    {
        $this->getDatabase();

        return $this->db->query('
            SELECT *
            FROM twitterlog
            WHERE botname LIKE :search
            OR error LIKE :search
            OR level LIKE :search
            OR file LIKE :search
            ORDER BY timestamp DESC
            LIMIT 100',
            [':search' => '%' . $sSearch . '%']
        );
    }
}
