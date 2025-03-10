<?php
namespace Ascarlat\PhpTable;

use Ascarlat\PhpTable\Traits\StaticCallerTrait;

/**
 * Class to manage one table row, no matter if it's a header, footer or body row
 */
class Row
{
    use StaticCallerTrait;

    protected string $tag;
    protected array  $cells;

    /**
     * Constructor
     *
     * @param array $cells An array of strings with the content of each cell
     * @param string|null $tag If <td> or <th> should be used for output
     */
    public function __construct(array $cells, ?string $tag = 'td')
    {
        $this->tag = $tag;
        $this->cells = $cells;
    }

    /**
     * Getter for $cells
     *
     * @return array
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * Generate the HTML for this row
     *
     * @param bool $pretty Returns the generated HTML easier to read
     *
     * @return string
     */
    public function html(?bool $pretty = false): string
    {
        $html = '';
        foreach ($this->cells as $cell) {
            $html .= (new Node($this->tag, $cell))->html($pretty * 6);
        }
        $html = (new Node('tr'))->setRawContent($html)->html($pretty * 4);
        return $html . ($pretty ? PHP_EOL : '');
    }

    /**
     * So you can use a row instance in a string operation
     * (e.g. echo, print or concatenation)
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->html();
    }
}
