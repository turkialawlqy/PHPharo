<?php


/**
 * PHP-Pharaoh => pp
 * H => MyHooks
 * ---------------------
 * They are shortcuts :)
 */

function minify_output()
{
    /** Here i get the output buffer from phpharo globals manager */
    $buffer = ob_get_clean();
    /** Here i Load The Output Helper `it will be loaded only if didn`t loaded` */
    PHPharo::LoadLibrary('Output');
    /** Here i Show Compressed And Minified Output `deflate` and remove spacess, newlines, .. */
    $buffer = '<!-- Compressed & Minified -->' . preg_replace('#\s+#',' ', $buffer);
    echo ob_compress($buffer, 'deflate', 9);    
}

/** Here i Hooked minify_output() to be exceuted in phpharo.output  */
/** Note: I set it`s order to heigh order to make it the last executed  */
/** As You Cannot Compress or Show anything after Compression  */
/** And Set The Arguments Of minify_output function to an empty array */
PHPharo::With(H)->register_action('phpharo.shutdown', 'minify_output', array(), 20);


