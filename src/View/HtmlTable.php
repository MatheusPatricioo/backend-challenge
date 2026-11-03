<?php

namespace Contatoseguro\TesteBackend\View;

class HtmlTable
{
    public static function render(array $rows): string
    {
        $html = "<table style='font-size: 10px; white-space: pre-line;'>";
        foreach ($rows as $row) {
            $html .= "<tr>";
            foreach ($row as $column) {
                $html .= "<td>{$column}</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table>";

        return $html;
    }
}