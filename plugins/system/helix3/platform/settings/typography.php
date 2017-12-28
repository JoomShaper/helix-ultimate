<?php

return [
    'fieldset' => 'typography',
    'name' => 'Typography Styles',
    'class_name' => 'basic-color-style',
    'icon' => 'fa fa-font',
    'group' => [
        'global' => [ 'name' => 'Global', 'desc' => 'This is the description area for Global' ],
        'header' => [ 'name' => 'Header', 'desc' => 'This is the description area for Header' ],
        'layout' => [ 'name' => 'Layout', 'desc' => 'This is the description area for layout' ],
        'logo'   => [ 'name' => 'Logo', 'desc' => 'This is the description area for Logo' ],
        'backgroud' => [ 'name' => 'Backgroud Style', 'desc' => 'This is the description area for Backgroud Style' ],
        'footer'         => [ 'name' => 'Footer', 'desc' => 'This is the description area for Footer' ],
        'social-icon'    => [ 'name' => 'Social Icon', 'desc' => 'This is the description area for Social Icon' ],
        'contact-info'   => [ 'name' => 'Contact Info', 'desc' => 'This is the description area for Contact Info' ],
        'coming-soon'    => [ 'name' => 'Coming Soon', 'desc' => 'This is the description area for Coming Soon' ],
        'error-design'   => [ 'name' => 'Error Design', 'desc' => 'This is the description area for Error Design' ]
    ],
    'fields' => [
        'preloader' => [
            'name' => 'Preloader',
            'desc' => 'Select Preloader Style',
            'group' => 'global',
            'type' => 'select',
            'values' => [
                'circle' => 'Circle',
                'double-loop' => 'Double Loop',
                'wave-two' => 'Wave Two',
                'audio-wave' => 'Audio Wave'
            ],
            'std' => ''
        ],
        'favicon' => [
            'name' => 'Upload Favicon',
            'desc' => 'Upload Favicon Here',
            'group' => 'global',
            'type' => 'image',
            'std' => ''
        ],
        'logo' => [
            'name' => 'Upload Logo',
            'desc' => 'Upload Logo Here',
            'group' => 'header',
            'type' => 'image',
            'std' => ''
        ],
        'mobile-logo' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'group' => 'header',
            'type' => 'image',
            'std' => ''
        ],
        'bg-chn' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'group' => 'backgroud',
            'type' => 'select',
            'values' => [
                'color' => 'Color',
                'image' => 'Image'
            ],
            'std' => ''
        ],
        'bg-image' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'group' => 'backgroud',
            'type' => 'image',
            'std' => ''
        ],
        'bg-color' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'group' => 'backgroud',
            'type' => 'color',
            'std' => ''
        ]
    ]
];