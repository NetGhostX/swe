<?php

class Task
{
    /**
     * @var string Aufgabentext kann z.B. auch LaTeX-Notation enthalten
     */
    private string $text;

    /**
     * @var array Alle verwendeten Variablen mit key als Variable und value als richtigen Wert
     */
    private array $variables;

    /**
     * Erstellt eine neue Aufgabe
     * @param string $text Aufgabentext, kann z.B. auch LaTeX-Notation enthalten
     * @param array $variables Assoziatives Array mit Variable → Richtiger Wert
     */
    public function __construct(string $text, array $variables)
    {
        $this->text = $text;
        $this->variables = array();
        foreach ($variables as $variable => $value) {
            if(!is_string($value)) {
                continue;
            }

            $this->variables[$variable] = $value;
        }
    }

    /**
     * @return string Aufgabentext
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array Assoziatives Array mit Variable → Richtiger Wert
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}