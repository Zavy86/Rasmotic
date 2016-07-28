<?php
//=======================================================================
// File:        jpgraph_ttf.inc.php
// Description: Handling of TTF fonts
// Created:     2006-11-19
// Ver:         $Id: jpgraph_ttf.inc.php 1858 2009-09-28 14:39:51Z ljp $
//
// Copyright (c) Asial Corporation. All rights reserved.
//========================================================================

// TTF Font families
define("FF_ARIAL",15);
define("FF_COURIER",10);
define("FF_TIMES",12);
define("FF_VERDANA",11);
define("FF_USERFONT",90);
define("FF_USERFONT1",90);
define("FF_USERFONT2",91);
define("FF_USERFONT3",92);

// Limits for fonts
define("_FIRST_FONT",10);
define("_LAST_FONT",99);

// TTF Font styles
define("FS_NORMAL",9001);
define("FS_BOLD",9002);
define("FS_ITALIC",9003);
define("FS_BOLDIT",9004);
define("FS_BOLDITALIC",9004);

//Definitions for internal font
define("FF_FONT0",1);
define("FF_FONT1",2);
define("FF_FONT2",4);

//------------------------------------------------------------------------
// Defines for font setup
//------------------------------------------------------------------------

// Actual name of the TTF file used together with FF_CHINESE aka FF_BIG5
// This is the TTF file being used when the font family is specified as
// either FF_CHINESE or FF_BIG5
define('CHINESE_TTF_FONT','bkai00mp.ttf');

// Special unicode greek language support
define("LANGUAGE_GREEK",false);

// If you are setting this config to true the conversion of greek characters
// will assume that the input text is windows 1251
define("GREEK_FROM_WINDOWS",false);

// Special unicode cyrillic language support
define("LANGUAGE_CYRILLIC",false);

// If you are setting this config to true the conversion
// will assume that the input text is windows 1251, if
// false it will assume koi8-r
define("CYRILLIC_FROM_WINDOWS",false);

// The following constant is used to auto-detect
// whether cyrillic conversion is really necessary
// if enabled. Just replace 'windows-1251' with a variable
// containing the input character encoding string
// of your application calling jpgraph.
// A typical such string would be 'UTF-8' or 'utf-8'.
// The comparison is case-insensitive.
// If this charset is not a 'koi8-r' or 'windows-1251'
// derivate then no conversion is done.
//
// This constant can be very important in multi-user
// multi-language environments where a cyrillic conversion
// could be needed for some cyrillic people
// and resulting in just erraneous conversions
// for not-cyrillic language based people.
//
// Example: In the free project management
// software dotproject.net $locale_char_set is dynamically
// set by the language environment the user has chosen.
//
// Usage: define('LANGUAGE_CHARSET', $locale_char_set);
//
// where $locale_char_set is a GLOBAL (string) variable
// from the application including JpGraph.
//
define('LANGUAGE_CHARSET', null);

// Japanese TrueType font used with FF_MINCHO, FF_PMINCHO, FF_GOTHIC, FF_PGOTHIC
// Standard fonts from Infomation-technology Promotion Agency (IPA)
// See http://mix-mplus-ipa.sourceforge.jp/
define('MINCHO_TTF_FONT','ipam.ttf');
define('PMINCHO_TTF_FONT','ipamp.ttf');
define('GOTHIC_TTF_FONT','ipag.ttf');
define('PGOTHIC_TTF_FONT','ipagp.ttf');

// Assume that Japanese text have been entered in EUC-JP encoding.
// If this define is true then conversion from EUC-JP to UTF8 is done
// automatically in the library using the mbstring module in PHP.
define('ASSUME_EUCJP_ENCODING',false);


// Default font family
define('FF_DEFAULT', FF_VERDANA);



//=================================================================
// CLASS LanguageConv
// Description:
// Converts various character encoding into proper
// UTF-8 depending on how the library have been configured and
// what font family is being used
//=================================================================
class LanguageConv {
    private $g2312 = null ;

    function Convert($aTxt,$aFF) {
        if( LANGUAGE_GREEK ) {
            if( GREEK_FROM_WINDOWS ) {
                $unistring = LanguageConv::gr_win2uni($aTxt);
            } else  {
                $unistring = LanguageConv::gr_iso2uni($aTxt);
            }
            return $unistring;
        } elseif( LANGUAGE_CYRILLIC ) {
            if( CYRILLIC_FROM_WINDOWS && (!defined('LANGUAGE_CHARSET') || stristr(LANGUAGE_CHARSET, 'windows-1251')) ) {
                $aTxt = convert_cyr_string($aTxt, "w", "k");
            }
            if( !defined('LANGUAGE_CHARSET') || stristr(LANGUAGE_CHARSET, 'koi8-r') || stristr(LANGUAGE_CHARSET, 'windows-1251')) {
                $isostring = convert_cyr_string($aTxt, "k", "i");
                $unistring = LanguageConv::iso2uni($isostring);
            }
            else {
                $unistring = $aTxt;
            }
            return $unistring;
        }
        elseif( $aFF === FF_SIMSUN ) {
            // Do Chinese conversion
            if( $this->g2312 == null ) {
                include_once 'jpgraph_gb2312.php' ;
                $this->g2312 = new GB2312toUTF8();
            }
            return $this->g2312->gb2utf8($aTxt);
        }
        elseif( $aFF === FF_BIG5 ) {
            if( !function_exists('iconv') ) {
                JpGraphError::RaiseL(25006);
                //('Usage of FF_CHINESE (FF_BIG5) font family requires that your PHP setup has the iconv() function. By default this is not compiled into PHP (needs the "--width-iconv" when configured).');
            }
            return iconv('BIG5','UTF-8',$aTxt);
        }
        elseif( ASSUME_EUCJP_ENCODING &&
        ($aFF == FF_MINCHO || $aFF == FF_GOTHIC || $aFF == FF_PMINCHO || $aFF == FF_PGOTHIC) ) {
            if( !function_exists('mb_convert_encoding') ) {
                JpGraphError::RaiseL(25127);
            }
            return mb_convert_encoding($aTxt, 'UTF-8','EUC-JP');
        }
        elseif( $aFF == FF_DAVID || $aFF == FF_MIRIAM || $aFF == FF_AHRON ) {
            return LanguageConv::heb_iso2uni($aTxt);
        }
        else
        return $aTxt;
    }

    // Translate iso encoding to unicode
    public static function iso2uni ($isoline){
        $uniline='';
        for ($i=0; $i < strlen($isoline); $i++){
            $thischar=substr($isoline,$i,1);
            $charcode=ord($thischar);
            $uniline.=($charcode>175) ? "&#" . (1040+($charcode-176)). ";" : $thischar;
        }
        return $uniline;
    }

    // Translate greek iso encoding to unicode
    public static function gr_iso2uni ($isoline) {
        $uniline='';
        for ($i=0; $i < strlen($isoline); $i++) {
            $thischar=substr($isoline,$i,1);
            $charcode=ord($thischar);
            $uniline.=($charcode>179 && $charcode!=183 && $charcode!=187 && $charcode!=189) ? "&#" . (900+($charcode-180)). ";" : $thischar;
        }
        return $uniline;
    }

    // Translate greek win encoding to unicode
    public static function gr_win2uni ($winline) {
        $uniline='';
        for ($i=0; $i < strlen($winline); $i++) {
            $thischar=substr($winline,$i,1);
            $charcode=ord($thischar);
            if ($charcode==161 || $charcode==162) {
                $uniline.="&#" . (740+$charcode). ";";
            }
            else {
                $uniline.=(($charcode>183 && $charcode!=187 && $charcode!=189) || $charcode==180) ? "&#" . (900+($charcode-180)). ";" : $thischar;
            }
        }
        return $uniline;
    }

    public static function heb_iso2uni($isoline) {
        $isoline = hebrev($isoline);
        $o = '';

        $n = strlen($isoline);
        for($i=0; $i < $n; $i++) {
            $c=ord( substr($isoline,$i,1) );
            $o .= ($c > 223) && ($c < 251) ? '&#'.(1264+$c).';' : chr($c);
        }
        return utf8_encode($o);
    }
}

//=============================================================
// CLASS TTF
// Description: Handle TTF font names and mapping and loading of
//              font files
//=============================================================
class TTF {
    private $font_files,$style_names;

    function __construct() {

	    // String names for font styles to be used in error messages
	    $this->style_names=array(
	    	FS_NORMAL =>'normal',
	    	FS_BOLD =>'bold',
	    	FS_ITALIC =>'italic',
	    	FS_BOLDITALIC =>'bolditalic');

	    // File names for available fonts
	    $this->font_files=array(

      /* Standard fonts */
      FF_ARIAL=>array(FS_NORMAL=>'arial.ttf',
       FS_BOLD=>'arialbd.ttf',
       FS_ITALIC=>'ariali.ttf',
       FS_BOLDITALIC=>'arialbi.ttf'),

      FF_COURIER=>array(FS_NORMAL=>'cour.ttf',
       FS_BOLD=>'courbd.ttf',
       FS_ITALIC=>'couri.ttf',
       FS_BOLDITALIC=>'courbi.ttf'),

      FF_TIMES=>array(FS_NORMAL=>'times.ttf',
       FS_BOLD=>'timesbd.ttf',
       FS_ITALIC=>'timesi.ttf',
       FS_BOLDITALIC=>'timesbi.ttf'),

      FF_VERDANA=>array(FS_NORMAL=>'verdana.ttf',
       FS_BOLD=>'verdanab.ttf',
       FS_ITALIC=>'verdanai.ttf',
       FS_BOLDITALIC=>'verdanaz.ttf'),

      /* Placeholders for defined fonts */
      FF_USERFONT1=>array(
       FS_NORMAL=>'',
       FS_BOLD=>'',
       FS_ITALIC=>'',
       FS_BOLDITALIC=>''),

      FF_USERFONT2=>array(
       FS_NORMAL=>'',
       FS_BOLD=>'',
       FS_ITALIC=>'',
       FS_BOLDITALIC=>''),

      FF_USERFONT3=>array(
       FS_NORMAL=>'',
       FS_BOLD=>'',
       FS_ITALIC=>'',
       FS_BOLDITALIC=>'')

	    );
    }

    //---------------
    // PUBLIC METHODS
    // Create the TTF file from the font specification
    function File($family,$style=FS_NORMAL) {
        $fam = @$this->font_files[$family];
        if( !$fam ) {
            JpGraphError::RaiseL(25046,$family);//("Specified TTF font family (id=$family) is unknown or does not exist. Please note that TTF fonts are not distributed with JpGraph for copyright reasons. You can find the MS TTF WEB-fonts (arial, courier etc) for download at http://corefonts.sourceforge.net/");
        }
        $ff = @$fam[$style];

        // There are several optional file names. They are tried in order
        // and the first one found is used
        if( !is_array($ff) ) {
            $ff = array($ff);
        }

        $jpgraph_font_dir = dirname(__FILE__).'/fonts/';

        foreach ($ff as $font_file) {
            // All font families are guaranteed to have the normal style

            if( $font_file==='' )
                    JpGraphError::RaiseL(25047,$this->style_names[$style],$this->font_files[$family][FS_NORMAL]);//('Style "'.$this->style_names[$style].'" is not available for font family '.$this->font_files[$family][FS_NORMAL].'.');
            if( !$font_file ) {
                JpGraphError::RaiseL(25048,$fam);//("Unknown font style specification [$fam].");
            }

            // check jpgraph/src/fonts dir
            $jpgraph_font_file = $jpgraph_font_dir . $font_file;
            if (file_exists($jpgraph_font_file) === true && is_readable($jpgraph_font_file) === true) {
                $font_file = $jpgraph_font_file;
                break;
            }

            // check OS font dir
            if ($family >= FF_MINCHO && $family <= FF_PGOTHIC) {
                $font_file = MBTTF_DIR.$font_file;
            } else {
                $font_file = TTF_DIR.$font_file;
            }
            if (file_exists($font_file) === true && is_readable($font_file) === true) {
                break;
            }
        }

        if( !file_exists($font_file) ) {
        	JpGraphError::RaiseL(25049,$font_file);//("Font file \"$font_file\" is not readable or does not exist.");
        }

        return $font_file;
    }

    function SetUserFont($aNormal,$aBold='',$aItalic='',$aBoldIt='') {
        $this->font_files[FF_USERFONT] =
        	array(FS_NORMAL     => $aNormal,
        		  FS_BOLD => $aBold,
        		  FS_ITALIC => $aItalic,
        		  FS_BOLDITALIC => $aBoldIt ) ;
    }

    function SetUserFont1($aNormal,$aBold='',$aItalic='',$aBoldIt='') {
        $this->font_files[FF_USERFONT1] =
        	array(FS_NORMAL     => $aNormal,
        		  FS_BOLD => $aBold,
        		  FS_ITALIC => $aItalic,
        		  FS_BOLDITALIC => $aBoldIt ) ;
    }

    function SetUserFont2($aNormal,$aBold='',$aItalic='',$aBoldIt='') {
        $this->font_files[FF_USERFONT2] =
        	array(FS_NORMAL     => $aNormal,
        		  FS_BOLD => $aBold,
        		  FS_ITALIC => $aItalic,
        		  FS_BOLDITALIC => $aBoldIt ) ;
    }

    function SetUserFont3($aNormal,$aBold='',$aItalic='',$aBoldIt='') {
        $this->font_files[FF_USERFONT3] =
        	array(FS_NORMAL     => $aNormal,
        		  FS_BOLD => $aBold,
        		  FS_ITALIC => $aItalic,
        		  FS_BOLDITALIC => $aBoldIt ) ;
    }

} // Class


//=============================================================================
// CLASS SymChar
// Description: Code values for some commonly used characters that
//              normally isn't available directly on the keyboard, for example
//              mathematical and greek symbols.
//=============================================================================
class  SymChar {
    static function Get($aSymb,$aCapital=FALSE) {
        $iSymbols = array(
        /* Greek */
        array('alpha','03B1','0391'),
        array('beta','03B2','0392'),
        array('gamma','03B3','0393'),
        array('delta','03B4','0394'),
        array('epsilon','03B5','0395'),
        array('zeta','03B6','0396'),
        array('ny','03B7','0397'),
        array('eta','03B8','0398'),
        array('theta','03B8','0398'),
        array('iota','03B9','0399'),
        array('kappa','03BA','039A'),
        array('lambda','03BB','039B'),
        array('mu','03BC','039C'),
        array('nu','03BD','039D'),
        array('xi','03BE','039E'),
        array('omicron','03BF','039F'),
        array('pi','03C0','03A0'),
        array('rho','03C1','03A1'),
        array('sigma','03C3','03A3'),
        array('tau','03C4','03A4'),
        array('upsilon','03C5','03A5'),
        array('phi','03C6','03A6'),
        array('chi','03C7','03A7'),
        array('psi','03C8','03A8'),
        array('omega','03C9','03A9'),
        /* Money */
        array('euro','20AC'),
        array('yen','00A5'),
        array('pound','20A4'),
        /* Math */
        array('approx','2248'),
        array('neq','2260'),
        array('not','2310'),
        array('def','2261'),
        array('inf','221E'),
        array('sqrt','221A'),
        array('int','222B'),
        /* Misc */
        array('copy','00A9'),
        array('para','00A7'),
        array('tm','2122'),   /* Trademark symbol */
        array('rtm','00AE'),   /* Registered trademark */
        array('degree','00b0'),
        array('lte','2264'), /* Less than or equal */
        array('gte','2265'), /* Greater than or equal */

        );

        $n = count($iSymbols);
        $i=0;
        $found = false;
        $aSymb = strtolower($aSymb);
        while( $i < $n && !$found ) {
            $found = $aSymb === $iSymbols[$i++][0];
        }
        if( $found ) {
            $ca = $iSymbols[--$i];
            if( $aCapital && count($ca)==3 )
                $s = $ca[2];
            else
                $s = $ca[1];
            return sprintf('&#%04d;',hexdec($s));
        }
        else
            return '';
    }
}


?>
