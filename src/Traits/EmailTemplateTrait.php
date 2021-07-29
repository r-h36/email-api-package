<?php

namespace Rh36\EmailApiPackage\Traits;

use Illuminate\Support\Facades\Blade;
use Rh36\EmailApiPackage\Models\EmailLog;

trait EmailTemplateTrait
{

    /**
     * Compile blade template with passing arguments.
     *
     * @param string $value HTML-code including blade
     * @param array $args Array of values used in blade
     * @return string
     */
    public function compileTemplateWiths($value, array $args = array())
    {
        $generated = Blade::compileString($value);

        ob_start() and extract($args, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try {
            eval('?>' . $generated);
        }

        // If we caught an exception, we'll silently flush the output
        // buffer so that no partially rendered views get thrown out
        // to the client and confuse the user with junk.
        catch (\Exception $e) {
            ob_get_clean();
            throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }


    public function composeBody(EmailLog $emaillog): array
    {
        if ($emaillog->use_template) {

            $html = $this->compileTemplateWiths($emaillog->template->template_body, json_decode($emaillog->template_data, true));
            $text = str_replace(PHP_EOL, '',  strip_tags($html));
            return [
                'HtmlBody' => $html,
                'TextBody' => $text
            ];
        } else {
            return [
                'HtmlBody' => $emaillog->plain_content,
                'TextBody' => str_replace(PHP_EOL, '',  strip_tags($emaillog->plain_content)),
            ];
        }
    }
}
