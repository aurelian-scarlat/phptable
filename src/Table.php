<?php

namespace Ascarlat\PhpTable;

use Ascarlat\PhpTable\Traits\StaticCallerTrait;

/**
 * Generates an HTML table
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
     * Set the header values
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


    protected function _setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    protected function _setBody(array $data): self
    {
        $this->body = array_merge($this->body, $data);
        return $this;
    }

    protected function _addRow(array $row): self
    {
        $this->body[] = $row;
        return $this;
    }

    protected function _htmlBlock(string $blockTag, string $cellTag, array $rows, ?bool $pretty = false): string
    {
        $html = '';
        foreach ($rows as $row) {
            $html .= (new Row($row, $cellTag))->html($pretty);
        }
        return (new Node($blockTag))->setRawContent($html)->html($pretty * 2);
    }

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
            $html .= $this->_htmlBlock('thead', 'th', [$header], $pretty);
        }

        if (!empty($this->body)) {
            $body = [];
            foreach ($this->body as $i => $rows) {
                foreach ($this->columns as $col) {
                    $body[$i][] = $rows[$col];
                }
            }
            $html .= $this->_htmlBlock('tbody', 'td', $body);
        }

        $footerValues = $this->headerAsFooter ? $this->header : $this->footer;

        if (!empty($footerValues)) {
            $footer = [];
            foreach ($this->columns as $col) {
                $footer[] = $footerValues[$col];
            }
            $html .= $this->_htmlBlock('tfoot', 'td', [$footer], $pretty);
        }


        return (new Node('table'))->setAttribute('border', '1')->setRawContent($html)->html($pretty);
    }

    public function __toString(): string
    {
        return $this->html();
    }
}
