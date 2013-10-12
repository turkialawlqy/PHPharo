<?php

/** ----Routes---- */
MyRouter::add('search.aspx', 'search_engine'); // :D
                   
/* ----------------------------------------- */


function search_engine()
{
    
    /** ----- Autoloads -----  */
    _LB('HttpHelper', 'ArrayHelper', 'HtmlHelper', 'XmlHelper');
    
    /** ----- Requests Filter ----- */
    http_filter_request('GET', 'q', '{STR}');
    http_filter_request('GET', 'p', '{INT}');

    $css =  '   
                body{margin:auto;text-align:center;background:#f9f9f9}
                #txtbox{width:300px;border:1px solid #ddd;outline:0;padding:8px}
                #txtbox:focus{border:1px solid #09f;outline:0}
                #btn{cursor:pointer;background:#0177ED;color:#eee;font-weight:bolder;border:0;padding:8px}
                #btn:hover{background:#0177BD}
                #err{margin:auto;text-align:center;background:#FFF2F2;border:1px solid #F9BFBF;color:maroon;max-width:60%;font-weight:bolder;padding:5px}
            '.Pagination::css();
    $style = minify_css($css,false);
    $meta = tag(array('title','title'),'','Simple Search Engine');
    $meta.= tag(array('meta'),'charset="UTF-8"');
    $header = tag(array('head','head'), '', $meta.tag(array('style','style'),'',$style));
        /* ---------------------- */
    $body_header = tag(array('h1','h1'), 'style="color:#333"', 'Search Engine');
    $form = html_form_factory(array(), array(
                                                array('type' => 'text', 'id' => 'txtbox', 'name' => 'q', 'placeholder' => 'Type Your Words Here', 'value' => array_get($_GET,'q')),
                                                array('type' => 'submit', 'id' => 'btn', 'value' => 'search', 'after' => '<hr />')
     
                                           ));
// هى دى قاعدة البيانا
    $data = array('this is test', 'هذا اختبار', 'phpharo is powerful'
                  ,'الاطار الفرعـــون','egypt','مصر','السعوديه - المملكه العربيه','data & data, google','search engine is good'
                  ,'هذا وهذا وهذا احسن حاجه هههه');
    $search_result = array_xsearch($data, array_get($_GET, 'q'));
    $count = count($search_result);
    $limit = '2';
    Pagination::config(2,$count,$limit,MyRouter::Url('','search.aspx?q='.@$_GET['q']),'p',@$_GET['p']);
    $search_paged = array_slice($search_result, Pagination::start(),Pagination::limit());
    $r_2 = '<b>Search Results</b>: '. $count .' , <b>Pages Number</b>: ' . Pagination::pages_num() . '  ,<b>Search</b>: ' . @$_GET['q'] . br(2);
    if($count < 1) $r_2 .= '<div id="err">No Result Found For "'.@$_GET['q'].'"</div>';
    else {foreach($search_paged as $s) $r_2 .= br() . $s . br();
    $r_2 .= Pagination::showLinks();}
    $no_query = tag(array('div','div'),'id="err"','empty query');
    $r_1 = (array_get($_GET,'q') !== false && empty($_GET['q']))
           ? $no_query : $r_2;
    $results = tag(array('div','div'),'id="results"',br(2) . $r_1);
    $body = tag(array('body','body'),'',$body_header.$form.$results);
    
    $html = tag(array('html','html'),'',$header.$body);
    
    echo $html;
}