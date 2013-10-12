<?php

/** Load Needed Libraries */
PHPharo::LoadLibrary('HtmlHelper');

/** Set DocType */
PHPharo::Globals('output_doc_type', 'html');

/** Array Of Replacements in Welcome Html File */
$replace = array(
                '8)' => '<img src="http://www.freesmileys.org/smileys/smiley-basic/cool.gif" alt="8)" title="8)" />',
                ':D' => tag(array('img'),"alt=':D' title=':D' src='http://www.freesmileys.org/smileys/smiley-basic/biggrin.gif'"),
                ';)' => tag(array('img'),'title=";)" alt=";)" src="http://www.freesmileys.org/smileys/smiley-basic/wink.gif"'),
                '[welcome]' => tag(array('img'),"title='[welcome]' alt='[welcome]' src='http://www.freesmileys.org/smileys/smiley-basic/welcome.gif'"),
                '[bootstrap.css]' => minify_css(ASSETS . 'css/bootstrap.css'),
                '[phpharo.version]' => PP_VERSION,
                '[exec_time]' => PHPharo::With(B)->exec_time('phpharo.start','',3)
                );
                
/** Render The Welcome Html File */
PHPharo::Render(PP_WELCOME_DIR . 'tpl.html', true, $replace);
