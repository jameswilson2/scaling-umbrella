<?php
/**
* @package SPLIB
* @version $Id: RandomImageText.php,v 1.2 2005/09/22 05:14:24 kevin Exp $
*/
/**
* RandomImageText<br />
* Generate image text which is hard for OCR programs to
* read but can still be read by humans, for use in registration
* systems.
* @package SPLIB
* @access public
*/
class RandomImageText {
    /**
    * The background image resource
    * @access private
    * @var resource
    */
    var $image;

    /**
    * Image height in pixels
    * @access private
    * @var int
    */
    var $iHeight;

    /**
    * Image width in pixels
    * @access private
    * @var int
    */
    var $iWidth;

    /**
    * Font height in pixels
    * @access private
    * @var int
    */
    var $fHeight;

    /**
    * Font width in pixels
    * @access private
    * @var int
    */
    var $fWidth;

    /**
    * Tracks the x position in pixels
    * @access private
    * @var  int
    */
    var $xPos;

    /**
    * An array of font idenfiers
    * @access private
    * @var array
    */
    var $fonts;

    /**
    * RandomImageText constructor
    * @param string relative or full path to background jpeg
    * @param int font height to use
    * @param int font width to use
    * @access public
    */
    function RandomImageText ($jpeg,$fHeight=10,$fWidth=10) {
        $this->image=ImageCreateFromJPEG($jpeg);
        $this->iHeight = ImageSY($this->image);
        $this->iWidth = ImageSX($this->image);
        $this->fHeight=$fHeight;
        $this->fWidth=$fWidth;
        $this->xPos=0;
        $this->fonts=array(2,3,4,5);
    }

    /**
    * Add text to the image which is "randomized"
    * @param string text to add
    * @param int red hex value (0-255)
    * @param int green hex value (0-255)
    * @param int blue hex value (0-255)
    * @return boolean true text was added successfully
    * @access public
    */
    function addText ($text,$r=38,$g=38,$b=38) {
        $length = $this->fWidth * strlen($text);

        if ( $length >= ($this->iWidth-($this->fWidth*2)) ) {
            return false;
        }

        $this->xPos = floor ( ( $this->iWidth - $length ) / 2 );

        $fColor = ImageColorAllocate($this->image,$r,$g,$b);

        srand ((float)microtime()*1000000);
        $fonts=$this->fonts;
        $yStart=floor ( $this->iHeight / 2 ) - $this->fHeight;
        $yEnd=$yStart + $this->fHeight;
        $yPos=range($yStart,$yEnd);

        for ( $strPos=0;$strPos < $length; $strPos++ ) {
            shuffle($fonts);
            shuffle($yPos);
            ImageString($this->image,
                        $fonts[0],
                        $this->xPos,
                        $yPos[0],
                        substr($text,$strPos,1),
                        $fColor);
            $this->xPos+=$this->fWidth;
        }
        return true;
    }

    /**
    * Empties any fonts currently stored for use
    * @return void
    * @access public
    */
    function clearFonts () {
        return $this->fonts=array();
    }

    /**
    * Adds a new font for use in text generation
    * @param string relative or full path to font file
    * @return void
    * @access public
    */
    function addFont ($font) {
        $this->fonts[]=imageloadfont($font);
    }

    /**
    * Returns the height of the background image in
    * pixels
    * @return int
    * @access public
    */
    function getHeight () {
        return $this->iHeight;
    }

    /**
    * Returns the width of the background image in
    * pixels
    * @return int
    * @access public
    */
    function getWidth () {
        return $this->iWidth;
    }

    /**
    * Returns the image resource for use with
    * the ImageJpeg() function
    * @return resource
    * @access public
    */
    function getImage () {
        return $this->image;
    }
}
?>