<?php

namespace Ascarlat\PhpTable;

/**
 * Class to manage a generic HTML node
 */
class Node
{
    /* Nodes that don't need closing tags */
    protected static array $voidTags = [
        'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input',
        'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

    protected string $tag;
    protected array  $attributes;
    protected string $content;

    /**
     * Constructor
     *
     * @param string      $tag        The tag name (e.g. table, tr, img, a, etc.)
     * @param string|null $content    The content of the node
     *                                Does not apply for void tags (e.g. input, img, etc.)
     *                                This content will be escaped using htmlentities()
     * @param array|null  $attributes Associative array of the attributes
     */
    public function __construct(string $tag, ?string $content = '', ?array $attributes = [])
    {
        $this->tag = $tag;
        $this->content = htmlentities($content);
        $this->attributes = $attributes;
    }

    /**
     * Setter for $content
     *
     * @param string $content The content of the node, which will be escaped
     *
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->setRawContent(htmlentities($content));
        return $this;
    }

    /**
     * Setter for unescaped $content, useful for injecting HTML code as content
     *
     * @param string $content The content of the node, which will NOT be escaped
     *
     * @return $this
     */
    public function setRawContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Setter for $attributes
     *
     * @param array $attributes Associative array, the keys being the name of the
     *                          attribute and the values will be the value of the
     *                          attribute
     *                          The value of an attribute can be an array of strings
     *                          that will be joined using spaces
     *
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Adds one attribute to $attributes
     *
     * @param string $name  The name of the attribute
     * @param string $value The value of the attribute. It can also be an array of
     *                      strings that will be joined using spaces
     *
     * @return $this
     */
    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Add one value to an existing attribute
     * TODO: check that the value doesn't already exist
     *
     * @param string $name  The name of the attribute. If the attribute already is
     *                      defined, this value will be appended to the previous one
     * @param string $value The value of the attribute
     *
     * @return $this
     */
    public function addAttribute(string $name, string $value): self
    {
        if (empty($this->attributes[$name])) {
            $this->setAttribute($name, $value);
            return $this;
        }
        if (!is_array($this->attributes[$name])) {
            $this->attributes[$name] = [$this->attributes[$name]];
        }
        $this->attributes[$name][] = $value;
        return $this;
    }

    /**
     * Set the class attribute for the node
     *
     * @param string $class Class name
     *
     * @return $this
     */
    public function setClass(string $class): self
    {
        return $this->setAttribute('class', $class);
    }

    /**
     * Add classes to the list of classes of this node
     * Usage: $node->addClass('text-primary', 'text-right', 'float-left')
     *
     * @param string ...$class Class name
     *
     * @return $this
     */
    public function addClass(string ...$class): self
    {
        foreach ($class as $c) {
            $this->addAttribute('class', $c);
        }
        return $this;
    }

    /**
     * Set the class attribute for the node
     *
     * @param string $id Class name
     *
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->setAttribute('id', $id);
        return $this;
    }

    /**
     * Generates the HTML code for this node
     *
     * @param int|null $indentation The number of spaces used for indentation
     *                              The content will be indented by an extra 2 spaces
     *                              Use 0 for no indentation
     *
     * @return string
     */
    public function html(?int $indentation = 0): string
    {
        $html = str_repeat(' ', $indentation) . '<' . $this->tag;
        foreach ($this->attributes as $attr_name => $attr_value) {
            $html .= ' ' . $attr_name . '="';
            if (is_array($attr_value)) {
                $attr_value = implode(' ', $attr_value);
            }
            $html .= htmlentities($attr_value) . '"';
        }
        $html .= '>';
        if (!in_array($this->tag, self::$voidTags)) {
            if ($indentation > 0) {
                $html .= "\n" . str_repeat(' ', $indentation + 2) . $this->content . PHP_EOL . str_repeat(' ', $indentation) . '</' . $this->tag . '>' . PHP_EOL;
            } else {
                $html .= $this->content . '</' . $this->tag . '>';
            }
        }
        return $html;
    }

    /**
     * So you can use a Row instance in a string operation
     * (e.g. echo, print or concatenation)
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->html();
    }
}
