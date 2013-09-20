<?php

if(!function_exists('tag')) PHPharo::LoadLibrary('HtmlHelper');

$replace = array(
                    '8)' => '<img src="http://www.freesmileys.org/smileys/smiley-basic/cool.gif" alt="8)" title="8)" />',
                    ':D' => tag(array('img'),"alt=':D' title=':D' src='http://www.freesmileys.org/smileys/smiley-basic/biggrin.gif'"),
                    ';)' => tag(array('img'),'title=";)" alt=";)" src="http://www.freesmileys.org/smileys/smiley-basic/wink.gif"'),
                    '[exec_time]' => PHPharo::With(B)->exec_time('phpharo.start'),
                    '[welcome]' => tag(array('img'),"title='[welcome]' alt='[welcome]' src='http://www.freesmileys.org/smileys/smiley-basic/welcome.gif'"),
                    '[bootstrap.css]' => PHPharo::Assets('css/bootstrap.css')
                );

PHPharo::Render('welcome/tpl.html', true, $replace);