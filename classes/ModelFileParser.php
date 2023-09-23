<?php namespace Waka\MailMjml\Classes;

/**
 * This class parses Mail templates.
 * Returns the structured file information.
 *
 * @author Alexey Bobkov, Samuel Georges
 */
class ModelFileParser
{
    const SECTION_SEPARATOR = '==';

    /**
     * Parses Pdf template content.
     * The expected file format is following:
     * <pre>
     * Settings section
     * ==
     * HTML content section
     * </pre>
     * If the content has  2 sections they are considered as settings and HTML.
     * If there is only a single section, it is considered as HTML.
     * @param string $content Specifies the file content.
     * @return array Returns an array with the following indexes: 'settings', 'html', 'text'.
     * The 'html' and 'text' elements contain strings. The 'settings' element contains the
     * parsed INI file as array. If the content string doesn't contain a section, the corresponding
     * result element has null value.
     */
    public static function parse($content)
    {
        $sections = preg_split('/^={2,}\s*/m', $content, -1);
        $count = count($sections);
        foreach ($sections as &$section) {
            $section = trim($section);
        }

        $result = [
            'settings' => [],
            'mjml'     => '',
        ];
        //trace_log($sections[0]);
        //trace_log($sections[1]);

        if ($count == 2) {
            $result['settings'] = parse_ini_string($sections[0], true);
            $result['mjml'] = $sections[1];
        }
        elseif ($count == 1) {
            $result['mjml'] = $sections[0];
        }
        return $result;
    }
}
