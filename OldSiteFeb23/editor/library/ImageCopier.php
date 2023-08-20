<?php

class ImageCopier{
    public function resize($source, $destination){
        if($source != $destination){
            copy($source, $destination);
        }
    }
}
