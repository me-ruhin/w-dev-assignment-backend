<?php

if (!function_exists('uploadImage')) {

    function uploadImage($path, $image)
    {
        try {
            $imageExt = $image->getClientOriginalExtension();
            $imageNewName = "product" . time() . "." . $imageExt;
            $image->storeAs($path, $imageNewName, 'public');
            return $imageNewName;
            //code...
        } catch (\Exception $e) {

            return  $e->getMessage();
        }
    }
}
