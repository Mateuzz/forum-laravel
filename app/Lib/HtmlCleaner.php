<?php

namespace App\Lib;

require __DIR__ . '/../../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

/* const ELEMENTS = ' */
/*     h1, h2, h3, h4, h5, h6, */
/*     blockquote, p, ul, ol, li, span, table[class],u */
/*     tbody, tr, td, div, font[color], */
/*     img[src][alt], */
/*     a[href], */
/*     *[style] */
/* '; */

const STYLES = 'font-size,text-align,background-color,color';

const CLASSES = 'table,table-bordered,note-video-clip';

const YOUTUBE_IFRAME_REGEX = '%^https?://www.youtube.com/embed/%';

class HtmlCleaner {
    protected \HTMLPurifier $purifier;

    public function __construct() {
        $config = \HTMLPurifier_Config::createDefault();

        /* $config->set('HTML.Allowed', ELEMENTS); */
        $config->set('Attr.AllowedClasses', CLASSES);
        $config->set('CSS.AllowedProperties', STYLES);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', YOUTUBE_IFRAME_REGEX);
        $config->set('URI.DisableExternalResources', true);

        $config->set('HTML.DefinitionID', 'html-default');
        $config->set('HTML.DefinitionRev', 6);

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addAttribute('a', 'target', 'Enum#_target,_self,_blank,_top,_parent');
            $def->addAttribute('img', 'data-filename', 'CDATA');
            /* $def->addAttribute('img', 'src', 'CDATA'); */
        }

        $this->purifier = new \HTMLPurifier($config);
    }

    public function clean(string $html): string {
        return $this->purifier->purify($html);
    }
}
