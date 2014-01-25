<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Migrator\Converter;

class LatexConverter extends AbstractConverter
{
    protected $lastResponse;

    public function convert($content)
    {
        $reg_exercise = '@<img(?:[^>]*)(?=(?:data-mathml="([^"]*)"|alt="([^"]*)")(?:[^>]*)(?:class="Wirisformula")|(?:class="Wirisformula")(?:[^>]*)(?:data-mathml="([^"]*)"|alt="([^"]*)"))(?:[^>]*)>@is';
        preg_match_all($reg_exercise, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $replace = end($match);

            if (strlen($replace)) {
                $replace = str_replace('¨', '"', $replace);
                $replace = str_replace('«', '<', $replace);
                $replace = str_replace('»', '>', $replace);
                $replace = str_replace('§', '&', $replace);

                $url    = 'http://www.wiris.net/demo/editor/mathml2latex';
                $myvars = 'mml=' . urlencode($replace);

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $response = "%%" . curl_exec($ch) . "%%";
                $response = PHP_EOL . $response . PHP_EOL;

                if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 500){
                    $response = PHP_EOL . '**Formula contained invalid data: conversion impossible.**' . PHP_EOL;
                    $this->needsFlagging = true;
                }

                $this->flag($response);

                $content = str_replace($match[0], $response, $content);
            }
        }

        return $content;
    }

    protected function flag($response)
    {
        if (stristr('\color[rgb]', $response)) {
            $this->needsFlagging = true;
        }
    }

    protected function depr(){

        $file = '/tmp/mml.xml';

        //$replace = '<math xmlns="http://www.w3.org/1998/Math/MathML"><msup><mfrac><mn>1</mn><mn>2</mn></mfrac><mrow><mn>123</mn><mfenced><mrow><mn>12</mn><mfenced open="[" close="]"><mn>5</mn></mfenced></mrow></mfenced></mrow></msup><mn>1234</mn><mo>&#8746;</mo><mn>12</mn><mo>&#8745;</mo><mn>45123</mn><mi mathvariant="normal">&#960;</mi><mo>&#8734;</mo><mn>123</mn><mi>df</mi><mover><mn>1</mn><msubsup><mn>2</mn><mn>3</mn><mn>1</mn></msubsup></mover><mfenced open="[" close="]"><mtable><mtr><mtd><mn>4</mn></mtd></mtr><mtr><mtd><mn>5</mn></mtd></mtr></mtable></mfenced><mn>2</mn><mo>&#8709;</mo><mo>/</mo><mn>3</mn><mroot><mrow><mn>1</mn><mfrac><mn>3</mn><mn>2</mn></mfrac><mfenced open="[" close="]"><mrow><mn>123</mn><mfenced open="{" close="}"><mn>4</mn></mfenced></mrow></mfenced><mo>&#8834;</mo><mn>2</mn></mrow><mn>2</mn></mroot></math>';

        file_put_contents($file, $replace);

        ob_start();
        passthru('xsltproc /var/www/vagrant/xsltml_2.0/mmltex.xsl ' . $file);
        $response = ob_get_contents();
        ob_end_clean(); //Use this instead of ob_flush()

        var_dump($response);
        die();

        $response = trim($response);
        $response = substr($response, 1);
        $response = substr($response, 0, -1);
        $response = trim($response);
        $response = '%%' . $response . '%%';

    }
}

