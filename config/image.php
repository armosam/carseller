<?php

return [
    'max_size' => (int) env('UPLOAD_IMAGE_MAX_SIZE', 4096),
    'max_width' => (int) env('UPLOAD_IMAGE_MAX_WIDTH', 1024),
    'max_height' => (int) env('UPLOAD_IMAGE_MAX_HEIGHT', 768),
];
