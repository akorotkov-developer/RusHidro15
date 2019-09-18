<?

class dtimer
{
    var $warnTime = 1.0;

    function _getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function dtimer()
    {
        $this->start = $this->_getmicrotime();
        $this->ticks[0] = new stdClass();
        $this->ticks[0]->time = $this->start;
        $this->ticks[0]->label = "Start";
        $this->ticks[0]->delta = 0.0;
    }

    function tick($label)
    {
        $idx = count($this->ticks);
        $this->ticks[$idx] = new stdClass();
        $this->ticks[$idx]->time = $this->_getmicrotime();
        $this->ticks[$idx]->label = $label;
        $this->ticks[$idx]->delta = $this->ticks[$idx]->time - $this->ticks[$idx - 1]->time;
    }

    function currentTime()
    {
        $alltime = $this->ticks[count($this->ticks) - 1]->time - $this->start;
        return substr($alltime, 0, 8);
    }

    function show()
    {
        $res = "<br><center><font color='gray' size='1'><table>";
        for ($i = 1; $i < count($this->ticks); $i++) {
            //$res .= sprintf("<tr><td>%s&nbsp;</td><td>%s</td></tr>", $this->ticks[$i]->label, substr($this->ticks[$i]->delta, 0,6));
            $mask = "<tr><td>%s&nbsp;</td><td>%s</td></tr>";

            if ($this->ticks[$i]->delta > $this->warnTime)
                $mask = "<tr><td>%s&nbsp;</td><td style=\"color:red\">%s</td></tr>";

            $res .= sprintf($mask, $this->ticks[$i]->label,
                number_format($this->ticks[$i]->delta, 6, '.', ' '));
        }

        $alltime = $this->ticks[count($this->ticks) - 1]->time - $this->start;
        $res .= sprintf("</table><b>Summ time: %s</b>", substr(sprintf("%0.10f", $alltime), 0, 8));
        return $res;
    }
}

?>