<?php

namespace A3DMV;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class EnableMimeType {
    private $mime_types = [
        'gltf'  => 'model/gltf+json',
        'glb'  => 'model/gltf+binary'
    ];

    public function enable() {
        add_filter('upload_mimes', [$this, 'add_mime_types']);
        add_filter('wp_check_filetype_and_ext', [$this, 'check_filetype'], 10, 5);
    }

    public function add_mime_types($mime_types) {
        return array_merge($mime_types, $this->mime_types);
    }

    public function check_filetype($data, $file, $filename, $mimes, $real_mime = null) {
        if (!empty($data['ext']) && !empty($data['type'])) {
            return $data;
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!array_key_exists($ext, $this->mime_types)) {
            return $data;
        }

        // Validate file content via magic bytes before trusting the extension.
        // GLB files start with the 4-byte magic 0x676C5446 ("glTF").
        // GLTF files are JSON and must start with a '{' character (0x7B).
        if ('glb' === $ext) {
            $handle = fopen($file, 'rb');
            if (false === $handle) {
                return $data;
            }
            $magic = fread($handle, 4);
            fclose($handle);
            if ($magic !== 'glTF') {
                return $data;
            }
        } elseif ('gltf' === $ext) {
            $handle = fopen($file, 'rb');
            if (false === $handle) {
                return $data;
            }
            $first_byte = fread($handle, 1);
            fclose($handle);
            if ($first_byte !== '{') {
                return $data;
            }
        }

        $data['ext']  = $ext;
        $data['type'] = $this->mime_types[$ext];

        return $data;
    }
}