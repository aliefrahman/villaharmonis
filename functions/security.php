<?php
function purify_html($html) {
    if (!class_exists('HTMLPurifier')) {
        // Fallback jika HTMLPurifier belum terload/terinstall
        return strip_tags($html, '<p><br><b><i><u><strong><em><h1><h2><h3><h4><h5><h6><a><img><ul><ol><li><blockquote><figure><figcaption><span><div>');
    }
    
    $config = HTMLPurifier_Config::createDefault();
    // Konfigurasi dasar yang mengizinkan format berita standar
    $config->set('HTML.Allowed', 'p,br,b,i,u,strong,em,h1,h2,h3,h4,h5,h6,a[href|title|target],img[src|alt|width|height|class],ul,ol,li,blockquote,figure,figcaption,span[style|class],div[style|class]');
    $config->set('CSS.AllowedProperties', 'text-align,color,background-color,font-weight,font-style,text-decoration');
    $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'data' => true));
    
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}
?>
