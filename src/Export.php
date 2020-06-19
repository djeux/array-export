<?php

declare(strict_types=1);

namespace Djeux\ArrayExport;

/**
 * Class Export
 */
class Export
{
    /**
     * @var bool
     */
    private $shortSyntax = true;

    /**
     * @var string
     */
    private $indentationChar = ' ';

    /**
     * @var int
     */
    private $size = 4;

    /**
     * @var bool
     */
    private $trailingComma = true;

    /**
     * @return $this
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * @param string $char
     * @return $this
     */
    public function indentationChar(string $char): self
    {
        $this->indentationChar = $char;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @param array $array
     * @return string
     */
    public function export(array $array): string
    {
        return $this->content($array);
    }

    /**
     * @param array $array
     * @return string
     */
    public function asFile(array $array): string
    {
        return "<?php\nreturn " . $this->export($array) .';';
    }

    /**
     * @param bool $useShortSyntax
     * @return $this
     */
    public function useShortSyntax(bool $useShortSyntax = true): self
    {
        $this->shortSyntax = $useShortSyntax;

        return $this;
    }

    /**
     * @return string
     */
    private function openingBrace(): string
    {
        return $this->shortSyntax ? "[" : "array(";
    }

    /**
     * @param array|mixed[] $array
     * @param int $level
     * @return string
     */
    private function content(array $array, int $level = 0): string
    {
        $output = $this->openingBrace();

        $elementsCount = count($array);
        $elementPosition = 0;
        $associative = array_keys($array) !== range(0, $elementsCount - 1, 1);
        foreach ($array as $key => $value) {
            if ($elementPosition === 0) {
                $output .= "\n";
            }
            $output .= $this->indent($level + 1);
            if ($associative) {
                if (is_string($key)) {
                    $output .= "'$key'";
                } else {
                    $output .= $key;
                }

                $output .= ' => ' . $this->value($value, $level);
            } else {
                $output .= $this->value($value, $level);
            }

            $elementPosition++;

            if ($elementPosition !== $elementsCount || $this->trailingComma) {
                $output .= ',';
            }

            $output .= "\n";
        }

        if (!empty($array)) {
            $output .= $this->indent($level);
        }

        $output .= $this->closingBrace();

        return $output;
    }

    /**
     * @param int $level
     * @return string
     */
    private function indent(int $level): string
    {
        return str_repeat($this->indentationChar, $level * $this->size);
    }

    /**
     * @param int|string|array $value
     * @param int $level
     * @return string
     */
    private function value($value, int $level): string
    {
        if (is_string($value)) {
            return $this->wrapString($value);
        }

        if (is_int($value)) {
            return (string)$value;
        }

        if (is_array($value)) {
            return $this->content($value, $level + 1);
        }

        if (!is_scalar($value)) {
            throw new \InvalidArgumentException('Values of array can only be scalar or array');
        }
    }

    /**
     * @param string $value
     * @return string
     */
    private function wrapString(string $value): string
    {
        return '\'' . addcslashes($value, "'") . '\'';
    }

    /**
     * @return string
     */
    private function closingBrace(): string
    {
        return $this->shortSyntax ? ']' : ')';
    }
}
