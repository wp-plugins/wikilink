<?php

if (file_exists('../../../../wp-blog-header.php')) {
  require('../../../../wp-blog-header.php');
} elseif (file_exists('../../../wp-blog-header.php')) {
  require('../../../wp-blog-header.php');
} elseif (file_exists('../../wp-blog-header.php')) {
  require('../../wp-blog-header.php');
}

header("HTTP/1.1 200 OK");

include('wikipedia-functions.php');

$language = $_POST['lang'] ? $_POST['lang'] : get_option('wiki_lang');
$search   = trim($_POST['search']);


if ($search)
{

    $wiki['cache']  = get_option('wiki_cache');
    $wiki['images'] = get_option('wiki_images');

    $wiki['title']  = preg_replace('!\s+!','_',$search);

	//Buscar si existe en el cache...
	$cache='./cache/wikipedia/'.$wiki['title'].'.'.$language.'.'.$wiki['images'].'.cache';
	if (!file_exists($cache))
	{
        $wikisearch = "http://{$language}.wikipedia.org/wiki/";
		$wikilink   = "http://{$language}.wikipedia.org/w/";
		$url        = $wikilink."index.php?title=".$wiki['title'].'&printable=yes';

        $request = new WP_Http;
        $result  = $request->request( $url );
        $body    = $result['body'];

		//eliminar comentarios
		$body=preg_replace("!<\!--.*?-->!si", "", $body);

        //contenido oculto
		$body=preg_replace('!<div class="noprint".*?</div>!si', "", $body);

        //sup y aclaraciones
		$body=preg_replace('!<sup.*?</sup>!si', "", $body);

        //reemplazar enlaces
		$body=str_replace('href="/wiki/', 'href="'.$wikisearch, $body);
  
        //lupa para ver imagen
		$body=str_replace('<img src="/skins-1.5/common/images/magnify-clip.png" width="15" height="11" alt="" />', '', $body);

        //tabla de informacion
        $body=preg_replace('!<table class="metadata .*?</table>!si',"",$body);

        //tomar imagen
		if ($wiki['images'] && preg_match('!class=\"image\"(.*?)>(.*?)</a>!si', $body, $imgs))
		{
            $imgs=$imgs[2];

            //resize imagen
            preg_match('!<img(.*?) width=\"(.*?)\"!si', $imgs, $origiwidth);
            $origiwidth = $origiwidth[2];
            preg_match('!<img(.*?) height=\"(.*?)\"!si', $imgs, $origiheight);
            $origiheight = $origiheight[2];

            $newsize = imageFXResize($origiwidth,$origiheight,100);
            $imgs=preg_replace('!<img(.*?)((height|width)=\"(.*?)\")?.((height|width)=\"(.*?)\")?!si', "<img ".$newsize, $imgs);

            //adjuntarle descripcion a la imagen
            $imgs = (string) $imgs;
            $imgs = '<div class="thumbinner">'.$imgs.'</div>';
        } else {
            $imgs = '';
        }

        //eliminar scripts
        $body=preg_replace('!<script.*?</script>!si', "", $body);

        //eliminar parrafos vacios
		$body=str_replace('<p><br clear="all" /></p>', '', $body);

        //catgorias
		$body=preg_replace("!<div id=\"catlinks\".*?</div>!si", "", $body);

		//remplazar enlaces
		$body=preg_replace("!/w/!si", $wikilink, $body);

        //tabla de contenidos
        $body=preg_replace('!<table id="toc".*?</table>!si', "", $body);

        $body=preg_replace('/<div class="messagebox.*?<\/p>(.*?<\/div>)/si', "", $body);

        //tabla de informacion
        $body=preg_replace('!<table class="infobox".*?</table>!si',"",$body);


        $rta = $body;
        //$tomar solo el primer parrafo
		preg_match('!<p>.*?<\/(?:p>.*?<p|ul)>!si', $rta, $body);
        $body=$body[0];

		$body=abstractE($body,85);
        $body=$body.'<a href="'.$wikisearch.$wiki['title'].'">'.__('(more...)').'</a>';
        $body=$body.$imgs;
        
		$body=utf8_decode($body);
		
		if(!$wiki['images'])
		{
			$body=preg_replace("!<img src=[^>]*>!si", "", $body);
			$body=preg_replace("!<div class=\"magnify\".*?</div>!si", "", $body);
			$body=preg_replace("!<div class=\"thumbcaption\".*?</div>!si", "", $body);
			$body=preg_replace("!<div class=\"thumbinner\".*?</div>!si", "", $body);
			$body=preg_replace("!<div class=\"thumb tright\".*?</div>!si", "", $body);
			$body=preg_replace("!<div class=\"thumb\".*?</div>!si", "", $body);
		}

        if($wiki['cache'])
        {
            $fh = fopen($cache, 'w') or die("can't open file");
            fwrite($fh, $body);
            fclose($fh);
		}
    }
    else
    {
        $fh = fopen($cache, 'r');
        $body = fread($fh, filesize($cache));
        fclose($fh);
    }

}

if ($body != "")
{
	echo utf8_encode($body);
}
else
{
    echo "<b>OOPS, This section is down for maintinance.  Please try again later.</b>";
}
?>