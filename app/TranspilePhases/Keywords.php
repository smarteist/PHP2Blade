<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class Keywords extends TranspilePhase
{

    /**
     * The keywords that takes parameter or conditions
     * so transpiler should be sensitive to these keywords
     */
    const parametricKeywords = [
        "if", // if()
        "for", // for()
        "foreach", // foreach()
        "while", // while()
        "elseif", // elseif()
        "switch", // switch()
        'case', // case ""
    ];

    /**
     * The keywords with simple form, without parameters
     * or conditions.
     */
    const nonParametricKeywords = [
        'break',
        'default',
        'continue',
        "else",
        "endif",
        'endswitch',
        'endfor',
        'endforeach',
        'endwhile',
    ];

    public function doTrans()
    {
        // convert keywords
        foreach ($this::parametricKeywords as $keyword) {
            $this->parametricKeywordsToBlade($this->subject, $keyword);
        }
        foreach ($this::nonParametricKeywords as $keyword) {
            $this->nonParametricKeywordsToBlade($this->subject, $keyword);
        }
    }


    /**
     * @param string $context of raw source code, as pointer
     * @param string $keyword the parametric keyword to be converted
     */
    public function parametricKeywordsToBlade(string &$context, string $keyword)
    {
        preg_match_all("/\b({$keyword}\s*(\((.*)\)|['\"].*['\"])\s*):/m", $context, $parametrics, PREG_SET_ORDER);
        $parametrics = is_array($parametrics) ? $parametrics : [];
        foreach ($parametrics as $parametric) {
            if (sizeof($parametric) === 4) {
                $replacement = " @endphp " . PHP_EOL . " @{$keyword} ($parametric[3]) " . PHP_EOL . " @php ";
            } else {
                $replacement = " @endphp " . PHP_EOL . " @{$keyword} ($parametric[2]) " . PHP_EOL . " @php ";
            }
            $context = str_replace($parametric[0], $replacement, $context);
        }
    }

    /**
     * @param string $context of raw source code, as pointer
     * @param string $keyword the non parametric keyword to be converted
     */
    public function nonParametricKeywordsToBlade(string &$context, string $keyword)
    {
        $replacement = " @endphp " . PHP_EOL . " @{$keyword} " . PHP_EOL . " @php ";
        $context = preg_replace(
            "/\b((?<!@){$keyword}\s*([;:]|\s(?=@endphp)))/m",
            $replacement,
            $context
        );
    }

}