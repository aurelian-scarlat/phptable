<?php

namespace Ascarlat\PhpTable;

use Ascarlat\PhpTable\Traits\StaticCallerTrait;

/**
 * Generates an HTML table
 *
 * @method setHeader(string[] $array)
 * @method static setHeader(string[] $array)
 * @method setBody(array[] $array)
 * @method static setBody(array[] $array)
 * @method setFooter(string[] $array)
 * @method static setFooter(string[] $array)
 * @method useHeaderAsFooter(bool $what)
 * @method static useHeaderAsFooter(bool $what)
 * @method setColumns(string[] $array)
 * @method static setColumns(string[] $array)
 * @method addRow(string[] $array)
 * @method static addRow(string[] $array)
 */
class Table
{
    use StaticCallerTrait;

    protected array $columns = [];
    protected array $header = [];
    protected array $footer = [];
    protected array $body = [];
    protected bool $headerAsFooter = false;

    /**
     * Set the header values; It can be a numeric or associative array
     * The same keys (numeric or associative) need to be both in header and in each row
     * Usage: Table::setHeader(['Name', 'Email', 'City']);
     *        $table->setHeader(['Name', 'Email', 'City']);
     *
     * @param array $header A list of the values to be placed in the header
     *
     * @return $this
     */
    protected function _setHeader(array $header): self
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Set the footer values
     * Usage: Table::setFooter(['Name', 'Email', 'City']);
     *        $table->setFooter(['Name', 'Email', 'City']);
     *
     * @param array $footer A list of the values to be placed in the footer
     *
     * @return $this
     */
    protected function _setFooter(array $footer): self
    {
        $this->header = $footer;
        return $this;
    }

    /**
     * Use the same header values for footer
     * Usage: Table::useHeaderAsFooter()
     *        $table->useHeaderAsFooter()
     *
     * @param bool $what Flag to use it or not; if set to false
     *
     * @return $this
     */
    protected function _useHeaderAsFooter(?bool $what = true): self
    {
        $this->headerAsFooter = $what;
        return $this;
    }

    /**
     * Set the keys or indexes used for each row, in case they differ from header
     * Usage: Table::setColumns(['name', 'email'])
     *        $table->setColumns(['name', 'email'])
     *
     * @param array $columns The array of columns
     *
     * @return $this
     */
    protected function _setColumns(array $columns): self
    {
        $this->columns = array_values($columns);
        return $this;
    }

    /**
     * The body of the table, it should be an array of rows
     * Usage: Table::setBody([['John', 'Doe', 'New York'], ['Jane', 'Smith', 'Dallas']])
     *        $table->setBody([['John', 'Doe', 'New York'], ['Jane', 'Smith', 'Dallas']])
     *
     * @param array $data The whole data for the table
     *
     * @return $this
     */
    protected function _setBody(array $data): self
    {
        $this->body = array_merge($this->body, $data);
        return $this;
    }

    /**
     * Add one row to the body
     * Usage: Table::addRow(['John', 'Doe', 'New York'])
     *        $table->addRow(['John', 'Doe', 'New York'])
     *
     * @param array $row The array of cells
     *
     * @return $this
     */
    protected function _addRow(array $row): self
    {
        $this->body[] = $row;
        return $this;
    }

    /**
     * Generate a <tbody>, <thead> or <tfoot> block
     *
     * @param string    $blockTag The name of the block tag, e.g. tbody or thead
     * @param string    $cellTag  The name of the cell, e.g. td or th
     * @param array     $rows     The rows to be added in the block
     * @param bool|null $pretty   To add indentation and new lines
     *
     * @return string The generated HTML code
     */
    protected function htmlBlock(string $blockTag, string $cellTag, array $rows, ?bool $pretty = false): string
    {
        $html = '';
        foreach ($rows as $row) {
            $html .= (new Row($row, $cellTag))->html($pretty);
        }
        return (new Node($blockTag))->setRawContent($html)->html($pretty * 2);
    }

    /**
     * Generate the HTML code for the whole table
     *
     * @param bool|null $pretty To add indentation and new lines
     *
     * @return string
     */
    public function html(?bool $pretty = false): string
    {
        if (empty($this->columns)) {
            $this->columns = array_keys($this->header);
        }

        $html = '';

        if (!empty($this->header)) {
            $header = [];
            foreach ($this->columns as $col) {
                $header[] = $this->header[$col];
            }
            $html .= $this->htmlBlock('thead', 'th', [$header], $pretty);
        }

        if (!empty($this->body)) {
            $body = [];
            foreach ($this->body as $i => $rows) {
                foreach ($this->columns as $col) {
                    $body[$i][] = $rows[$col];
                }
            }
            $html .= $this->htmlBlock('tbody', 'td', $body);
        }

        $footerValues = $this->headerAsFooter ? $this->header : $this->footer;

        if (!empty($footerValues)) {
            $footer = [];
            foreach ($this->columns as $col) {
                $footer[] = $footerValues[$col];
            }
            $html .= $this->htmlBlock('tfoot', 'td', [$footer], $pretty);
        }


        return (new Node('table'))->setAttribute('border', '1')->setRawContent($html)->html($pretty);
    }

    /**
     * So you can use a Table instance in a string operation
     * (e.g. echo, print or concatenation)
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->html();
    }
}
