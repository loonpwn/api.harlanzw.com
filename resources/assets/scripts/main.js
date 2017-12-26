// import external dependencies
import 'jquery';
// Import everything from autoload
import 'bootstrap';
import 'daemonite-material';
// make header nicer
import 'headroom.js';
import 'headroom.js/dist/jQuery.headroom.js';
// lazy loading
import 'lazysizes/plugins/unveilhooks/ls.unveilhooks';
import 'lazysizes/plugins/object-fit/ls.object-fit';
import 'lazysizes/plugins/bgset/ls.bgset';
import 'lazysizes/plugins/respimg/ls.respimg'

// import local dependencies
import Components from './util/components';
import common from './components/common';

const components = new Components([
    common,
]);


components.fire('init', $);
/** Load Events */
jQuery(document).ready(($) => {
    components.fire('ready', $);
});
jQuery(window).on('load', ($) => components.fire('loaded', $));

