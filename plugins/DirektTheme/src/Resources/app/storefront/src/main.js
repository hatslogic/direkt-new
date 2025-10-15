// search toggle
import './plugins/search-toggle.js';
// header sticky
import './plugins/header-sticky.js';
// show more
import './plugins/showmore.js';
// video js
import './plugins/video-play.js';
// filter js
import './plugins/filter-drop.js';

const PluginManager = window.PluginManager;
// import PluginManager from 'src/plugin/forms/form-cms-handler.plugin.js';
// import ExtendedFormCmsHandler from './plugins/newsletter-alert.js';

PluginManager.override('FormCmsHandler', () => import('./plugins/newsletter-alert.js'), '.cms-element-form form');
