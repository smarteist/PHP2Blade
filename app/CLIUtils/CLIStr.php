<?php


namespace App\CLIUtils;


class CLIStr
{
    private $consoleStyle = [
        'ts' => [
            'reset_all' => '0',
            'bold' => '1',
            'dim' => '2',
            'italic' => '3',
            'underlined' => '4',
            'blink' => '5',
            'invert' => '7',
            'hidden' => '8',
            'strikethrough' => '9',
            'reset_bold' => '21',
            'reset_dim' => '22',
            'reset_italic' => '23',
            'reset_underlined' => '24',
            'reset_blink' => '25',
            'reset_invert' => '27',
            'reset_hidden' => '28',
            'reset_strikethrough' => '29',
        ],
        'fg' => [
            'black' => '30',
            'red' => '31',
            'green' => '32',
            'yellow' => '33',
            'blue' => '34',
            'purple' => '35',
            'cyan' => '36',
            'light_gray' => '37',
            'default' => '39',
            'dark_gray' => '90',
            'light_red' => '91',
            'light_green' => '92',
            'light_yellow' => '93',
            'light_blue' => '94',
            'light_purple' => '95',
            'light_cyan' => '96',
            'white' => '97'
        ],
        'bg' => [
            'black' => '40',
            'red' => '41',
            'green' => '42',
            'yellow' => '43',
            'blue' => '44',
            'purple' => '45',
            'cyan' => '46',
            'light_gray' => '47',
            'default' => '49',
            'dark_gray' => '100',
            'light_red' => '101',
            'light_green' => '102',
            'light_yellow' => '103',
            'light_blue' => '104',
            'light_purple' => '105',
            'light_cyan' => '106',
            'white' => '107',
        ]
    ];

    private $value;

    private $textStyle = "0";

    private $foreground = "39";

    private $background = "49";

    /**
     * CLIString constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = strval($value);
    }

    public static function strRepeat($string, $times = 1)
    {
        $output = '';
        for ($i = 0; $i < $times; $i++) {
            $output .= $string;
        }
        return $output;
    }

    public static function consoleWrite($string, $removePrev = false)
    {
        global $consolePrevLines;
        if ($removePrev) {
            if (is_int($consolePrevLines))
                self::clearLines($consolePrevLines);
            $consolePrevLines = sizeof(explode(PHP_EOL, $string));
        }
        echo $string.PHP_EOL;
    }

    public static function clearLines($n = 1)
    {
        // move up and clear
        echo self::strRepeat("\033[1A" . "\x1b[2K", $n);
    }

    public static function tableRow($cols = [], $colsWidth = [], $separator = '')
    {
        if (empty($cols)) {
            return '';
        }
        $mask = $separator;
        if (sizeof($cols) === sizeof($colsWidth)) {
            for ($i = sizeof($cols) - 1; $i >= 0; $i--) {
                $size = intval($colsWidth[$i]);
                $mask = $separator . "%-{$size}s " . $mask;
            }
        } else {
            $colWidth = intval(100 / count($cols));
            for ($i = 1; $i <= count($cols); $i++) {
                $mask = $separator . "%-{$colWidth}s " . $mask;
            }
        }
        return CLIStr::create(vsprintf($mask, $cols));
    }

    public static function create($value = null)
    {
        return new self($value);
    }

    public function length()
    {
        return strlen($this->value);
    }

    public function __toString()
    {
        return "\x1b[{$this->textStyle};{$this->foreground};{$this->background}m{$this->value}\x1b[0m";
    }

    // Returns colored string
    public function setColors($foregroundColor = null, $backgroundColor = null)
    {
        // Check if given foreground color found
        if (isset($this->consoleStyle['fg'][$foregroundColor])) {
            $this->foreground = $this->consoleStyle['fg'][$foregroundColor];
        }
        // Check if given background color found
        if (isset($this->consoleStyle['bg'][$backgroundColor])) {
            $this->background = $this->consoleStyle['bg'][$backgroundColor];
        }
        return $this;
    }

    public function append($string)
    {
        $this->value .= $string;
        return $this;
    }

    public function prepend($string)
    {
        $this->value = $string . $this->value;
        return $this;
    }

    public function bold()
    {
        $this->textStyle = $this->consoleStyle['ts']['bold'];
        return $this;
    }

    public function dim()
    {
        $this->textStyle = $this->consoleStyle['ts']['dim'];
        return $this;
    }

    public function italic()
    {
        $this->textStyle = $this->consoleStyle['ts']['italic'];
        return $this;
    }

    public function underlined()
    {
        $this->textStyle = $this->consoleStyle['ts']['underlined'];
        return $this;
    }

    public function blink()
    {
        $this->textStyle = $this->consoleStyle['ts']['blink'];
        return $this;
    }

    public function invert()
    {
        $this->textStyle = $this->consoleStyle['ts']['invert'];
        return $this;
    }

    public function hidden()
    {
        $this->textStyle = $this->consoleStyle['ts']['hidden'];
        return $this;
    }

    public function strikethrough()
    {
        $this->textStyle = $this->consoleStyle['ts']['strikethrough'];
        return $this;
    }

}
