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
        'case', // case "" | case ("")
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
        if ($keyword === 'case') {
            $regex = "/\b{$keyword}\s*\(?([\w\W]+?)\)?\s*:/m";
        } else {
            $regex = "/\b{$keyword}\s*\(((?:[^()]|\((?1)\))*+)\)\s*:/m";
        }
        $replacement = " @endphp " . PHP_EOL . " @{$keyword} ($1) " . PHP_EOL . " @php ";

        $context = preg_replace($regex, $replacement, $context);

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