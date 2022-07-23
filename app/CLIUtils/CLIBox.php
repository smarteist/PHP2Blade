<?php


namespace App\CLIUtils;


class CLIBox
{
    /**
     * @var array
     */
    private $style;
    /**
     * @var array|CLIStr
     */
    private $content;
    /**
     * @var int
     */
    private $boxWidth;

    public function __construct($style = [])
    {
        $this->style = array_merge(
            [
                'padding' => 2,
                'margin' => 4,
                'align' => 'center',
                'theme' => 1,
                'tableColor' => ''
            ],
            $style
        );
    }

    public function getBox($content, $title = null, $footer = null)
    {
        $themes = [
            1 => [
                'tr' => '╗',
                'tl' => '╔',
                'br' => '╝',
                'bl' => '╚',
                'h' => '═',
                'v' => '║',
            ],
            2 => [
                'tr' => '┐',
                'tl' => '┌',
                'br' => '┘',
                'bl' => '└',
                'h' => '─',
                'v' => '│',
            ],
            3 => [
                'tr' => '╮',
                'tl' => '╭',
                'br' => '╯',
                'bl' => '╰',
                'h' => '─',
                'v' => '│',
            ],
            4 => [
                'tr' => '+',
                'tl' => '+',
                'br' => '+',
                'bl' => '+',
                'h' => '-',
                'v' => '|',
            ],
        ];
        if (!is_array($this->style['theme'])) {
            $this->style['theme'] = $themes[$this->style['theme']] ?: $themes[1];
        }
        if (!is_a($title, CLIStr::class)) {
            $title = CLIStr::create("");
        }
        if (!is_a($footer, CLIStr::class)) {
            $footer = CLIStr::create("");
        }
        $this->content = is_array($content) ? $content : explode(PHP_EOL, $content);
        for ($i = 0; $i < count($this->content); $i++) {
            if (!is_a($this->content[$i], CLIStr::class)) {
                $this->content[$i] = CLIStr::create($this->content[$i]);
            }
        }
        $this->boxWidth = $this->getBoxWidth($this->content, $title, $footer);

        $box = '';
        // BOX HEAD
        $box .= $this->drawBoxHead($title);
        // BOX CONTENT
        $box .= $this->drawBoxContent();
        // BOX FOOTER
        $box .= $this->drawBoxFoot($footer);
        return $box;
    }

    private function getBoxWidth($content, $title, $footer)
    {
        $boxWidth = max($title->length(), $footer->length()) + (2 * $this->style['padding']) + 2;
        foreach ($content as $str) {
            $length = $str->length() + (2 * $this->style['padding']) + 2;
            if ($length > $boxWidth) {
                $boxWidth = $length;
            }
        }
        return $boxWidth;
    }

    private function drawBoxMargin()
    {
        $margin = CLIStr::create("");
        for ($i = 0; $i < $this->style['margin']; $i++) {
            $margin->append(
                CLIStr::create("")
                    ->append(CLIStr::strRepeat(' ', $this->style['margin']))
                    ->append(CLIStr::strRepeat(' ', $this->boxWidth))
                    ->append(CLIStr::strRepeat(' ', $this->style['margin']))
                    ->setColors($this->style['tableColor']) . PHP_EOL
            );
        }
        return $margin;
    }

    private function drawBoxHead($title)
    {
        $theme = $this->style['theme'];
        // BOX HEAD
        $head1 = CLIStr::create(CLIStr::strRepeat(' ', $this->style['margin']))
            ->append(CLIStr::strRepeat($theme['tl']))
            ->append(CLIStr::strRepeat($theme['h']))
            ->setColors($this->style['tableColor']);

        $head2 = CLIStr::create(CLIStr::strRepeat($theme['h'], $this->boxWidth - $title->length() - 3))
            ->append(CLIStr::strRepeat($theme['tr']))
            ->append(CLIStr::strRepeat(' ', $this->style['margin']))
            ->setColors($this->style['tableColor']);

        return $this->drawBoxMargin() . $head1 . $title . $head2 . PHP_EOL;
    }

    private function drawBoxContent()
    {
        $boxContent = CLIStr::create($this->drawBoxContentPadding());
        foreach ($this->content as $str) {
            $boxContent->append($this->drawBoxContentAlignment($str));
        }
        return $boxContent->append($this->drawBoxContentPadding());
    }

    private function drawBoxContentAlignment($content)
    {
        $theme = $this->style['theme'];
        $align = $this->style['align'];
        $boxLeft = CLIStr::create(CLIStr::strRepeat(' ', $this->style['margin']))
            ->append(CLIStr::strRepeat($theme['v']))
            ->append(CLIStr::strRepeat(' ', $this->style['padding']))
            ->setColors($this->style['tableColor']);

        $boxContent = CLIStr::create();
        if ($align === 'right') {
            $boxContent->append(CLIStr::strRepeat(' ', $this->boxWidth - (2 * $this->style['padding']) - $content->length() - 2))
                ->append($content);
        } else if ($align === 'center') {
            $space = $this->boxWidth - (2 * $this->style['padding']) - $content->length() - 2;
            $start = intval($space / 2);
            $boxContent->append(CLIStr::strRepeat(' ', $start))
                ->append($content)
                ->append(CLIStr::strRepeat(' ', $space - $start));
        } else {
            $boxContent->append($content)
                ->append(CLIStr::strRepeat(' ', $this->boxWidth - (2 * $this->style['padding']) - $content->length() - 2));
        }

        $boxRight = CLIStr::create(CLIStr::strRepeat(' ', $this->style['padding']))
            ->append(CLIStr::strRepeat($theme['v']))
            ->append(CLIStr::strRepeat(' ', $this->style['margin']))
            ->setColors($this->style['tableColor']);

        return $boxLeft . $boxContent . $boxRight . PHP_EOL;

    }

    private function drawBoxContentPadding()
    {
        $theme = $this->style['theme'];
        $boxPadding = CLIStr::create();
        for ($i = 0; $i < $this->style['padding']; $i++) {
            $boxPadding->append(
                CLIStr::create(CLIStr::strRepeat(' ', $this->style['margin']))
                    ->append(CLIStr::strRepeat($theme['v']))
                    ->append(CLIStr::strRepeat(' ', $this->boxWidth - 2))
                    ->append(CLIStr::strRepeat($theme['v']))
                    ->append(CLIStr::strRepeat(' ', $this->style['margin']))
                    ->setColors($this->style['tableColor']) . PHP_EOL
            );
        }
        return $boxPadding;

    }

    private function drawBoxFoot($footer)
    {
        $theme = $this->style['theme'];
        // BOX FOOTER
        $footer1 = CLIStr::create(CLIStr::strRepeat(' ', $this->style['margin']))
            ->append(CLIStr::strRepeat($theme['bl']))
            ->append(CLIStr::strRepeat($theme['h']))
            ->setColors($this->style['tableColor']);

        $footer2 = CLIStr::create(CLIStr::strRepeat($theme['h'], $this->boxWidth - $footer->length() - 3))
            ->append(CLIStr::strRepeat($theme['br']))
            ->append(CLIStr::strRepeat(' ', $this->style['margin']))
            ->setColors($this->style['tableColor']);

        return $footer1 . $footer . $footer2 . PHP_EOL . $this->drawBoxMargin();

    }

}
