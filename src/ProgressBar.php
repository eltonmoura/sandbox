<?php
namespace Sandbox;

class ProgressBar
{
    protected $size = 20;

    private $total;

    private $start;

    private $maxLength;

    public function __construct($total)
    {
        $this->total = $total;
        $this->start = time();
    }

    public function display($done = 0)
    {
        $this->done = $done;

        $percent = number_format(($done / $this->total) * 100, 0);
        $bar = $this->getBar($percent);
        $timeInfo = $this->getTimeInfo();

        $return = sprintf(" %s/%s %s %s%% | %s", $done, $this->total, $bar, $percent, $timeInfo);

        // Guarada o maior comprimento da linha para manter preenchido com espaços em branco
        $length = strlen($return);
        if ($length > $this->maxLength) {
            $this->maxLength = $length;
        }
        $return = str_pad($return, $this->maxLength);

        if ($done < $this->total) {
            $return .= "\r";
        } else {
            $return .= "\n";
        }


        print $return;
    }

    private function getBar($percent)
    {
        $totalSize = $this->size;
        $barSize = floor(($percent*$totalSize)/100);

        $barContents = str_repeat('=', $barSize);

        if ($barSize < $totalSize) {
            $barContents .= '>';
        }

        $barContents .= str_repeat(' ', $totalSize - $barSize);
        $barContents = '[' . $barContents . ']';

        return $barContents;
    }

    private function getTimeInfo()
    {
        $now = time();

        $elapsed = $now - $this->start;
        if ($this->done) {
            $rate = $elapsed / $this->done;
        } else {
            $rate = 0;
        }

        $left = $this->total - $this->done;
        $etc = round($rate * $left, 2);

        if ($this->done) {
            $etcNowText = '< 1 sec';
        } else {
            $etcNowText = '???';
        }

        $timeRemaining = self::humanTime($etc, $etcNowText);
        $timeElapsed = self::humanTime($elapsed);

        $timeInfo = sprintf("Elapsed: %s | Remaining: %s", $timeElapsed, $timeRemaining);

        return $timeInfo;
    }

    /**
     * Convert a number of seconds into something human readable like "2 days, 4 hrs"
     *
     * @param int    $seconds how far in the future/past to display
     * @param string $nowText if there are no seconds, what text to display
     *
     * @static
     * @return string representation of the time
     */
    protected static function humanTime($seconds, $nowText = '< 1 sec')
    {
        $prefix = '';
        if ($seconds < 0) {
            $prefix = '- ';
            $seconds = -$seconds;
        }

        $days = $hours = $minutes = 0;

        if ($seconds >= 86400) {
            $days = (int) ($seconds / 86400);
            $seconds = $seconds - $days * 86400;
        }
        if ($seconds >= 3600) {
            $hours = (int) ($seconds / 3600);
            $seconds = $seconds - $hours * 3600;
        }
        if ($seconds >= 60) {
            $minutes = (int) ($seconds / 60);
            $seconds = $seconds - $minutes * 60;
        }
        $seconds = (int) $seconds;

        $return = array();

        if ($days) {
            $return[] = "$days days";
        }
        if ($hours) {
            $return[] = "$hours hrs";
        }
        if ($minutes) {
            $return[] = "$minutes mins";
        }
        if ($seconds) {
            $return[] = "$seconds secs";
        }

        if (!$return) {
            return $nowText;
        }
        return $prefix . implode(array_slice($return, 0, 2), ', ');
    }
}
